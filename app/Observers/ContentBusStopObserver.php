<?php

namespace App\Observers;

use App\Models\BusStop;
use App\Services\ContentCacheService;
use Illuminate\Support\Facades\Log;

class BusStopObserver
{
    /**
     * Handle the BusStop "created" event.
     */
    public function created(BusStop $contentBusStop): void
    {
        $this->invalidateBusStopCache($contentBusStop, 'created');
    }

    /**
     * Handle the BusStop "updated" event.
     */
    public function updated(BusStop $contentBusStop): void
    {
        $this->invalidateBusStopCache($contentBusStop, 'updated');
        
        // If location changed, invalidate location-based cache
        if ($contentBusStop->wasChanged(['latitude', 'longitude'])) {
            $this->invalidateLocationCache($contentBusStop);
        }
    }

    /**
     * Handle the BusStop "deleted" event.
     */
    public function deleted(BusStop $contentBusStop): void
    {
        $this->invalidateBusStopCache($contentBusStop, 'deleted');
        $this->invalidateLocationCache($contentBusStop);
    }

    /**
     * Handle the BusStop "restored" event.
     */
    public function restored(BusStop $contentBusStop): void
    {
        $this->invalidateBusStopCache($contentBusStop, 'restored');
        $this->invalidateLocationCache($contentBusStop);
    }

    /**
     * Handle the BusStop "force deleted" event.
     */
    public function forceDeleted(BusStop $contentBusStop): void
    {
        $this->invalidateBusStopCache($contentBusStop, 'force_deleted');
        $this->invalidateLocationCache($contentBusStop);
    }

    /**
     * Invalidate bus stop specific cache
     */
    private function invalidateBusStopCache(BusStop $contentBusStop, string $action): void
    {
        // Invalidate specific bus stop cache
        ContentCacheService::invalidateContentCache('BUS_STOP', $contentBusStop->id);

        // Invalidate user's bus stop list cache
        if ($contentBusStop->dynamicContent && $contentBusStop->dynamicContent->user_id) {
            ContentCacheService::invalidateUserContentListCache(
                $contentBusStop->dynamicContent->user_id, 
                'bus_stop'
            );
        }

        Log::info("Bus stop cache invalidated", [
            'bus_stop_id' => $contentBusStop->id,
            'action' => $action,
            'stop_id' => $contentBusStop->stop_id,
            'name' => $contentBusStop->name,
            'coordinates' => [
                'latitude' => $contentBusStop->latitude,
                'longitude' => $contentBusStop->longitude,
            ],
        ]);
    }

    /**
     * Invalidate location-based cache for areas around this bus stop
     */
    private function invalidateLocationCache(BusStop $contentBusStop): void
    {
        if ($contentBusStop->latitude && $contentBusStop->longitude) {
            // Invalidate cache for different radius sizes around this location
            // Bus stops typically need smaller radiuses since they're more localized
            $radiuses = [1.0, 2.0, 5.0]; // km
            
            foreach ($radiuses as $radius) {
                ContentCacheService::invalidateLocationCache(
                    $contentBusStop->latitude,
                    $contentBusStop->longitude,
                    $radius
                );
            }

            Log::info("Location-based cache invalidated for bus stop", [
                'bus_stop_id' => $contentBusStop->id,
                'stop_id' => $contentBusStop->stop_id,
                'latitude' => $contentBusStop->latitude,
                'longitude' => $contentBusStop->longitude,
                'radiuses_cleared' => $radiuses,
            ]);
        }
    }
}