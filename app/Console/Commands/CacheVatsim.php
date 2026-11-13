<?php

namespace App\Console\Commands;

use App\Classes\HttpHelper;
use App\Classes\VatsimHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CacheVatsim extends Command
{
    private const AIRPORTS = ['CYVR', 'CYYJ', 'CYLW', 'CYXS', 'CYXX', 'CYQQ'];

    private const LIVE_MAP_AIRPORTS = [
        'CYVR' => [49.1947, -123.1792],
        'CYYJ' => [48.6469, -123.4258],
        'CYLW' => [49.9561, -119.3778],
    ];

    private const LIVE_MAP_RADIUS_KM = 9.26;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:cachevatsim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache VATSIM Data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $response = HttpHelper::getClient()
                ->retry(2, 250, throw: false)
                ->get(VatsimHelper::getDatafeedUrl());

            if (! $response->successful()) {
                Log::warning('VATSIM controller cache request failed!', ['status' => $response->status()]);

                return self::FAILURE;
            }

            $controllers = $response->json('controllers', []);
            $atisStations = $response->json('atis', []);
            $pilots = $response->json('pilots', []);

            $finalPositions = [];
            $prefixes = ['CZVR_', 'ZVR_', 'CYVR_', 'CYYJ_', 'CYLW_', 'CYXS_', 'CYXX_', 'CYCD_'];

            if (is_array($controllers)) {
                foreach ($controllers as $c) {
                    if (
                        is_array($c) &&
                        isset($c['callsign'], $c['facility']) &&
                        Str::startsWith($c['callsign'], $prefixes) &&
                        ! Str::endsWith($c['callsign'], ['ATIS', 'OBS']) &&
                        $c['facility'] != 0
                    ) {
                        $finalPositions[] = $c;
                    }
                }
            }

            Cache::put('vatsim.controllers', $finalPositions, 300); // 5 min

            $airportAtis = [];

            if (is_array($atisStations)) {
                foreach ($atisStations as $station) {
                    if (! is_array($station)) {
                        continue;
                    }

                    $callsign = (string) ($station['callsign'] ?? '');

                    foreach (self::AIRPORTS as $icao) {
                        if (isset($airportAtis[$icao]) || ! Str::startsWith($callsign, $icao)) {
                            continue;
                        }

                        $text = $station['text_atis'] ?? null;
                        $airportAtis[$icao] = [
                            'letter' => $station['atis_code'] ?? null,
                            'text' => is_array($text) ? implode(' ', $text) : $text,
                        ];
                    }
                }
            }

            // Keep the last successful result available if an upstream refresh
            // later fails or the scheduler is briefly unavailable
            Cache::forever('vatsim.atis', $airportAtis);

            $nearbyPilots = array_fill_keys(array_keys(self::LIVE_MAP_AIRPORTS), []);
            $airportFlights = array_fill_keys(array_keys(self::LIVE_MAP_AIRPORTS), []);

            if (is_array($pilots)) {
                foreach ($pilots as $pilot) {
                    if (! is_array($pilot)) {
                        continue;
                    }

                    $flight = $this->mapPilot($pilot);

                    if ($flight === null) {
                        continue;
                    }

                    foreach (self::LIVE_MAP_AIRPORTS as $icao => $coordinates) {
                        if ($flight['departure'] === $icao || $flight['arrival'] === $icao) {
                            $airportFlights[$icao][] = $flight;
                        }

                        if ($this->distanceFromAirport($flight['latitude'], $flight['longitude'], $coordinates) <= self::LIVE_MAP_RADIUS_KM) {
                            $nearbyPilots[$icao][] = $flight;
                        }
                    }
                }
            }

            $updatedAt = now()->toIso8601String();

            foreach (array_keys(self::LIVE_MAP_AIRPORTS) as $icao) {
                $cachePrefix = 'vatsim.'.strtolower($icao);
                Cache::forever($cachePrefix.'.pilots', $nearbyPilots[$icao]);
                Cache::forever($cachePrefix.'.flights', $airportFlights[$icao]);
                Cache::forever($cachePrefix.'.updated_at', $updatedAt);
            }
            $this->info('VATSIM controllers cached successfully!');
        } catch (\Throwable $e) {
            Log::error('Failed to cache VATSIM controllers!', ['message' => $e->getMessage()]);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function mapPilot(array $pilot): ?array
    {
        if (! is_numeric($pilot['latitude'] ?? null) || ! is_numeric($pilot['longitude'] ?? null)) {
            return null;
        }

        return [
            'cid' => $pilot['cid'] ?? null,
            'callsign' => (string) ($pilot['callsign'] ?? ''),
            'name' => (string) ($pilot['name'] ?? ''),
            'latitude' => (float) $pilot['latitude'],
            'longitude' => (float) $pilot['longitude'],
            'altitude' => (int) ($pilot['altitude'] ?? 0),
            'groundspeed' => (int) ($pilot['groundspeed'] ?? 0),
            'heading' => (int) ($pilot['heading'] ?? 0),
            'aircraft' => (string) ($pilot['flight_plan']['aircraft_short'] ?? $pilot['flight_plan']['aircraft_faa'] ?? ''),
            'departure' => strtoupper((string) ($pilot['flight_plan']['departure'] ?? '')),
            'arrival' => strtoupper((string) ($pilot['flight_plan']['arrival'] ?? '')),
            'planned_altitude' => (string) ($pilot['flight_plan']['altitude'] ?? ''),
        ];
    }

    private function distanceFromAirport(float $latitude, float $longitude, array $coordinates): float
    {
        [$airportLatitude, $airportLongitude] = $coordinates;
        $latitudeDelta = deg2rad($latitude - $airportLatitude);
        $longitudeDelta = deg2rad($longitude - $airportLongitude);
        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($airportLatitude)) * cos(deg2rad($latitude))
            * sin($longitudeDelta / 2) ** 2;

        return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
