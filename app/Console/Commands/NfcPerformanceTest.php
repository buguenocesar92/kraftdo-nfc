<?php

namespace App\Console\Commands;

use App\Models\NfcToken;
use App\Services\NfcCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NfcPerformanceTest extends Command
{
    protected $signature = 'nfc:performance-test 
                           {--clear-cache : Limpiar cache antes de la prueba}
                           {--iterations=10 : Número de iteraciones para el test}';

    protected $description = 'Probar performance de la aplicación NFC con y sin cache';

    public function handle()
    {
        $iterations = (int) $this->option('iterations');

        if ($this->option('clear-cache')) {
            $this->info('🧹 Limpiando cache...');
            Cache::flush();
        }

        $this->info('🚀 Iniciando test de performance NFC...');
        $this->newLine();

        // Obtener un token de prueba
        $token = NfcToken::with('dynamicContent')->active()->first();

        if (! $token) {
            $this->error('❌ No se encontró ningún token activo para testing');

            return 1;
        }

        $this->info("🎯 Token de prueba: {$token->token_id}");
        $this->info("📊 Iteraciones: {$iterations}");
        $this->newLine();

        // Test 1: Performance SIN cache
        $this->info('🔴 TEST 1: SIN CACHE (queries directas a BD)');
        $withoutCache = $this->testWithoutCache($token->token_id, $iterations);

        $this->newLine();

        // Test 2: Performance CON cache
        $this->info('🟢 TEST 2: CON CACHE (optimizado)');
        $withCache = $this->testWithCache($token->token_id, $iterations);

        $this->newLine();

        // Mostrar resultados
        $this->showResults($withoutCache, $withCache, $iterations);
    }

    private function testWithoutCache(string $tokenId, int $iterations): array
    {
        $times = [];
        $queryCount = 0;

        for ($i = 0; $i < $iterations; $i++) {
            // Limpiar cache para esta iteración
            Cache::forget("nfc_token_full:{$tokenId}");

            // Contar queries
            DB::enableQueryLog();

            $start = microtime(true);

            // Simular el proceso SIN cache (como era antes)
            $token = NfcToken::where('token_id', $tokenId)->first();
            if ($token) {
                $dynamicContent = $token->dynamicContent;
                if ($dynamicContent && $token->content_type === 'GIFT') {
                    $contentGift = \App\Models\ContentGift::where('dynamic_content_id', $dynamicContent->id)->first();
                    $contentMultimedia = \App\Models\ContentMultimedia::where('dynamic_content_id', $dynamicContent->id)->first();
                    if ($contentMultimedia) {
                        $galleryImages = $contentMultimedia->galleryImages()->orderBy('sort_order')->get();
                    }
                }
            }

            $end = microtime(true);
            $times[] = ($end - $start) * 1000; // en milisegundos

            $queries = DB::getQueryLog();
            $queryCount += count($queries);

            $this->output->write('.');
        }

        $this->newLine();

        return [
            'times' => $times,
            'avg_time' => array_sum($times) / count($times),
            'min_time' => min($times),
            'max_time' => max($times),
            'total_queries' => $queryCount,
            'avg_queries' => $queryCount / $iterations,
        ];
    }

    private function testWithCache(string $tokenId, int $iterations): array
    {
        $times = [];
        $queryCount = 0;

        for ($i = 0; $i < $iterations; $i++) {
            DB::enableQueryLog();

            $start = microtime(true);

            // Usar el servicio optimizado CON cache
            $cachedData = NfcCacheService::getTokenWithContent($tokenId);

            $end = microtime(true);
            $times[] = ($end - $start) * 1000; // en milisegundos

            $queries = DB::getQueryLog();
            $queryCount += count($queries);

            $this->output->write('.');
        }

        $this->newLine();

        return [
            'times' => $times,
            'avg_time' => array_sum($times) / count($times),
            'min_time' => min($times),
            'max_time' => max($times),
            'total_queries' => $queryCount,
            'avg_queries' => $queryCount / $iterations,
        ];
    }

    private function showResults(array $withoutCache, array $withCache, int $iterations): void
    {
        $this->info('📊 RESULTADOS DE PERFORMANCE:');
        $this->newLine();

        // Tabla de tiempos
        $this->table(
            ['Métrica', 'Sin Cache', 'Con Cache', 'Mejora'],
            [
                [
                    'Tiempo Promedio',
                    number_format($withoutCache['avg_time'], 2) . ' ms',
                    number_format($withCache['avg_time'], 2) . ' ms',
                    $this->calculateImprovement($withoutCache['avg_time'], $withCache['avg_time']),
                ],
                [
                    'Tiempo Mínimo',
                    number_format($withoutCache['min_time'], 2) . ' ms',
                    number_format($withCache['min_time'], 2) . ' ms',
                    $this->calculateImprovement($withoutCache['min_time'], $withCache['min_time']),
                ],
                [
                    'Tiempo Máximo',
                    number_format($withoutCache['max_time'], 2) . ' ms',
                    number_format($withCache['max_time'], 2) . ' ms',
                    $this->calculateImprovement($withoutCache['max_time'], $withCache['max_time']),
                ],
                [
                    'Queries Promedio',
                    number_format($withoutCache['avg_queries'], 1),
                    number_format($withCache['avg_queries'], 1),
                    $this->calculateImprovement($withoutCache['avg_queries'], $withCache['avg_queries']),
                ],
            ]
        );

        $this->newLine();

        // Análisis
        $speedup = $withoutCache['avg_time'] / $withCache['avg_time'];
        $queryReduction = (($withoutCache['avg_queries'] - $withCache['avg_queries']) / $withoutCache['avg_queries']) * 100;

        $this->info("🚀 CONCLUSIONES:");
        $this->info("   • La aplicación es {$speedup}x más rápida con cache");
        $this->info("   • Reducción de {$queryReduction}% en queries a la BD");
        $this->info("   • Tiempo ahorrado por request: " . number_format($withoutCache['avg_time'] - $withCache['avg_time'], 2) . " ms");

        $this->newLine();

        if ($speedup > 2) {
            $this->info('🎉 ¡EXCELENTE! Cache funcionando óptimamente');
        } elseif ($speedup > 1.5) {
            $this->info('✅ Cache funcionando bien');
        } else {
            $this->warn('⚠️  Mejora marginal - revisar configuración');
        }
    }

    private function calculateImprovement(float $before, float $after): string
    {
        $improvement = (($before - $after) / $before) * 100;
        $color = $improvement > 50 ? 'green' : ($improvement > 20 ? 'yellow' : 'red');

        return sprintf('<fg=%s>%s%.1f%%</>', $color, $improvement > 0 ? '-' : '+', abs($improvement));
    }
}
