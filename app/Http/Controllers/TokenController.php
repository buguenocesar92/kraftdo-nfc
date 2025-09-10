<?php

namespace App\Http\Controllers;

use App\Models\NfcToken;
use App\Models\ContentGift;
use App\Models\ContentProfile;
use App\Models\ContentMultimedia;
use App\Helpers\ThemeHelper;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function show(Request $request, $tokenId)
    {
        // Buscar el token por su token_id (UUID)
        $token = NfcToken::where('token_id', $tokenId)->first();
        
        if (!$token) {
            abort(404, 'Token no encontrado');
        }

        // Manejamos tokens de tipo GIFT y PROFILE
        if (!in_array($token->content_type, ['GIFT', 'PROFILE'])) {
            abort(404, 'Tipo de contenido no disponible');
        }

        // Verificar que el token esté activo
        if (!$token->is_active) {
            return view('token.inactive', compact('token'));
        }

        // Obtener el contenido dinámico
        $dynamicContent = $token->dynamicContent;
        if (!$dynamicContent) {
            abort(404, 'Contenido no encontrado');
        }

        if ($token->content_type === 'GIFT') {
            // Obtener el contenido del regalo
            $contentGift = ContentGift::where('dynamic_content_id', $dynamicContent->id)->first();
            
            // Obtener contenido multimedia si existe
            $contentMultimedia = ContentMultimedia::where('dynamic_content_id', $dynamicContent->id)->first();
            
            // Obtener imágenes de galería si existen
            $galleryImages = [];
            if ($contentMultimedia) {
                $galleryImages = $contentMultimedia->galleryImages()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            }

            // Obtener el tema configurado
            $theme = $contentMultimedia ? ($contentMultimedia->settings['theme'] ?? 'love') : 'love';
            $themeConfig = ThemeHelper::getThemeConfig($theme);

            // Datos para la vista
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
            // Obtener el contenido del perfil
            $contentProfile = ContentProfile::where('dynamic_content_id', $dynamicContent->id)->first();
            
            // Obtener contenido multimedia si existe
            $contentMultimedia = ContentMultimedia::where('dynamic_content_id', $dynamicContent->id)->first();
            
            // Obtener imágenes de galería si existen
            $galleryImages = [];
            if ($contentMultimedia) {
                $galleryImages = $contentMultimedia->galleryImages()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            }

            // Obtener enlaces sociales si existen
            $socialLinks = [];
            if ($contentProfile) {
                $socialLinks = $contentProfile->socialLinks()
                    ->ordered()
                    ->get();
            }

            // Datos para la vista
            $data = [
                'token' => $token,
                'dynamicContent' => $dynamicContent,
                'contentProfile' => $contentProfile,
                'contentMultimedia' => $contentMultimedia,
                'galleryImages' => $galleryImages,
                'socialLinks' => $socialLinks,
            ];

            return view('token.profile', $data);
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