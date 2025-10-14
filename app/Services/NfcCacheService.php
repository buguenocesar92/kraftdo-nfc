<?php

namespace App\Services;

use App\Models\NfcToken;
use App\Models\ContentGift;
use App\Models\ContentProfile;
use App\Models\ContentBusiness;
use App\Models\ContentTourist;
use App\Models\ContentBusinessGroup;
use App\Models\ContentMultimedia;
use App\Models\NfcAnalytic;
use Illuminate\Support\Facades\Cache;

class NfcCacheService
{
    // TTL en segundos
    const TOKEN_CACHE_TTL = 3600;        // 1 hora - tokens cambian poco
    const CONTENT_CACHE_TTL = 1800;      // 30 min - contenido puede editarse
    const ANALYTICS_CACHE_TTL = 600;     // 10 min - analytics se actualizan frecuentemente
    const STATIC_CACHE_TTL = 86400;      // 24 horas - datos estáticos

    /**
     * Obtener token con cache optimizado para escaneos NFC
     */
    public static function getTokenWithContent(string $tokenId): ?array
    {
        $cacheKey = "nfc_token_full:{$tokenId}";
        
        return Cache::remember($cacheKey, self::TOKEN_CACHE_TTL, function() use ($tokenId) {
            $token = NfcToken::with(['dynamicContent', 'user'])
                ->where('token_id', $tokenId)
                ->where('is_active', true)
                ->first();

            if (!$token || !$token->dynamicContent) {
                return null;
            }

            $dynamicContent = $token->dynamicContent;
            $contentData = [];

            // Cache contenido según tipo
            if ($token->content_type === 'GIFT') {
                $contentData = [
                    'gift' => ContentGift::where('dynamic_content_id', $dynamicContent->id)->first(),
                    'multimedia' => ContentMultimedia::with(['galleryImages' => function($query) {
                        $query->orderBy('sort_order')->orderBy('id');
                    }])->where('dynamic_content_id', $dynamicContent->id)->first()
                ];
            } elseif ($token->content_type === 'PROFILE') {
                $contentData = [
                    'profile' => ContentProfile::with(['socialLinks' => function($query) {
                        $query->ordered();
                    }])->where('dynamic_content_id', $dynamicContent->id)->first(),
                    'multimedia' => ContentMultimedia::with(['galleryImages' => function($query) {
                        $query->orderBy('sort_order')->orderBy('id');
                    }])->where('dynamic_content_id', $dynamicContent->id)->first()
                ];
            } elseif ($token->content_type === 'BUSINESS') {
                $contentData = [
                    'business' => ContentBusiness::with(['socialLinks' => function($query) {
                        $query->ordered();
                    }, 'products'])->where('dynamic_content_id', $dynamicContent->id)->first(),
                    'multimedia' => ContentMultimedia::with(['galleryImages' => function($query) {
                        $query->orderBy('sort_order')->orderBy('id');
                    }])->where('dynamic_content_id', $dynamicContent->id)->first()
                ];
            } elseif ($token->content_type === 'TOURIST') {
                $contentData = [
                    'tourist' => ContentTourist::with(['nearbySpots' => function($query) {
                        $query->where('is_active', true)->orderBy('distance_km')->orderBy('sort_order');
                    }])->where('dynamic_content_id', $dynamicContent->id)->first()
                ];
            } elseif ($token->content_type === 'MENU') {
                // MENU is deprecated - treating as BUSINESS (restaurantes)
                $contentData = [
                    'business' => ContentBusiness::with(['socialLinks' => function($query) {
                        $query->ordered();
                    }, 'directProducts'])->where('dynamic_content_id', $dynamicContent->id)->first(),
                    'multimedia' => ContentMultimedia::with(['galleryImages' => function($query) {
                        $query->orderBy('sort_order')->orderBy('id');
                    }])->where('dynamic_content_id', $dynamicContent->id)->first()
                ];
            } elseif ($token->content_type === 'BUS_STOP') {
                $contentData = [
                    'bus_stop' => \App\Models\BusStop::with(['routes.schedules', 'utilityPhones'])->where('dynamic_content_id', $dynamicContent->id)->first()
                ];
            } elseif ($token->content_type === 'BUSINESS_GROUP') {
                $businessGroup = ContentBusinessGroup::with([
                    'memberBusinesses' => function($query) {
                        $query->wherePivot('member_status', 'active')
                              ->orderByPivot('display_order')
                              ->orderByPivot('is_featured', 'desc');
                    },
                    'memberBusinesses.dynamicContent',
                    'memberBusinesses.dynamicContent.nfcToken'
                ])->where('dynamic_content_id', $dynamicContent->id)->first();
                
                $contentData = [
                    'business_group' => $businessGroup
                ];
            }

            return [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'content' => $contentData
            ];
        });
    }

