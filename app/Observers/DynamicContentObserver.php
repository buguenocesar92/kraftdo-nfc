<?php

namespace App\Observers;

use App\Models\DynamicContent;
use App\Services\NfcCacheService;

class DynamicContentObserver
{
    /**
     * Handle the DynamicContent "updated" event.
     */
    public function updated(DynamicContent $dynamicContent): void
    {
        // Invalidar cache del contenido
        NfcCacheService::invalidateContentCache($dynamicContent->content_id);

        // Si tiene token asociado, invalidar su cache también
        if ($dynamicContent->nfcToken) {
            NfcCacheService::invalidateTokenCache($dynamicContent->nfcToken->token_id);
        }
    }

    /**
     * Handle the DynamicContent "deleted" event.
     */
    public function deleted(DynamicContent $dynamicContent): void
    {
        // Limpiar todo el cache relacionado
        NfcCacheService::invalidateContentCache($dynamicContent->content_id);

        if ($dynamicContent->nfcToken) {
            NfcCacheService::invalidateTokenCache($dynamicContent->nfcToken->token_id);
        }
    }
}
