<?php

namespace App\Application\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

class CacheHelper
{
    private static function getTenantId(): ?int
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return $user->tenant_id ?? null;
    }

    public static function tenantKey(string $key): string
    {
        $tenantId = self::getTenantId();
        return $tenantId ? "tenant:{$tenantId}:{$key}" : $key;
    }

    public static function remember(string $key, int $ttl = 3600, callable $callback)
    {
        $fullKey = self::tenantKey($key);

        return Cache::remember($fullKey, $ttl, $callback);
    }

    public static function rememberForever(string $key, callable $callback)
    {
        $fullKey = self::tenantKey($key);

        return Cache::rememberForever($fullKey, $callback);
    }

    public static function forget(string $key): bool
    {
        $fullKey = self::tenantKey($key);

        return Cache::forget($fullKey);
    }

    public static function flushTenant(): void
    {
        $tenantId = self::getTenantId();
        if ($tenantId) {
            $pattern = "tenant:{$tenantId}:*";

            if (config('cache.default') === 'redis') {
                $redis = Redis::connection(config('cache.stores.redis.connection'));
                $keys = $redis->keys($pattern);

                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }
        }
    }

    public static function flushAllTenants(): void
    {
        if (config('cache.default') === 'redis') {
            $redis = Redis::connection(config('cache.stores.redis.connection'));
            $keys = $redis->keys('tenant:*');

            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }

    public static function invalidateProducts(): void
    {
        $tenantId = self::getTenantId();
        if ($tenantId && config('cache.default') === 'redis') {
            $redis = Redis::connection(config('cache.stores.redis.connection'));
            $pattern = "tenant:{$tenantId}:products:*";
            $keys = $redis->keys($pattern);

            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }

    public static function invalidateCustomers(): void
    {
        $tenantId = self::getTenantId();
        if ($tenantId && config('cache.default') === 'redis') {
            $redis = Redis::connection(config('cache.stores.redis.connection'));
            $pattern = "tenant:{$tenantId}:customers:*";
            $keys = $redis->keys($pattern);

            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }

    public static function invalidateSales(): void
    {
        $tenantId = self::getTenantId();
        if ($tenantId && config('cache.default') === 'redis') {
            $redis = Redis::connection(config('cache.stores.redis.connection'));
            $pattern = "tenant:{$tenantId}:sales:*";
            $keys = $redis->keys($pattern);

            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }

    public static function invalidateDashboard(): void
    {
        $tenantId = self::getTenantId();
        if ($tenantId && config('cache.default') === 'redis') {
            $redis = Redis::connection(config('cache.stores.redis.connection'));
            $pattern = "tenant:{$tenantId}:dashboard:stats";
            $keys = $redis->keys($pattern);

            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }
}
