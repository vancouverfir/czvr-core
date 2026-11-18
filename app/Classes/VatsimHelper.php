<?php

namespace App\Classes;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VatsimHelper
{
    private const DEFAULT_DATAFEED_URL = 'https://data.vatsim.net/v3/vatsim-data.json';

    public static function getDatafeed(): ?array
    {
        $key = 'vatsim.datafeed.snapshot';

        try {
            return Cache::lock($key.'.lock', 60)->block(20, function () use ($key): ?array {
                $snapshot = Cache::get($key);
                if ($snapshot !== null) {
                    return $snapshot['feed'];
                }

                try {
                    $response = HttpHelper::getClient()->retry(2, 250, throw: false)->get(self::getDatafeedUrl());
                    $feed = $response->successful() ? $response->json() : null;
                } catch (ConnectionException $exception) {
                    Log::warning('VATSIM datafeed connection failed.', ['message' => $exception->getMessage()]);
                    $feed = null;
                }

                if (! is_array($feed) || ! is_array($feed['controllers'] ?? null)) {
                    Log::warning('VATSIM datafeed did not contain a valid controllers list.');
                    $feed = null;
                }

                Cache::put($key, ['feed' => $feed], 50);

                return $feed;
            });
        } catch (LockTimeoutException) {
            return null;
        }
    }

    public static function getDatafeedUrl(): string
    {
        return Cache::remember('vatsim-datafeed-url', 300, function () {
            try {
                $response = HttpHelper::getClient()
                    ->retry(2, 250, throw: false)
                    ->get('https://status.vatsim.net/status.json');

                if (! $response->successful()) {
                    return self::DEFAULT_DATAFEED_URL;
                }

                $url = $response->json('data.v3.0');

                if (! is_string($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
                    return self::DEFAULT_DATAFEED_URL;
                }

                $host = strtolower((string) parse_url($url, PHP_URL_HOST));

                return parse_url($url, PHP_URL_SCHEME) === 'https'
                    && ($host === 'vatsim.net' || Str::endsWith($host, '.vatsim.net'))
                        ? $url
                        : self::DEFAULT_DATAFEED_URL;
            } catch (\Throwable) {
                return self::DEFAULT_DATAFEED_URL;
            }
        });
    }
}
