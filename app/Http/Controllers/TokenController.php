<?php

namespace App\Http\Controllers;

use App\Models\NfcToken;
use App\Models\ContentGift;
use App\Models\ContentMultimedia;
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

        // Por ahora solo manejamos tokens de tipo GIFT
        if ($token->content_type !== 'GIFT') {
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

        // Datos para la vista
        $data = [
            'token' => $token,
            'dynamicContent' => $dynamicContent,
            'contentGift' => $contentGift,
            'contentMultimedia' => $contentMultimedia,
            'galleryImages' => $galleryImages,
        ];

        // Determinar la vista basada en el tipo de contenido
        return view('token.gift', $data);
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