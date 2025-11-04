<?php

namespace App\Services;

use App\Models\ContentEvent;
use App\Models\ContentTourist;
use App\Models\ContentBusStop;
use App\Models\DynamicContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ContentCacheService
{
    // Cache TTL constants for different content types
    public const EVENT_CACHE_TTL = 3600; // 1 hour - events change frequently
    public const TOURIST_CACHE_TTL = 7200; // 2 hours - tourist info is more static
    public const BUS_STOP_CACHE_TTL = 1800; // 30 minutes - transport info changes regularly
    public const CONTENT_LIST_CACHE_TTL = 600; // 10 minutes - for content listings

    // Cache key prefixes
    private const CACHE_PREFIX = 'content_';
    private const EVENT_PREFIX = 'event_';
    private const TOURIST_PREFIX = 'tourist_';
    private const BUS_STOP_PREFIX = 'bus_stop_';
    private const LIST_PREFIX = 'list_';

    /**
     * Get cached event content with related data
     */
    public static function getEventContent(int $eventId): ?array
    {
        $cacheKey = self::CACHE_PREFIX . self::EVENT_PREFIX . $eventId;

        return Cache::remember($cacheKey, self::EVENT_CACHE_TTL, function () use ($eventId) {
            $event = ContentEvent::with(['dynamicContent.nfcToken', 'dynamicContent.multimedia'])
                ->find($eventId);

            if (!$event) {
                return null;
            }

            return [
                'content' => $event,
                'is_active' => $event->isActive(),
                'duration_hours' => $event->getDurationInHours(),
                'requires_registration' => $event->requiresRegistration(),
                'cached_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get cached tourist content with location data
     */
    public static function getTouristContent(int $touristId): ?array
    {
        $cacheKey = self::CACHE_PREFIX . self::TOURIST_PREFIX . $touristId;

        return Cache::remember($cacheKey, self::TOURIST_CACHE_TTL, function () use ($touristId) {
            $tourist = ContentTourist::with([
                'dynamicContent.nfcToken',
                'dynamicContent.multimedia',
                'nearbySpots'
            ])->find($touristId);

            if (!$tourist) {
                return null;
            }

            return [
                'content' => $tourist,
                'has_coordinates' => $tourist->latitude && $tourist->longitude,
                'nearby_spots_count' => $tourist->nearbySpots->count(),
                'cached_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get cached bus stop content with routes and schedules
     */
    public static function getBusStopContent(int $busStopId): ?array
    {
        $cacheKey = self::CACHE_PREFIX . self::BUS_STOP_PREFIX . $busStopId;

        return Cache::remember($cacheKey, self::BUS_STOP_CACHE_TTL, function () use ($busStopId) {
            $busStop = ContentBusStop::with([
                'dynamicContent.nfcToken',
                'routes.schedules',
                'utilityPhones'
            ])->find($busStopId);

            if (!$busStop) {
                return null;
            }

            return [
                'content' => $busStop,
                'active_routes_count' => $busStop->routes->count(),
                'has_real_time_data' => $busStop->routes()->exists(),
                'utility_phones_count' => $busStop->utilityPhones->count(),
                'cached_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get cached content list by type for a user
     */
    public static function getUserContentByType(int $userId, string $contentType): array
    {
        $cacheKey = self::CACHE_PREFIX . self::LIST_PREFIX . $userId . '_' . strtolower($contentType);

        return Cache::remember($cacheKey, self::CONTENT_LIST_CACHE_TTL, function () use ($userId, $contentType) {
            $contents = DynamicContent::where('user_id', $userId)
                ->where('type', strtoupper($contentType))
                ->where('is_active', true)
                ->with(['nfcToken'])
                ->orderBy('updated_at', 'desc')
                ->get();

            return [
                'contents' => $contents,
                'count' => $contents->count(),
                'content_type' => $contentType,
                'cached_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get cached public content by location (for tourist and bus stop content)
     */
    public static function getPublicContentByLocation(float $latitude, float $longitude, float $radius = 5.0): array
    {
        $cacheKey = self::CACHE_PREFIX . 'location_' . md5("{$latitude}_{$longitude}_{$radius}");

        return Cache::remember($cacheKey, self::TOURIST_CACHE_TTL, function () use ($latitude, $longitude, $radius) {
            // Get tourist content within radius
            $touristContent = ContentTourist::whereHas('dynamicContent', function ($query) {
                $query->where('status', 'published')->where('is_active', true);
            })
            ->whereRaw("
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) + sin(radians(?)) 
                * sin(radians(latitude)))) <= ?
            ", [$latitude, $longitude, $latitude, $radius])
            ->with('dynamicContent.nfcToken')
            ->limit(20)
            ->get();

            // Get bus stops within radius
            $busStops = ContentBusStop::whereHas('dynamicContent', function ($query) {
                $query->where('status', 'published')->where('is_active', true);
            })
            ->whereRaw("
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) + sin(radians(?)) 
                * sin(radians(latitude)))) <= ?
            ", [$latitude, $longitude, $latitude, $radius])
            ->with(['dynamicContent.nfcToken', 'routes'])
            ->limit(10)
            ->get();

            return [
                'tourist_spots' => $touristContent,
                'bus_stops' => $busStops,
                'location' => compact('latitude', 'longitude', 'radius'),
                'cached_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Invalidate cache for specific content
     */
    public static function invalidateContentCache(string $contentType, int $contentId): void
    {
        $prefix = match (strtoupper($contentType)) {
            'EVENT' => self::EVENT_PREFIX,
            'TOURIST' => self::TOURIST_PREFIX,
            'BUS_STOP' => self::BUS_STOP_PREFIX,
            default => '',
        };

        if ($prefix) {
            $cacheKey = self::CACHE_PREFIX . $prefix . $contentId;
            Cache::forget($cacheKey);
            
            Log::info("Cache invalidated for {$contentType} content", [
                'content_type' => $contentType,
                'content_id' => $contentId,
                'cache_key' => $cacheKey
            ]);
        }
    }

    /**
     * Invalidate user's content list cache
     */
    public static function invalidateUserContentListCache(int $userId, ?string $contentType = null): void
    {
        if ($contentType) {
            $cacheKey = self::CACHE_PREFIX . self::LIST_PREFIX . $userId . '_' . strtolower($contentType);
            Cache::forget($cacheKey);
        } else {
            // Invalidate all content types for user
            $contentTypes = ['event', 'tourist', 'bus_stop', 'gift', 'profile', 'business'];
            foreach ($contentTypes as $type) {
                $cacheKey = self::CACHE_PREFIX . self::LIST_PREFIX . $userId . '_' . $type;
                Cache::forget($cacheKey);
            }
        }

        Log::info("User content list cache invalidated", [
            'user_id' => $userId,
            'content_type' => $contentType ?? 'all'
        ]);
    }

    /**
     * Invalidate location-based cache
     */
    public static function invalidateLocationCache(float $latitude, float $longitude, float $radius = 5.0): void
    {
        $cacheKey = self::CACHE_PREFIX . 'location_' . md5("{$latitude}_{$longitude}_{$radius}");
        Cache::forget($cacheKey);

        Log::info("Location cache invalidated", compact('latitude', 'longitude', 'radius'));
    }

    /**
     * Clear all content cache
     */
    public static function clearAllContentCache(): void
    {
        $pattern = self::CACHE_PREFIX . '*';
        
        // Note: This is a simple implementation. In production with Redis,
        // you might want to use SCAN with pattern matching
        Cache::flush(); // Clears all cache - use with caution
        
        Log::info("All content cache cleared");
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        // This is a simplified version. In production, you'd want to track
        // hit rates, miss rates, and other metrics
        return [
            'cache_driver' => config('cache.default'),
            'ttl_settings' => [
                'event_cache_ttl' => self::EVENT_CACHE_TTL,
                'tourist_cache_ttl' => self::TOURIST_CACHE_TTL,
                'bus_stop_cache_ttl' => self::BUS_STOP_CACHE_TTL,
                'list_cache_ttl' => self::CONTENT_LIST_CACHE_TTL,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Warm up cache for frequently accessed content
     */
    public static function warmUpCache(string $contentType, array $contentIds): void
    {
        Log::info("Starting cache warm-up", [
            'content_type' => $contentType,
            'content_count' => count($contentIds)
        ]);

        foreach ($contentIds as $contentId) {
            match (strtoupper($contentType)) {
                'EVENT' => self::getEventContent($contentId),
                'TOURIST' => self::getTouristContent($contentId),
                'BUS_STOP' => self::getBusStopContent($contentId),
                default => null,
            };
        }

        Log::info("Cache warm-up completed", [
            'content_type' => $contentType,
            'content_count' => count($contentIds)
        ]);
    }
}