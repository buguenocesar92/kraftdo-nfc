<?php

namespace App\Observers;

use App\Models\ContentEvent;
use App\Services\ContentCacheService;
use Illuminate\Support\Facades\Log;

class ContentEventObserver
{
    /**
     * Handle the ContentEvent "created" event.
     */
    public function created(ContentEvent $contentEvent): void
    {
        $this->invalidateEventCache($contentEvent, 'created');
    }

    /**
     * Handle the ContentEvent "updated" event.
     */
    public function updated(ContentEvent $contentEvent): void
    {
        $this->invalidateEventCache($contentEvent, 'updated');
    }

    /**
     * Handle the ContentEvent "deleted" event.
     */
    public function deleted(ContentEvent $contentEvent): void
    {
        $this->invalidateEventCache($contentEvent, 'deleted');
    }

    /**
     * Handle the ContentEvent "restored" event.
     */
    public function restored(ContentEvent $contentEvent): void
    {
        $this->invalidateEventCache($contentEvent, 'restored');
    }

    /**
     * Handle the ContentEvent "force deleted" event.
     */
    public function forceDeleted(ContentEvent $contentEvent): void
    {
        $this->invalidateEventCache($contentEvent, 'force_deleted');
    }

    /**
     * Invalidate event-specific cache
     */
    private function invalidateEventCache(ContentEvent $contentEvent, string $action): void
    {
        // Invalidate specific event cache
        ContentCacheService::invalidateContentCache('EVENT', $contentEvent->id);

        // Invalidate user's event list cache
        if ($contentEvent->dynamicContent && $contentEvent->dynamicContent->user_id) {
            ContentCacheService::invalidateUserContentListCache(
                $contentEvent->dynamicContent->user_id, 
                'event'
            );
        }

        Log::info("Event cache invalidated", [
            'event_id' => $contentEvent->id,
            'action' => $action,
            'event_location' => $contentEvent->event_location,
            'event_start_date' => $contentEvent->event_start_date,
        ]);
    }
}