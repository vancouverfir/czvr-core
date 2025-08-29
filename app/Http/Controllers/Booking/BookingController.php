<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

        $airports = [
            'CZVR' => ['CTR', 'A_CTR', 'N_CTR', 'S_CTR', 'E_CTR', 'W_CTR', 'X_CTR', 'H_CTR', 'Z_CTR', 'K_CTR', 'Y_CTR', 'C_CTR', 'F_CTR'],
            'CYVR' => ['APP', 'X_APP', 'L_APP', 'T_APP', 'DEP', 'N_DEP', 'TWR', 'N_TWR', 'O_TWR', 'N_GND', 'GND', 'A_GND', 'DEL'],
            'CYQQ' => ['APP', 'DEP', 'GND', 'TWR'],
            'CYYJ' => ['APP', 'DEP', 'TWR', 'O_TWR', 'GND', 'DEL'],
            'CYXX' => ['TWR', 'GND'],
            'CYPK' => ['TWR', 'GND'],
            'CYNJ' => ['TWR', 'GND'],
            'CZBB' => ['TWR', 'O_TWR', 'GND'],
            'CYLW' => ['APP', 'TWR', 'GND'],
            'CYXS' => ['TWR', 'GND'],
            'CYHC' => ['TWR', 'DEL'],
            'CYZT' => ['F_TWR'],
            'CYCD' => ['F_TWR', 'F_GND'],
            'CYCG' => ['F_TWR'],
            'CYBL' => ['F_TWR'],
            'CYKA' => ['F_TWR', 'DEL'],
            'CYWL' => ['F_TWR'],
            'CYXC' => ['F_TWR'],
            'CYXT' => ['F_TWR'],
            'CYYD' => ['F_TWR'],
            'CYYF' => ['F_TWR', 'F_GND'],
            'CYWH' => ['F_TWR'],
        ];

        $callsigns = [];
        foreach ($airports as $prefix => $suffixes) {
            foreach ($suffixes as $s) {
                $callsigns[] = "{$prefix}_{$s}";
            }
        }

        $response = Http::withToken($this->apiKey)->get($this->bookingUrl, $query);
        $bookings = $response->successful() ? collect($response->json()) : collect();

        return view('booking', ['bookings' => $bookings, 'callsigns' => $callsigns]);
    }

    public function create(Request $request)
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
            'start' => gmdate('Y-m-d H:i:s', strtotime($data['start'])),
            'end' => gmdate('Y-m-d H:i:s', strtotime($data['end'])),
            'division' => $user->division_code ?? null,
            'subdivision' => $user->subdivision_code ?? null,
        ];

        $response = Http::withToken($this->apiKey)->post($this->bookingUrl, $apiData);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])->with('error', 'Failed to create booking! Please try again!');
        }

        return redirect()->route('booking')->with('success', 'Booking created successfully!');
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
            'start' => gmdate('Y-m-d H:i:s', strtotime($data['start'])),
            'end' => gmdate('Y-m-d H:i:s', strtotime($data['end'])),
            'division' => $user->division_code ?? null,
            'subdivision' => $user->subdivision_code ?? null,
        ];

        $response = Http::withToken($this->apiKey)->put("{$this->bookingUrl}/{$id}", $apiData);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])->with('error', 'Failed to update booking! Please try again!');
        }

        return redirect()->route('booking')->with('success', 'Booking updated successfully!');
    }

    public function delete($id)
    {
        $response = Http::withToken($this->apiKey)->delete("{$this->bookingUrl}/{$id}");

        if ($response->failed()) {
            return back()->withErrors(['api_error' => $response->body()])->with('error', 'Failed to delete booking! Please try again!');
        }

        return redirect()->route('booking')->with('success', 'Booking deleted successfully!');
    }
}
