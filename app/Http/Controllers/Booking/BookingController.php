<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Events\Event;
use App\Models\Users\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    protected $bookingUrl = 'https://atc-bookings.vatsim.net/api/booking';

    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.booking.key');
    }

    protected function client(): PendingRequest
    {
        return Http::withToken((string) $this->apiKey)
            ->acceptJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->retry(2, 250, throw: false);
    }

    protected function getCachedBookings(): Collection
    {
        $bookings = Cache::remember('bookings.data', 300, function (): array {
            try {
                $response = $this->client()->get($this->bookingUrl, ['key_only' => true]);

                if (! $response->successful()) {
                    Log::warning('Booking list request failed!', ['status' => $response->status()]);

                    return [];
                }

                $payload = $response->json();

                return is_array($payload) ? $payload : [];
            } catch (ConnectionException $e) {
                Log::warning('Booking list request could not connect!', ['message' => $e->getMessage()]);

                return [];
            }
        });

        return collect(is_array($bookings) ? $bookings : []);
    }

    protected function refreshBookingsCache(): void
    {
        Cache::forget('bookings.data');
    }

    protected function remoteBooking($id): ?array
    {
        if (! ctype_digit((string) $id)) {
            return null;
        }

        try {
            $response = $this->client()->get("{$this->bookingUrl}/{$id}");
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();

        if (is_array($payload['data'] ?? null)) {
            $payload = $payload['data'];
        }

        return is_array($payload) ? $payload : null;
    }

    protected function assertCanModify(array $booking): void
    {
        $user = auth()->user();
        $owned = (string) ($booking['cid'] ?? '') === (string) $user->id;

        abort_unless(($owned && $user->isController()) || $user->can('bookings.manage'), 403, 'You may only modify your own bookings!');
    }

    protected function bookingOverlapsWestCoastWeekend(CarbonImmutable $start, CarbonImmutable $end): bool
    {
        return Event::query()
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%west coast weekend%'])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ['%west-coast-weekend%']);
            })
            ->where('start_timestamp', '<', $end->format('Y-m-d H:i:s'))
            ->where('end_timestamp', '>', $start->format('Y-m-d H:i:s'))
            ->exists();
    }

    protected function userCertifiedForCallsign(string $callsign, ?int $cid = null): bool
    {
        [$icao, $position] = array_pad(explode('_', $callsign, 2), 2, null);
        if (! $icao || ! $position) {
            return false;
        }

        $mapping = config("bookingairports.certifiedairports.{$icao}");
        $roster = RosterMember::where('cid', $cid ?? auth()->id())->first();

        if (! is_array($mapping) || ! $roster) {
            return false;
        }

        foreach ($mapping['positions'] ?? [] as $certifiedPosition) {
            if (! Str::contains($position, $certifiedPosition)) {
                continue;
            }

            foreach ($mapping['columns'] ?? [] as $column) {
                if ((int) $roster->{$column} > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function validateBookingRules(CarbonImmutable $start, CarbonImmutable $end): ?array
    {
        if ($end->lessThanOrEqualTo($start)) {
            return [
                'end' => 'Booking end time must be after the start time!',
            ];
        }

        $durationMinutes = $start->diffInMinutes($end);

        if ($durationMinutes < 45) {
            return [
                'duration' => 'Your booking cannot be less than 45 minutes!',
            ];
        }

        if ($start->lessThan(CarbonImmutable::now('UTC')->addHours(4))) {
            return [
                'start' => 'Bookings must be created at least 4 hours in advance!',
            ];
        }

        $isWestCoastWeekend = $this->bookingOverlapsWestCoastWeekend($start, $end);
        $maxDurationMinutes = $isWestCoastWeekend ? 120 : 240;

        if ($durationMinutes > $maxDurationMinutes) {
            return [
                'duration' => $isWestCoastWeekend
                    ? 'Reserved bookings during West Coast Weekends cannot be longer than 2 hours!'
                    : 'Reserved bookings cannot be longer than 4 hours!',
            ];
        }

        return null;
    }

    public function indexPublic(Request $request): View
    {
        $events = Event::all();
        $airports = config('bookingairports.airports');

        $callsigns = [];
        foreach ($airports as $prefix => $suffixes) {
            foreach ($suffixes as $s) {
                $callsigns[] = "{$prefix}_{$s}";
            }
        }

        $bookings = $this->getCachedBookings();

        return view('booking.index', ['bookings' => $bookings, 'callsigns' => $callsigns, 'events' => $events]);
    }

    public function create(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'callsign' => ['required', 'string', 'max:32'],
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $start = CarbonImmutable::parse($data['start'], 'UTC');
        $end = CarbonImmutable::parse($data['end'], 'UTC');

        $bookingRuleErrors = $this->validateBookingRules($start, $end);

        if ($bookingRuleErrors) {
            return back()->withErrors($bookingRuleErrors)->withInput();
        }

        $user = auth()->user();

        if (! $this->userCertifiedForCallsign($data['callsign'])) {
            return back()->withErrors(['certification' => 'You are not certified for this position!'])->withInput();
        }

        $apiData = [
            'callsign' => $data['callsign'],
            'cid' => $user->id,
            'type' => 'booking',
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s'),
            'division' => $user->division_code ?? null,
            'subdivision' => $user->subdivision_code ?? null,
        ];

        $response = $this->client()->post($this->bookingUrl, $apiData);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => 'The booking service rejected the request!'])->with('error', 'Failed to create booking! Please try again!');
        }

        $this->refreshBookingsCache();

        return redirect()->route('booking')->with('success', 'Booking created successfully!');
    }

    public function edit($id, Request $request): Response|JsonResponse|View
    {
        $booking = $this->remoteBooking($id);
        abort_if(! $booking, 404);

        $this->assertCanModify($booking);

        if ($request->ajax()) {
            return response()->json($booking);
        }

        return view('booking.edit', compact('booking'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $data = $request->validate([
            'callsign' => ['required', 'string', 'max:32'],
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $start = CarbonImmutable::parse($data['start'], 'UTC');
        $end = CarbonImmutable::parse($data['end'], 'UTC');

        $bookingRuleErrors = $this->validateBookingRules($start, $end);

        if ($bookingRuleErrors) {
            return back()->withErrors($bookingRuleErrors)->withInput();
        }

        $booking = $this->remoteBooking($id);

        if (! $booking) {
            return back()->withErrors(['api_error' => 'The booking could not be found!']);
        }

        $this->assertCanModify($booking);

        $user = User::find($booking['cid']) ?? auth()->user();

        if (! $this->userCertifiedForCallsign($data['callsign'], $user->id)) {
            return back()->withErrors(['certification' => 'The controller is not certified for this position!'])->withInput();
        }
        $apiData = [
            'callsign' => $data['callsign'],
            'cid' => $user->id,
            'type' => 'booking',
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s'),
            'division' => $user->division_code ?? null,
            'subdivision' => $user->subdivision_code ?? null,
        ];

        $response = $this->client()->put("{$this->bookingUrl}/{$id}", $apiData);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => 'The booking service rejected the request!'])->with('error', 'Failed to update booking! Please try again!');
        }

        $this->refreshBookingsCache();

        return redirect()->route('booking')->with('success', 'Booking updated successfully!');
    }

    public function delete($id): RedirectResponse
    {
        $booking = $this->remoteBooking($id);

        if (! $booking) {
            return back()->withErrors(['api_error' => 'The booking could not be found!']);
        }

        $this->assertCanModify($booking);

        $response = $this->client()->delete("{$this->bookingUrl}/{$id}");

        if ($response->failed()) {
            return back()->withErrors(['api_error' => 'The booking service rejected the request!'])->with('error', 'Failed to delete booking! Please try again!');
        }

        $this->refreshBookingsCache();

        return redirect()->route('booking')->with('success', 'Booking deleted successfully!');
    }
}
