<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AirportController extends Controller
{
    private const AIRPORTS = ['CYVR', 'CYYJ', 'CYLW', 'CYXS', 'CYXX', 'CYQQ'];

    public function index(): View
    {
        $atis = Cache::get('vatsim.atis', []);
        $weather = Cache::get('weather.data', []);
        $airportWeather = [];

        foreach (self::AIRPORTS as $icao) {
            $atisLetter = data_get($atis, "{$icao}.letter");
            $atisText = data_get($atis, "{$icao}.text");
            $metar = data_get($weather, "{$icao}.raw_text")
                ?? data_get($weather, "{$icao}.raw");

            $airportWeather[$icao] = [
                'atis_letter' => is_string($atisLetter) && $atisLetter !== '' ? $atisLetter : null,
                'text' => is_string($atisText) && $atisText !== ''
                    ? $atisText
                    : (is_string($metar) && $metar !== '' ? $metar : 'Weather data is temporarily unavailable!'),
            ];
        }

        return view('airports', compact('airportWeather'));
    }
}
