<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class VatcanFacilities
{
    /** VATCAN facility IDs */
    public const NAMES = [
        0 => 'Academy',
        1 => 'Edmonton FIR',
        2 => 'Gander Oceanic',
        3 => 'Moncton/Gander FIR',
        4 => 'Montreal FIR',
        5 => 'Toronto FIR',
        6 => 'Vancouver FIR',
        7 => 'Winnipeg FIR',
    ];

    /** CID → home facility ID, refreshed by vancouver:sync-students */
    public const HOME_FACILITY_CACHE_KEY = 'vatcan.home_facilities';

    public static function name(?int $facilityId): ?string
    {
        return $facilityId === null ? null : (self::NAMES[$facilityId] ?? null);
    }

    public static function homeFacilityName(int $cid): ?string
    {
        return self::name(Cache::get(self::HOME_FACILITY_CACHE_KEY, [])[$cid] ?? null);
    }
}
