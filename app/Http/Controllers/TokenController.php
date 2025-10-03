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
        // 🚀 OPTIMIZACIÓN: Cache completo del token y contenido
        $cachedData = NfcCacheService::getTokenWithContent($tokenId);
        
        if (!$cachedData) {
            abort(404, 'Token no encontrado');
        }

        $token = $cachedData['token'];
        $dynamicContent = $cachedData['dynamicContent'];
        $content = $cachedData['content'];

        // Validaciones rápidas en memoria
        if (!in_array($token->content_type, ['GIFT', 'PROFILE', 'BUSINESS'])) {
            abort(404, 'Tipo de contenido no disponible');
        }

        if (!$token->is_active) {
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

            return view('token.business', $data);
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

    public function preview(Request $request, $tokenId)
    {
        // Similar al show pero para preview en el admin
        $token = NfcToken::where('id', $tokenId)->first();
        
        if (!$token) {
            abort(404, 'Token no encontrado');
        }

        // Verificar que el usuario tenga permisos para ver este token
        if ($token->user_id !== auth()->id() && !auth()->user()->can('view_any_nfc_token')) {
            abort(403, 'No tienes permisos para ver este token');
        }

        // Redirigir a la vista pública usando el token_id
        return redirect()->route('token.show', $token->token_id);
    }
}