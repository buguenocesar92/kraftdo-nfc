<?php

namespace App\Http\Controllers;

use App\Models\NfcToken;
use App\Models\ContentGift;
use App\Models\ContentProfile;
use App\Models\ContentMultimedia;
use App\Models\NfcAnalytic;
use App\Helpers\ThemeHelper;
use App\Services\NfcCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TokenController extends Controller
{
    public function show(Request $request, $tokenId)
    {
        // Debug: Disable cache for bus stop tokens temporarily
        if ($tokenId === '9bbbd1c1-163c-48d6-b442-1c3d1997c8d4') {
            \Log::info('Debug: Processing BUS_STOP token directly without cache');
            
            $token = \App\Models\NfcToken::with(['dynamicContent', 'user'])
                ->where('token_id', $tokenId)
                ->where('is_active', true)
                ->first();
                
            if (!$token || !$token->dynamicContent) {
                abort(404, 'Token no encontrado');
            }
            
            $cachedData = [
                'token' => $token,
                'dynamicContent' => $token->dynamicContent,
                'content' => [
                    'bus_stop' => \App\Models\BusStop::with(['routes.schedules', 'utilityPhones'])->where('dynamic_content_id', $token->dynamicContent->id)->first()
                ]
            ];
        } else {
            // 🚀 OPTIMIZACIÓN: Cache completo del token y contenido
            $cachedData = NfcCacheService::getTokenWithContent($tokenId);
        }
        
        if (!$cachedData) {
            // Respuesta diferente según si es API o web
            if ($request->expectsJson()) {
                return response()->json([
                    'data' => null,
                    'message' => 'Token no encontrado',
                    'status' => 404
                ], 404);
            }
            abort(404, 'Token no encontrado');
        }

        $token = $cachedData['token'];
        $dynamicContent = $cachedData['dynamicContent'];
        $content = $cachedData['content'];
        
        // Debug: Log content type
        \Log::info('Token content type: ' . $token->content_type);

        // Validaciones rápidas en memoria
        if (!in_array($token->content_type, ['GIFT', 'PROFILE', 'BUSINESS', 'TOURIST', 'MENU', 'BUS_STOP', 'BUSINESS_GROUP'])) { // MENU kept for legacy compatibility
            if ($request->expectsJson()) {
                return response()->json([
                    'data' => null,
                    'message' => 'Tipo de contenido no disponible',
                    'status' => 404
                ], 404);
            }
            abort(404, 'Tipo de contenido no disponible');
        }

        if (!$token->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $token,
                    'message' => 'Token inactivo',
                    'status' => 200
                ]);
            }
            return view('token.inactive', compact('token'));
        }

        // 📊 REGISTRO DE ANALYTICS (asíncrono en background)
        $this->recordAnalyticsAsync($dynamicContent->content_id, $token->content_type, $token->id);

        // Preparar datos según tipo de contenido
        if ($token->content_type === 'GIFT') {
            $contentGift = $content['gift'];
            $contentMultimedia = $content['multimedia'];
            $galleryImages = $contentMultimedia?->galleryImages ?? collect();

            // 🎨 Cache del tema
            $theme = $contentMultimedia?->settings['theme'] ?? 'love';
            $themeConfig = Cache::remember("theme_config:{$theme}", 3600, function() use ($theme) {
                return ThemeHelper::getThemeConfig($theme);
            });

            $data = [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'contentGift' => $contentGift,
                'contentMultimedia' => $contentMultimedia,
                'galleryImages' => $galleryImages,
                'theme' => $themeConfig,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $data,
                    'message' => 'Token obtenido exitosamente',
                    'status' => 200
                ]);
            }

            return view('token.gift', $data);
            
        } elseif ($token->content_type === 'PROFILE') {
            $contentProfile = $content['profile'];
            $contentMultimedia = $content['multimedia'];
            $galleryImages = $contentMultimedia?->galleryImages ?? collect();
            $socialLinks = $contentProfile?->socialLinks ?? collect();

            $data = [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'contentProfile' => $contentProfile,
                'contentMultimedia' => $contentMultimedia,
                'galleryImages' => $galleryImages,
                'socialLinks' => $socialLinks,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $data,
                    'message' => 'Token obtenido exitosamente',
                    'status' => 200
                ]);
            }

            return view('token.profile', $data);
            
        } elseif ($token->content_type === 'BUSINESS') {
            $contentBusiness = $content['business'];
            $contentMultimedia = $content['multimedia'];
            $galleryImages = $contentMultimedia?->galleryImages ?? collect();
            $socialLinks = $contentBusiness?->socialLinks ?? collect();

            $data = [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'contentBusiness' => $contentBusiness,
                'contentMultimedia' => $contentMultimedia,
                'galleryImages' => $galleryImages,
                'socialLinks' => $socialLinks,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $data,
                    'message' => 'Token obtenido exitosamente',
                    'status' => 200
                ]);
            }

            return view('token.business', $data);
            
        } elseif ($token->content_type === 'TOURIST') {
            $contentTourist = $content['tourist'];
            $nearbySpots = $contentTourist?->activeNearbySpots ?? collect();

            $data = [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'content' => $dynamicContent, // Para compatibilidad con la vista tourist
                'tourist' => $contentTourist,
                'mapData' => $contentTourist?->getMapData() ?? [],
                'nearbySpots' => $nearbySpots,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $data,
                    'message' => 'Token obtenido exitosamente',
                    'status' => 200
                ]);
            }

            return view('token.tourist', $data);
            
        } elseif ($token->content_type === 'MENU') { // DEPRECATED - treating as BUSINESS
            $data = [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'content' => $content,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $data,
                    'message' => 'Token obtenido exitosamente',
                    'status' => 200
                ]);
            }

            // Para web, podríamos crear una vista menu, por ahora usamos business
            return view('token.business', $data);
            
        } elseif ($token->content_type === 'BUS_STOP') {
            $busStopData = $content['bus_stop'] ?? null;
            
            // El modelo puede venir serializado de forma extraña, accedemos directamente
            if ($busStopData && is_array($busStopData)) {
                $contentBusStop = array_values($busStopData)[0] ?? null;
            } else {
                $contentBusStop = $busStopData;
            }
            
            $routes = $contentBusStop['routes'] ?? [];
            $utilityPhones = $contentBusStop['utility_phones'] ?? $contentBusStop['utilityPhones'] ?? [];
            
            $data = [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'content' => $dynamicContent, // Alias para compatibilidad con la vista
                'contentBusStop' => $contentBusStop,
                'routes' => $routes,
                'utilityPhones' => $utilityPhones,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $data,
                    'message' => 'Token obtenido exitosamente',
                    'status' => 200
                ]);
            }

            // Crear una vista simple inline para mostrar toda la data del paradero
            $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($dynamicContent->title) . '</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600 mb-2">' . htmlspecialchars($dynamicContent->title) . '</h1>
            <p class="text-gray-600">' . htmlspecialchars($dynamicContent->description) . '</p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-xl font-semibold mb-4 text-green-600">🚌 Rutas de Transporte (' . count($routes) . ')</h2>';
                
            foreach ($routes as $route) {
                $html .= '<div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-3 py-1 rounded-full text-white font-bold" style="background-color: ' . htmlspecialchars($route['color']) . '">
                            Línea ' . htmlspecialchars($route['route_number']) . '
                        </span>
                        <span class="text-lg font-bold text-green-600">$' . number_format($route['fare']) . ' ' . htmlspecialchars($route['currency']) . '</span>
                    </div>
                    <h3 class="font-semibold">' . htmlspecialchars($route['name']) . '</h3>
                    <p class="text-sm text-gray-600 mb-2">
                        <strong>Desde:</strong> ' . htmlspecialchars($route['origin']) . '<br>
                        <strong>Hacia:</strong> ' . htmlspecialchars($route['destination']) . '<br>
                        <strong>Operador:</strong> ' . htmlspecialchars($route['operator']) . '
                    </p>';
                    
                if (isset($route['schedules']) && count($route['schedules']) > 0) {
                    $html .= '<div class="mt-2">
                        <p class="text-xs font-semibold text-blue-600">Horarios disponibles:</p>';
                    foreach ($route['schedules'] as $schedule) {
                        $html .= '<div class="text-xs text-gray-500">
                            <strong>' . ucfirst($schedule['day_of_week']) . ':</strong> 
                            Cada ' . $schedule['frequency_minutes'] . ' min | ' . $schedule['notes'] . '
                        </div>';
                    }
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            
            $html .= '</div>
            
            <div>
                <h2 class="text-xl font-semibold mb-4 text-red-600">📞 Teléfonos Útiles (' . count($utilityPhones) . ')</h2>';
                
            foreach ($utilityPhones as $phone) {
                $iconColor = $phone['is_emergency'] ? 'text-red-600' : 'text-blue-600';
                $bgColor = $phone['is_emergency'] ? 'bg-red-50' : 'bg-blue-50';
                
                $html .= '<div class="' . $bgColor . ' p-3 rounded-lg mb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-xl mr-3">' . $phone['icon'] . '</span>
                            <div>
                                <h3 class="font-semibold ' . $iconColor . '">' . htmlspecialchars($phone['name']) . '</h3>
                                <p class="text-sm text-gray-600">' . htmlspecialchars($phone['description']) . '</p>
                            </div>
                        </div>
                        <a href="tel:' . htmlspecialchars($phone['phone_number']) . '" 
                           class="' . $iconColor . ' font-bold text-lg hover:underline">
                           ' . htmlspecialchars($phone['phone_number']) . '
                        </a>
                    </div>
                </div>';
            }
            
            $html .= '</div>
        </div>
        
        <div class="mt-8 bg-blue-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold text-blue-800 mb-2">📍 Información del Paradero</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>Código:</strong> ' . htmlspecialchars($contentBusStop['stop_id']) . '<br>
                    <strong>Dirección:</strong> ' . htmlspecialchars($contentBusStop['address']) . '<br>
                    <strong>Municipio:</strong> ' . htmlspecialchars($contentBusStop['municipality_name']) . '
                </div>
                <div>
                    <strong>Coordenadas:</strong> ' . $contentBusStop['latitude'] . ', ' . $contentBusStop['longitude'] . '<br>
                    <strong>Web Municipio:</strong> <a href="' . htmlspecialchars($contentBusStop['municipality_website']) . '" class="text-blue-600 hover:underline" target="_blank">Sitio Web</a>
                </div>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Información actualizada • Powered by KraftDo NFC
            </p>
        </div>
    </div>
</body>
</html>';
            
            return $html;
            
        } elseif ($token->content_type === 'BUSINESS_GROUP') {
            $businessGroupData = $content['business_group'] ?? null;
            
            $data = [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'content' => $dynamicContent, // Alias para compatibilidad
                'businessGroup' => $businessGroupData,
                'memberBusinesses' => $businessGroupData ? $businessGroupData->activeMemberBusinesses : collect([]),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $data,
                    'message' => 'Token obtenido exitosamente',
                    'status' => 200
                ]);
            }

            return view('token.business-group', $data);
        }
    }

    /**
     * 🛍️ Mostrar catálogo completo de productos de un negocio
     */
    public function showProducts(Request $request, $tokenId)
    {
        // Obtener datos del token usando el cache
        $cachedData = NfcCacheService::getTokenWithContent($tokenId);
        
        if (!$cachedData) {
            abort(404, 'Token no encontrado');
        }

        $token = $cachedData['token'];
        $dynamicContent = $cachedData['dynamicContent'];
        $content = $cachedData['content'];

        // Validar que sea un token de tipo BUSINESS
        if ($token->content_type !== 'BUSINESS') {
            abort(404, 'Esta página solo está disponible para negocios');
        }

        if (!$token->is_active) {
            return view('token.inactive', compact('token'));
        }

        $contentBusiness = $content['business'];
        
        // Verificar que el negocio tenga catálogo habilitado
        if (!$contentBusiness || !$contentBusiness->catalog_enabled) {
            abort(404, 'Catálogo no disponible para este negocio');
        }

        // Obtener todos los productos del negocio
        $products = $contentBusiness->products()->get();

        // 📊 Registrar analytics
        $this->recordAnalyticsAsync($dynamicContent->content_id, $token->content_type, $token->id);

        $data = [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'contentBusiness' => $contentBusiness,
            'products' => $products,
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $products,
                'message' => 'Productos obtenidos exitosamente',
                'status' => 200
            ]);
        }

        return view('token.business-products', $data);
    }

    /**
     * 📊 Registro asíncrono de analytics para no impactar performance
     */
    private function recordAnalyticsAsync(string $contentId, string $contentType, int $tokenId): void
    {
        // En entorno de producción, esto debería ser una cola/job
        try {
            NfcAnalytic::recordAccess($contentId, $contentType, $tokenId);
            
            // Invalidar cache de analytics después del registro
            NfcCacheService::invalidateAnalyticsCache($contentId);
        } catch (\Exception $e) {
            // Log error pero no interrumpir la respuesta
            \Log::warning('Analytics recording failed', [
                'content_id' => $contentId,
                'error' => $e->getMessage()
            ]);
        }
    }
}