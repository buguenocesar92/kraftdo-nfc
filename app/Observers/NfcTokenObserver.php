<?php

namespace App\Observers;

use App\Models\NfcToken;
use App\Services\NfcCacheService;

class NfcTokenObserver
{
    /**
     * Handle the NfcToken "created" event.
     */
    public function created(NfcToken $nfcToken): void
    {
        // Limpiar cache global cuando se crea un nuevo token
        \Cache::forget('global_analytics_stats');
    }

    /**
     * Handle the NfcToken "updated" event.
     */
    public function updated(NfcToken $nfcToken): void
    {
        // Invalidar cache del token específico
        NfcCacheService::invalidateTokenCache($nfcToken->token_id);
        
        // Si cambió el estado activo, limpiar stats globales
        if ($nfcToken->wasChanged('is_active')) {
            \Cache::forget('global_analytics_stats');
        }
    }

    /**
     * Handle the NfcToken "deleted" event.
     */
    public function deleted(NfcToken $nfcToken): void
    {
        // Limpiar todo el cache relacionado con este token
        NfcCacheService::invalidateTokenCache($nfcToken->token_id);
        \Cache::forget('global_analytics_stats');
        
        // Limpiar analytics relacionadas
        if ($nfcToken->dynamicContent) {
            NfcCacheService::invalidateAnalyticsCache($nfcToken->dynamicContent->content_id);
        }
    }
}