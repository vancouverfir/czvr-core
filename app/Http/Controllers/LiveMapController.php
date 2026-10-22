<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LiveMapController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        $airport = strtoupper((string) $request->query('airport', 'CYVR'));

        if (! in_array($airport, ['CYVR', 'CYYJ', 'CYLW'], true)) {
            abort(404);
        }

        $cachePrefix = 'vatsim.'.strtolower($airport);

        return response()->json([
            'pilots' => Cache::get($cachePrefix.'.pilots', []),
            'flights' => Cache::get($cachePrefix.'.flights', []),
            'updated_at' => Cache::get($cachePrefix.'.updated_at'),
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
