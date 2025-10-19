<?php

namespace App\Console\Commands;

use App\Services\NfcCacheService;
use Illuminate\Console\Command;

class NfcCacheClear extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'nfc:cache-clear 
                           {--type= : Tipo específico de cache (tokens|analytics|themes|all)}
                           {--token= : Token específico para limpiar}';

    /**
     * The console command description.
     */
    protected $description = 'Limpiar cache específico de la aplicación NFC';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $tokenId = $this->option('token');

        if ($tokenId) {
            $this->info("Limpiando cache del token: {$tokenId}");
            NfcCacheService::invalidateTokenCache($tokenId);
            $this->info('✅ Cache del token limpiado');

            return;
        }

        switch ($type) {
            case 'tokens':
                $this->info('🚀 Limpiando cache de tokens...');
                $this->clearTokensCache();

                break;

            case 'analytics':
                $this->info('📊 Limpiando cache de analytics...');
                $this->clearAnalyticsCache();

                break;

            case 'themes':
                $this->info('🎨 Limpiando cache de temas...');
                $this->clearThemesCache();

                break;

            case 'all':
            default:
                $this->info('🧹 Limpiando todo el cache NFC...');
                NfcCacheService::clearAllNfcCache();
                $this->info('✅ Todo el cache NFC ha sido limpiado');

                break;
        }
    }

    private function clearTokensCache(): void
    {
        // Limpiar cache de tokens específicos
        \Cache::forget('customization_plans');
        $this->info('✅ Cache de tokens limpiado');
    }

    private function clearAnalyticsCache(): void
    {
        // Limpiar analytics globales
        \Cache::forget('global_analytics_stats');
        $this->info('✅ Cache de analytics limpiado');
    }

    private function clearThemesCache(): void
    {
        // Limpiar temas
        \Cache::forget('multimedia_themes');
        $this->info('✅ Cache de temas limpiado');
    }
}
