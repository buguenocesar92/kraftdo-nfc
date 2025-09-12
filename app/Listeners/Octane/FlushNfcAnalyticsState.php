<?php

namespace App\Listeners\Octane;

use Laravel\Octane\Contracts\OperationTerminated;

class FlushNfcAnalyticsState implements OperationTerminated
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        // Limpiar cualquier estado de analytics que pueda persistir
        if (class_exists(\App\Models\NfcAnalytic::class)) {
            // Reset any static properties if they exist in future versions
        }
        
        // Limpiar variables de sessión temporales relacionadas con analytics
        if (session()->has('nfc_temp_analytics')) {
            session()->forget('nfc_temp_analytics');
        }
        
        // Force garbage collection para arrays de analytics grandes
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}