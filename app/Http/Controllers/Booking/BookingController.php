<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Events\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    protected function getCachedBookings(): \Illuminate\Support\Collection
    {
        return Cache::remember('bookings.data', 300, function () {
            $response = Http::withToken($this->apiKey)->get($this->bookingUrl, ['key_only' => true]);

            return $response->successful() ? collect($response->json()) : collect();
        });
    }

    protected function refreshBookingsCache(): void
    {
        $response = Http::withToken($this->apiKey)->get($this->bookingUrl, ['key_only' => true]);
        $bookings = $response->successful() ? collect($response->json()) : collect();
        Cache::put('bookings.data', $bookings, 300);
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
            'start' => 'required',
            'end' => 'required',
        ]);

        $start = strtotime($data['start']);
        $end = strtotime($data['end']);
        $durationMinutes = ($end - $start) / 60;

        if ($durationMinutes < 45 || $durationMinutes > 300) {
            return back()->withErrors([
                'duration' => 'Booking must be at least 45 minutes and no more than 5 hours!',
            ])->withInput();
        }

        $user = auth()->user();

        $apiData = [
            'callsign' => $data['callsign'],
            'cid' => $user->id,
            'type' => 'booking',
            'start' => date('Y-m-d H:i:s', strtotime($data['start'])),
            'end' => date('Y-m-d H:i:s', strtotime($data['end'])),
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
            'start' => 'required',
            'end' => 'required',
        ]);

        $start = strtotime($data['start']);
        $end = strtotime($data['end']);
        $durationMinutes = ($end - $start) / 60;

        if ($durationMinutes < 45 || $durationMinutes > 180) {
            return back()->withErrors([
                'duration' => 'Booking must be at least 45 minutes and no more than 3 hours!',
            ])->withInput();
        }

        $user = auth()->user();

        $apiData = [
            'callsign' => $data['callsign'],
            'cid' => $user->id,
            'type' => 'booking',
            'start' => date('Y-m-d H:i:s', strtotime($data['start'])),
            'end' => date('Y-m-d H:i:s', strtotime($data['end'])),
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
