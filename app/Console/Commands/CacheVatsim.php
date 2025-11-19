<?php

namespace App\Console\Commands;

use App\Classes\VatsimHelper;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CacheVatsim extends Command
{
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
    public function handle()
    {
        try {
            $client = new Client();
            $response = $client->request('GET', VatsimHelper::getDatafeedUrl());
            $controllers = json_decode($response->getBody()->getContents());

            $finalPositions = [];
            $prefixes = ['CZVR_', 'ZVR_', 'CYVR_', 'CYYJ_', 'CYLW_', 'CYXS_', 'CYXX_', 'CYCD_'];

            if (isset($controllers->controllers)) {
                foreach ($controllers->controllers as $c) {
                    if (
                        isset($c->callsign, $c->facility) &&
                        Str::startsWith($c->callsign, $prefixes) &&
                        ! Str::endsWith($c->callsign, ['ATIS', 'OBS']) &&
                        $c->facility != 0
                    ) {
                        $finalPositions[] = $c;
                    }
                }
            }

            Cache::put('vatsim.controllers', $finalPositions, 300); // 5 min
            $this->info('VATSIM controllers cached successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to cache VATSIM controllers: '.$e->getMessage());
        }
    }
}
