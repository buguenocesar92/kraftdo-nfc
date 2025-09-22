<?php

namespace App\Observers;

use App\Models\ContentProfile;
use Illuminate\Support\Facades\Cache;

class ContentProfileObserver
{
    /**
     * Handle the ContentProfile "created" event.
     */
    public function created(ContentProfile $contentProfile): void
    {
        $this->clearDynamicContentCache($contentProfile);
    }

    /**
     * Handle the ContentProfile "updated" event.
     */
    public function updated(ContentProfile $contentProfile): void
    {
        $this->clearDynamicContentCache($contentProfile);
    }

    /**
     * Handle the ContentProfile "deleted" event.
     */
    public function deleted(ContentProfile $contentProfile): void
    {
        $this->clearDynamicContentCache($contentProfile);
    }

    /**
     * Clear cache for the associated dynamic content
     */
    private function clearDynamicContentCache(ContentProfile $contentProfile): void
    {
        if ($contentProfile->dynamic_content_id) {
            Cache::forget("dynamic_content_{$contentProfile->dynamic_content_id}");
        }
    }
}