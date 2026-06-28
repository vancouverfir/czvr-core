<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Events\Event;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class BookingController extends Controller
{
    protected $bookingUrl = 'https://atc-bookings.vatsim.net/api/booking';

    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('BOOKING_API_KEY');
    }

    protected function getCachedBookings(): Collection
    {
        return Cache::remember('bookings.data', 300, function () {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout(10)
                    ->get($this->bookingUrl, ['key_only' => true]);

                return $response->successful() ? collect($response->json()) : collect();
            } catch (ConnectionException $e) {
                return collect();
            }
        });
    }

    protected function refreshBookingsCache(): void
    {
        Cache::forget('bookings.data');
        $this->getCachedBookings();
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

        return view('booking', ['bookings' => $bookings, 'callsigns' => $callsigns, 'events' => $events]);
    }

    public function create(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'callsign' => 'required|string',
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

        $apiData = [
            'callsign' => $data['callsign'],
            'cid' => $user->id,
            'type' => 'booking',
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s'),
            'division' => $user->division_code ?? null,
            'subdivision' => $user->subdivision_code ?? null,
        ];

        $response = Http::withToken($this->apiKey)->post($this->bookingUrl, $apiData);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])->with('error', 'Failed to create booking! Please try again!');
        }

        $this->refreshBookingsCache();

        return redirect()->route('booking')->with('success', 'Booking created successfully!');
    }

    public function edit($id, Request $request): Response|JsonResponse|View
    {
        $response = Http::withToken($this->apiKey)->get("{$this->bookingUrl}/{$id}");
        $booking = $response->successful() ? $response->json() : null;

        if ($request->ajax()) {
            return response()->json($booking);
        }

        return view('booking.edit', compact('booking'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $data = $request->validate([
            'callsign' => 'required|string',
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

        $apiData = [
            'callsign' => $data['callsign'],
            'cid' => $user->id,
            'type' => 'booking',
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s'),
            'division' => $user->division_code ?? null,
            'subdivision' => $user->subdivision_code ?? null,
        ];

        $response = Http::withToken($this->apiKey)->put("{$this->bookingUrl}/{$id}", $apiData);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])->with('error', 'Failed to update booking! Please try again!');
        }

        $this->refreshBookingsCache();

        return redirect()->route('booking')->with('success', 'Booking updated successfully!');
    }

    public function delete($id): RedirectResponse
    {
        $response = Http::withToken($this->apiKey)->delete("{$this->bookingUrl}/{$id}");

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])->with('error', 'Failed to delete booking! Please try again!');
        }

        $this->refreshBookingsCache();

        return redirect()->route('booking')->with('success', 'Booking deleted successfully!');
    }
}