    /**
     * Cache de estadísticas de analytics con optimización
     */
    public static function getCachedAnalytics(string $contentId): array
    {
        $cacheKey = "analytics_stats:{$contentId}";
        
        return Cache::remember($cacheKey, self::ANALYTICS_CACHE_TTL, function() use ($contentId) {
            return NfcAnalytic::getStatsForContent($contentId);
        });
    }

    /**
     * Cache de estadísticas globales (muy pesadas)
     */
    public static function getCachedGlobalStats(): array
    {
        $cacheKey = "global_analytics_stats";
        
        return Cache::remember($cacheKey, self::ANALYTICS_CACHE_TTL, function() {
            return NfcAnalytic::getGlobalStats();
        });
    }

    /**
     * Cache de planes de personalización (datos estáticos)
     */
    public static function getCachedCustomizationPlans(): array
    {
        return Cache::rememberForever('customization_plans', function() {
            return NfcToken::getCustomizationPlans();
        });
    }

    /**
     * Cache de ROI y métricas financieras por token
     */
    public static function getCachedTokenROI(int $tokenId): array
    {
        $cacheKey = "token_roi:{$tokenId}";
        
        return Cache::remember($cacheKey, 300, function() use ($tokenId) { // 5 min TTL
            $token = NfcToken::find($tokenId);
            return $token ? $token->getROI() : [];
        });
    }

    /**
     * Cache de temas para contenido multimedia
     */
    public static function getCachedThemes(): array
    {
        return Cache::rememberForever('multimedia_themes', function() {
            return \App\Helpers\ThemeHelper::getAllThemes();
        });
    }

    // =====================================
    // MÉTODOS DE INVALIDACIÓN DE CACHE
    // =====================================

    /**
     * Invalidar cache cuando se actualiza un token
     */
    public static function invalidateTokenCache(string $tokenId): void
    {
        Cache::forget("nfc_token_full:{$tokenId}");
        
        // Si tenemos el ID numérico, invalidar ROI también
        $token = NfcToken::where('token_id', $tokenId)->first();
        if ($token) {
            Cache::forget("token_roi:{$token->id}");
        }
    }

    /**
     * Invalidar cache cuando se actualiza contenido
     */
    public static function invalidateContentCache(string $contentId): void
    {
        Cache::forget("analytics_stats:{$contentId}");
        
        // Buscar token relacionado e invalidar
        $token = NfcToken::whereHas('dynamicContent', function($query) use ($contentId) {
            $query->where('content_id', $contentId);
        })->first();
        
        if ($token) {
            self::invalidateTokenCache($token->token_id);
        }
    }

    /**
     * Invalidar analytics cuando se registra nuevo acceso
     */
    public static function invalidateAnalyticsCache(string $contentId): void
    {
        Cache::forget("analytics_stats:{$contentId}");
        Cache::forget("global_analytics_stats");
    }

    /**
     * Limpiar todo el cache NFC (para mantenimiento)
     */
    public static function clearAllNfcCache(): void
    {
        // Obtener todas las claves de cache NFC
        $patterns = [
            'nfc_token_full:*',
            'analytics_stats:*', 
            'token_roi:*',
            'global_analytics_stats',
            'customization_plans',
            'multimedia_themes'
        ];

        foreach ($patterns as $pattern) {
            // En Redis puedes usar SCAN con patrón
            Cache::flush(); // Para desarrollo, en producción usar scan pattern
        }
    }

    // =====================================
    // MÉTRICAS DE CACHE
    // =====================================

    /**
     * Obtener estadísticas de uso de cache
     */
    public static function getCacheStats(): array
    {
        $driver = config('cache.default');
        $stats = [
            'cache_driver' => $driver,
        ];
        
        // Solo obtener info de Redis si estamos usando Redis
        if ($driver === 'redis') {
            try {
                $stats['redis_info'] = Cache::getRedis()->info() ?? 'No disponible';
            } catch (\Exception $e) {
                $stats['redis_info'] = 'No disponible: ' . $e->getMessage();
            }
        }
        
        return $stats;
    }
}