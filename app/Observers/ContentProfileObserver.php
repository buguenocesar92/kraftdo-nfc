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
     * Clear cache for the associated dynamic content and token
     */
    private function clearDynamicContentCache(ContentProfile $contentProfile): void
    {
        if ($contentProfile->dynamic_content_id) {
            Cache::forget("dynamic_content_{$contentProfile->dynamic_content_id}");
            
            // Also clear the token cache since profile updates affect token display
            if ($contentProfile->dynamicContent && $contentProfile->dynamicContent->nfcToken) {
                $tokenId = $contentProfile->dynamicContent->nfcToken->token_id;
                Cache::forget("nfc_token_full:{$tokenId}");
            }
        }
    }
}
