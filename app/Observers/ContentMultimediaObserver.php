<?php

namespace App\Observers;

use App\Models\ContentMultimedia;
use App\Services\NfcCacheService;

class ContentMultimediaObserver
{
    /**
     * Handle the ContentMultimedia "updated" event.
     */
    public function updated(ContentMultimedia $contentMultimedia): void
    {
        // Invalidar cache del contenido dinámico relacionado
        if ($contentMultimedia->dynamicContent) {
            NfcCacheService::invalidateContentCache($contentMultimedia->dynamicContent->content_id);

            // Si hay token asociado, invalidar su cache
            if ($contentMultimedia->dynamicContent->nfcToken) {
                NfcCacheService::invalidateTokenCache($contentMultimedia->dynamicContent->nfcToken->token_id);
            }
        }
    }

    /**
     * Handle the ContentMultimedia "deleted" event.
     */
    public function deleted(ContentMultimedia $contentMultimedia): void
    {
        // Mismo proceso que updated
        if ($contentMultimedia->dynamicContent) {
            NfcCacheService::invalidateContentCache($contentMultimedia->dynamicContent->content_id);

            if ($contentMultimedia->dynamicContent->nfcToken) {
                NfcCacheService::invalidateTokenCache($contentMultimedia->dynamicContent->nfcToken->token_id);
            }
        }
    }
}
