<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CacheBooking extends Command
{
    protected $signature = 'vancouver:cache-bookings';
    protected $description = 'Cache ATC Bookings Data';

    public function handle(): int
    {
        try {
            $apiKey = env('BOOKING_API_KEY');
            $bookingUrl = 'https://atc-bookings.vatsim.net/api/booking';

            $response = Http::withToken($apiKey)->get($bookingUrl, ['key_only' => true]);

            if ($response->failed()) {
                $this->error('Failed to fetch bookings: ' . $response->body());
                return 1;
            }

            $bookings = collect($response->json());
            $now = now()->utc();

            $expired = $bookings->filter(
                fn($b) => \Carbon\Carbon::parse($b['end'], 'UTC')->lt($now)
            );

            foreach ($expired as $b) {
                Http::withToken($apiKey)->delete("{$bookingUrl}/{$b['id']}");
            }

            if ($expired->isNotEmpty()) {
                $response = Http::withToken($apiKey)->get($bookingUrl, ['key_only' => true]);
                $bookings = $response->successful() ? collect($response->json()) : collect();
            }

            Cache::put('bookings.data', $bookings, 900);

            $this->info("Bookings cached successfully ({$bookings->count()} bookings).");
        } catch (\Exception $e) {
            \Log::error('Failed to cache bookings: ' . $e->getMessage());
            $this->error('Exception: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
