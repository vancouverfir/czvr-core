<?php

namespace App\Classes;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class HttpHelper
{
    public static function getClient(): PendingRequest
    {
        return Http::withHeaders(['User-Agent' => 'czvr.ca'])->connectTimeout(5);
    }

}