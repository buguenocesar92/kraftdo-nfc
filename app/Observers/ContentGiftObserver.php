<?php

namespace App\Observers;

use App\Models\ContentGift;
use App\Services\NfcCacheService;

class ContentGiftObserver
{
    /**
     * Handle the ContentGift "updated" event.
     */
    public function updated(ContentGift $contentGift): void
    {
        $this->invalidateRelatedCache($contentGift);
    }

    /**
     * Handle the ContentGift "deleted" event.
     */
    public function deleted(ContentGift $contentGift): void
    {
        $this->invalidateRelatedCache($contentGift);
    }

    /**
     * Handle the ContentGift "created" event.
     */
    public function created(ContentGift $contentGift): void
    {
        $this->invalidateRelatedCache($contentGift);
    }

    /**
     * Invalidar cache relacionado
     */
    private function invalidateRelatedCache(ContentGift $contentGift): void
    {
        // Obtener el DynamicContent relacionado
        $dynamicContent = $contentGift->dynamicContent;
        
        if (!$dynamicContent) {
            return;
        }

        // Invalidar cache del contenido
        NfcCacheService::invalidateContentCache($dynamicContent->content_id);

        // Si tiene token asociado, invalidar su cache también
        if ($dynamicContent->nfcToken) {
            NfcCacheService::invalidateTokenCache($dynamicContent->nfcToken->token_id);
        }
    }
}