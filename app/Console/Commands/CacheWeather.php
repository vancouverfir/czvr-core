<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CacheWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vancouver:cacheweather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache Weather Data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $apiKey = config('services.checkwx.key');
        if (! is_string($apiKey) || $apiKey === '') {
            $this->error('AIRPORT_API_KEY is not configured!');

            return self::FAILURE;
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders(['X-API-Key' => $apiKey])
                ->connectTimeout(5)
                ->timeout(20)
                ->retry(2, 250, throw: false)
                ->get('https://api.checkwx.com/metar/CYVR,CYYJ,CYLW,CYXS,CYXX,CYQQ/decoded');

            if (! $response->successful()) {
                Log::warning('CheckWX weather cache request failed!', ['status' => $response->status()]);

                return self::FAILURE;
            }

            $weatherData = $response->json('data', []);

            $weatherArray = [];

            if (is_array($weatherData)) {
                foreach ($weatherData as $weather) {
                    if (! is_array($weather)) {
                        continue;
                    }

                    $icao = $weather['icao'] ?? 'UNKNOWN';
                    $weather['flight_category'] ??= 'N/A';
                    $weather['temperature'] ??= null;
                    $weather['wind'] ??= null;

                    $weatherArray[$icao] = $weather;
                }
            }

            // The scheduler refreshes this every 15 minutes. Keeping the last
            // successful payload avoids blocking web requests during outages
            Cache::forever('weather.data', $weatherArray);
            $this->info('Weather cached successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to cache weather!', ['message' => $e->getMessage()]);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
