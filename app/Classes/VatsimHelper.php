<?php

namespace App\Classes;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VatsimHelper
{
    public static function getDatafeedUrl(): string
    {
        return Cache::remember('vatsim-datafeed-url', 86400, function () {
            try {
                $response = HttpHelper::getClient()->get('https://status.vatsim.net/status.json');

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['data']['v3'][0])) {
                        return $data['data']['v3'][0];
                    } else {
                        Log::warning('VATSIM status.json: "data.v3[0]" key missing.', ['response' => $data]);
                        throw new \Exception('Invalid response structure from VATSIM status.json.');
                    }
                } else {
                    Log::error('Failed to fetch VATSIM status.json.', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    throw new \Exception('Failed to fetch VATSIM status.json.');
                }
            } catch (\Exception $e) {
                Log::error('Error fetching VATSIM datafeed URL: '.$e->getMessage());
                throw $e;
            }
        });
    }
}
