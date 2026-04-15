<?php

namespace App\Services;

use App\Models\ContentEvent;
use App\Models\DynamicContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ContentCacheService
{
    // Cache TTL constants for different content types
    public const EVENT_CACHE_TTL = 3600; // 1 hour - events change frequently
    public const CONTENT_LIST_CACHE_TTL = 600; // 10 minutes - for content listings

    // Cache key prefixes
    private const CACHE_PREFIX = 'content_';
    private const EVENT_PREFIX = 'event_';
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
     * Invalidate cache for specific content
     */
    public static function invalidateContentCache(string $contentType, int $contentId): void
    {
        $prefix = match (strtoupper($contentType)) {
            'EVENT' => self::EVENT_PREFIX,
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
            $contentTypes = ['event', 'gift', 'profile', 'business'];
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
                default => null,
            };
        }

        Log::info("Cache warm-up completed", [
            'content_type' => $contentType,
            'content_count' => count($contentIds)
        ]);
    }
}
