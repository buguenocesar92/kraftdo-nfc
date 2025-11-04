<?php

namespace App\Observers;

use App\Models\ContentTourist;
use App\Services\ContentCacheService;
use Illuminate\Support\Facades\Log;

class ContentTouristObserver
{
    /**
     * Handle the ContentTourist "created" event.
     */
    public function created(ContentTourist $contentTourist): void
    {
        $this->invalidateTouristCache($contentTourist, 'created');
    }

    /**
     * Handle the ContentTourist "updated" event.
     */
    public function updated(ContentTourist $contentTourist): void
    {
        $this->invalidateTouristCache($contentTourist, 'updated');
        
        // If location changed, invalidate location-based cache
        if ($contentTourist->wasChanged(['latitude', 'longitude'])) {
            $this->invalidateLocationCache($contentTourist);
        }
    }

    /**
     * Handle the ContentTourist "deleted" event.
     */
    public function deleted(ContentTourist $contentTourist): void
    {
        $this->invalidateTouristCache($contentTourist, 'deleted');
        $this->invalidateLocationCache($contentTourist);
    }

    /**
     * Handle the ContentTourist "restored" event.
     */
    public function restored(ContentTourist $contentTourist): void
    {
        $this->invalidateTouristCache($contentTourist, 'restored');
        $this->invalidateLocationCache($contentTourist);
    }

    /**
     * Handle the ContentTourist "force deleted" event.
     */
    public function forceDeleted(ContentTourist $contentTourist): void
    {
        $this->invalidateTouristCache($contentTourist, 'force_deleted');
        $this->invalidateLocationCache($contentTourist);
    }

    /**
     * Invalidate tourist-specific cache
     */
    private function invalidateTouristCache(ContentTourist $contentTourist, string $action): void
    {
        // Invalidate specific tourist content cache
        ContentCacheService::invalidateContentCache('TOURIST', $contentTourist->id);

        // Invalidate user's tourist content list cache
        if ($contentTourist->dynamicContent && $contentTourist->dynamicContent->user_id) {
            ContentCacheService::invalidateUserContentListCache(
                $contentTourist->dynamicContent->user_id, 
                'tourist'
            );
        }

        Log::info("Tourist cache invalidated", [
            'tourist_id' => $contentTourist->id,
            'action' => $action,
            'location_name' => $contentTourist->location_name,
            'coordinates' => [
                'latitude' => $contentTourist->latitude,
                'longitude' => $contentTourist->longitude,
            ],
        ]);
    }

    /**
     * Invalidate location-based cache for areas around this tourist spot
     */
    private function invalidateLocationCache(ContentTourist $contentTourist): void
    {
        if ($contentTourist->latitude && $contentTourist->longitude) {
            // Invalidate cache for different radius sizes around this location
            $radiuses = [5.0, 10.0, 20.0]; // km
            
            foreach ($radiuses as $radius) {
                ContentCacheService::invalidateLocationCache(
                    $contentTourist->latitude,
                    $contentTourist->longitude,
                    $radius
                );
            }

            Log::info("Location-based cache invalidated for tourist spot", [
                'tourist_id' => $contentTourist->id,
                'latitude' => $contentTourist->latitude,
                'longitude' => $contentTourist->longitude,
                'radiuses_cleared' => $radiuses,
            ]);
        }
    }
}