<?php

namespace App\Classes;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WeatherHelper
{
    /**
     * Gets ATIS Letter for Vancouver Airports Page.
     *
     * @param  $icao
     * @return string|null
     */
    public static function getAtisLetter($icao)
    {
        $atis = Cache::remember('vancouver.atis', 300, function () {
            $client = new Client();
            $response = $client->request('GET', 'https://data.vatsim.net/v3/vatsim-data.json');
            return json_decode($response->getBody()->getContents())->atis ?? [];
        });

        foreach ($atis as $a) {
            if (Str::startsWith($a->callsign, $icao)) {
                return $a->atis_code ?? null;
            }
        }

        return null;
    }

    /**
     * Gets ATIS Letter for Vancouver Airports Page.
     *
     * @param  $icao
     * @return string
     */
    public static function getAtis($icao)
    {
        $atis = Cache::remember('vancouver.atis', 300, function () {
            $client = new Client();
            $response = $client->request('GET', 'https://data.vatsim.net/v3/vatsim-data.json');
            return json_decode($response->getBody()->getContents())->atis ?? [];
        });

        foreach ($atis as $a) {
            if (Str::startsWith($a->callsign, $icao) && !empty($a->text_atis)) {
                return implode(' ', $a->text_atis);
            }
        }

        return Cache::remember('metar.data.'.$icao, 900, function () use ($icao) {
            $c = new Client();
            try {
                $res = $c->request('GET', 'https://api.checkwx.com/metar/'.$icao, [
                    'headers' => [
                        'X-API-Key' => env('AIRPORT_API_KEY'),
                    ],
                ]);

                $metar = json_decode($res->getBody()->getContents())->data ?? null;
                return $metar[0] ?? 'No Weather Data!';
            } catch (\Exception $e) {
                return 'No Weather Data!';
            }
        });
    }
}
