<?php

namespace App\Console\Commands;

use App\Models\NfcToken;
use App\Services\NfcCacheService;
use Illuminate\Console\Command;

class NfcCacheWarm extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'nfc:cache-warm 
                           {--tokens= : Número de tokens activos a pre-cachear (default: 50)}
                           {--force : Forzar recache de tokens ya cacheados}';

    /**
     * The console command description.
     */
    protected $description = 'Pre-cachear datos críticos de la aplicación NFC para mejor performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tokensLimit = (int) $this->option('tokens') ?: 50;
        $force = $this->option('force');

        $this->info('🔥 Iniciando pre-cache de datos NFC...');

        // 1. Pre-cachear planes de personalización
        $this->info('📋 Pre-cacheando planes de personalización...');
        NfcCacheService::getCachedCustomizationPlans();
        $this->info('✅ Planes de personalización cacheados');

        // 2. Pre-cachear temas
        $this->info('🎨 Pre-cacheando temas...');
        NfcCacheService::getCachedThemes();
        $this->info('✅ Temas cacheados');

        // 3. Pre-cachear estadísticas globales
        $this->info('📊 Pre-cacheando estadísticas globales...');
        NfcCacheService::getCachedGlobalStats();
        $this->info('✅ Estadísticas globales cacheadas');

        // 4. Pre-cachear tokens más activos
        $this->info("🚀 Pre-cacheando los {$tokensLimit} tokens más activos...");
        $this->warmTopTokens($tokensLimit, $force);

        $this->info('🎉 Pre-cache completado exitosamente!');
    }

    private function warmTopTokens(int $limit, bool $force): void
    {
        // Obtener tokens más recientemente usados
        $tokens = NfcToken::active()
            ->whereNotNull('last_used_at')
            ->orderByDesc('last_used_at')
            ->limit($limit)
            ->get(['token_id', 'id']);

        $bar = $this->output->createProgressBar($tokens->count());
        $bar->start();

        $cached = 0;
        $skipped = 0;

        foreach ($tokens as $token) {
            $cacheKey = "nfc_token_full:{$token->token_id}";
            
            if (!$force && \Cache::has($cacheKey)) {
                $skipped++;
            } else {
                // Pre-cachear token con contenido
                NfcCacheService::getTokenWithContent($token->token_id);
                
                // Pre-cachear ROI si es necesario
                NfcCacheService::getCachedTokenROI($token->id);
                
                $cached++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Tokens procesados: {$cached} cacheados, {$skipped} ya en cache");
    }
}