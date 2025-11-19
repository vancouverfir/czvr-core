<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
    public function handle()
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.checkwx.com/metar/CYVR,CYYJ,CYLW,CYXS,CYXX,CYQQ/decoded?pretty=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-Key: '.env('AIRPORT_API_KEY')]);

            $resp = json_decode(curl_exec($ch));
            curl_close($ch);

            $weatherArray = [];

            if (!empty($resp->data)) {
                foreach ($resp->data as $w) {
                    $icao = $w->icao ?? 'UNKNOWN';
                    $w->flight_category = $w->flight_category ?? 'N/A';
                    $w->temperature = $w->temperature ?? null;
                    $w->wind = $w->wind ?? null;

                    $weatherArray[$icao] = $w;
                }
            }

            Cache::put('weather.data', $weatherArray, 900); // 15 min
            $this->info('Weather cached successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to cache weather: '.$e->getMessage());
        }
    }
}
