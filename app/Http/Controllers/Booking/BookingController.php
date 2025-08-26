<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingUrl = 'https://atc-bookings.vatsim.net/api/booking';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('BOOKING_API_KEY');
    }

    public function indexPublic(Request $request)
    {
        $query = ['key_only' => true];

        $response = Http::withToken($this->apiKey)->get($this->bookingUrl, $query);
        $bookings = $response->successful() ? collect($response->json()) : collect();

        return view('booking', [
            'bookings' => $bookings,
            'upcomingBookings' => $bookings,
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'callsign' => 'required|string',
            'type'     => 'nullable|string',
            'start'    => 'required',
            'end'      => 'required',
        ]);

        $user = auth()->user();

        $apiData = [
            'callsign'    => $data['callsign'],
            'cid'         => $user->id,
            'type'        => $data['type'] ?? 'booking',
            'start'       => gmdate('Y-m-d H:i:s', strtotime($data['start'])),
            'end'         => gmdate('Y-m-d H:i:s', strtotime($data['end'])),
            'division'    => $user->division_code ?? null,
            'subdivision' => $user->subdivision_code ?? null,
        ];

        $response = Http::withToken($this->apiKey)->post($this->bookingUrl, $apiData);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])
                         ->with('error', 'Failed to create booking! Please try again!');
        }

        return redirect()->route('booking')
                         ->with('success', 'Booking created successfully!');
    }

    public function edit($id, Request $request)
    {
        $response = Http::withToken($this->apiKey)->get("{$this->bookingUrl}/{$id}");
        $booking = $response->successful() ? $response->json() : null;

        if ($request->ajax()) {
            return response()->json($booking);
        }

        return view('booking.edit', compact('booking'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'callsign' => 'required|string',
            'type'     => 'nullable|string',
            'start'    => 'required',
            'end'      => 'required',
        ]);

        $user = auth()->user();

        $apiData = [
            'callsign'    => $data['callsign'],
            'cid'         => $user->id,
            'type'        => $data['type'] ?? 'booking',
            'start'       => gmdate('Y-m-d H:i:s', strtotime($data['start'])),
            'end'         => gmdate('Y-m-d H:i:s', strtotime($data['end'])),
            'division'    => $user->division_code ?? null,
            'subdivision' => $user->subdivision_code ?? null,
        ];

        $response = Http::withToken($this->apiKey)->put("{$this->bookingUrl}/{$id}", $apiData);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])
                         ->with('error', 'Failed to update booking! Please try again!');
        }

        return redirect()->route('booking')
                         ->with('success', 'Booking updated successfully!');
    }

    public function delete($id)
    {
        $response = Http::withToken($this->apiKey)->delete("{$this->bookingUrl}/{$id}");

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])
                         ->with('error', 'Failed to delete booking! Please try again!');
        }

        return redirect()->route('booking')
                         ->with('success', 'Booking deleted successfully!');
    }
}
