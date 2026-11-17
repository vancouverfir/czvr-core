<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomepageImages extends Model
{
    protected $fillable = [
        'url', 'credit',
    ];

    public function browserUrl(): string
    {
        $url = trim((string) $this->url);
        $path = parse_url($url, PHP_URL_PATH);

        if (is_string($path) && Str::startsWith($path, '/storage/')) {
            return $path;
        }

        if (Str::startsWith($url, 'storage/')) {
            return '/'.$url;
        }

        return $url;
    }

    public function isAvailable(): bool
    {
        $url = $this->browserUrl();

        if ($url === '') {
            return false;
        }

        if (Str::startsWith($url, '/storage/')) {
            return Storage::disk('public')->exists(Str::after($url, '/storage/'));
        }

        if (Str::startsWith($url, '/')) {
            return is_file(public_path(ltrim($url, '/')));
        }

        return Str::startsWith($url, ['http://', 'https://']);
    }
}
