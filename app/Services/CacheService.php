<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function get($key)
    {
        return Cache::get($key);
    }

    public function put($key, $value, $minutes): void
    {
        Cache::put($key, $value, $minutes);
    }

    public function has($key): bool
    {
        return Cache::has($key);
    }
}
