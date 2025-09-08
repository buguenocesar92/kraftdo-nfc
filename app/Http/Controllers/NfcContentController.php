<?php

namespace App\Http\Controllers;

use App\Models\DynamicContent;
use App\Models\NfcAnalytic;
use App\Models\NfcToken;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;

class NfcContentController extends Controller
{
    /**
     * Mostrar contenido NFC público por content_id
     */
    public function show(string $contentId): View|Response
    {
        // Buscar contenido activo y público
        $content = DynamicContent::findActiveByContentId($contentId);
        
        if (!$content) {
            abort(404, 'Contenido no encontrado o no disponible');
        }

        // Registrar el acceso en analytics
        NfcAnalytic::recordAccess(
            $contentId,
            $content->type,
            $content->nfc_token_id
        );

        // Seleccionar vista según el tipo de contenido
        $viewName = $this->getViewForContentType($content->type);
        
        // Preparar datos adicionales según el tipo de contenido
        $additionalData = $this->prepareViewData($content);
        
        return view($viewName, array_merge(compact('content'), $additionalData));
    }

    /**
     * RETROCOMPATIBILIDAD: Manejar formato antiguo /nfc?TYPE=X&ID=uuid
     */
    public function showLegacy(Request $request): View|Response|\Illuminate\Http\RedirectResponse
    {
        $type = $request->get('TYPE');
        $id = $request->get('ID');
        
        if (!$type || !$id) {
            abort(404, 'Parámetros requeridos: TYPE e ID');
        }
        
        // Validar que el ID sea un UUID válido
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $id)) {
            abort(404, 'ID no válido');
        }
        
        // Buscar contenido por content_id (UUID)
        $content = DynamicContent::findActiveByContentId($id);
        
        if (!$content) {
            abort(404, 'Contenido no encontrado o no disponible');
        }
        
        // Registrar el acceso en analytics
        NfcAnalytic::recordAccess(
            $id,
            $content->type,
            $content->nfc_token_id
        );
        
        // Actualizar último uso del token si existe
        if ($content->nfcToken) {
            $content->nfcToken->updateLastUsed();
        }
        
        // Seleccionar vista según el tipo de contenido
        $viewName = $this->getViewForContentType($content->type);
        
        // Preparar datos adicionales según el tipo de contenido
        $additionalData = $this->prepareViewData($content);
        
        return view($viewName, array_merge(compact('content'), $additionalData));
    }

    /**
     * Mostrar contenido NFC por token_id físico
     */
    public function showByToken(string $tokenId): View|Response
    {
        // Buscar token activo
        $token = NfcToken::findActiveByTokenId($tokenId);
        
        if (!$token || !$token->hasContent()) {
            abort(404, 'Token no encontrado o sin contenido configurado');
        }

        $content = $token->dynamicContent;
        
        if (!$content->isPubliclyAccessible()) {
            abort(404, 'Contenido no disponible públicamente');
        }

        // Registrar el acceso en analytics
        NfcAnalytic::recordAccess(
            $content->content_id,
            $content->type,
            $token->id
        );

        // Actualizar último uso del token
        $token->updateLastUsed();

        // Seleccionar vista según el tipo de contenido
        $viewName = $this->getViewForContentType($content->type);
        
        // Preparar datos adicionales según el tipo de contenido
        $additionalData = $this->prepareViewData($content);
        
        return view($viewName, array_merge(compact('content', 'token'), $additionalData));
    }

    /**
     * Vista previa de contenido (solo para propietarios)
     */
    public function preview(string $contentId): View|Response
    {
        $content = DynamicContent::where('content_id', $contentId)->firstOrFail();
        
        // Verificar que el usuario sea el propietario del contenido
        if (auth()->id() !== $content->user_id) {
            abort(403, 'No tienes permisos para ver este contenido');
        }

        $viewName = $this->getViewForContentType($content->type);
        $isPreview = true;
        
        // Preparar datos adicionales según el tipo de contenido
        $additionalData = $this->prepareViewData($content);
        
        return view($viewName, array_merge(compact('content', 'isPreview'), $additionalData));
    }

    /**
     * API para obtener estadísticas de un contenido
     */
    public function getStats(string $contentId): \Illuminate\Http\JsonResponse
    {
        $content = DynamicContent::where('content_id', $contentId)->firstOrFail();
        
        // Verificar que el usuario sea el propietario
        if (auth()->id() !== $content->user_id) {
            abort(403, 'No tienes permisos para ver estas estadísticas');
        }

        $stats = NfcAnalytic::getStatsForContent($contentId);
        
        return response()->json($stats);
    }

    /**
     * Obtener la vista apropiada según el tipo de contenido
     */
    private function getViewForContentType(string $type): string
    {
        return match($type) {
            DynamicContent::TYPE_GIFT => 'nfc.gift',
            DynamicContent::TYPE_MENU => 'nfc.menu',
            DynamicContent::TYPE_PROFILE => 'nfc.profile',
            DynamicContent::TYPE_TOURIST => 'nfc.tourist',
            DynamicContent::TYPE_EVENT => 'nfc.event',
            DynamicContent::TYPE_PRODUCT => 'nfc.product',
            default => 'nfc.default',
        };
    }

    /**
     * Preparar datos adicionales para las vistas según el tipo de contenido
     */
    private function prepareViewData(DynamicContent $content): array
    {
        $data = [];
        
        switch ($content->type) {
            case DynamicContent::TYPE_GIFT:
                // Usar tablas normalizadas con fallback a campos temporales
                $multimedia = $content->multimedia;
                $gift = $content->gift;
                
                if ($multimedia) {
                    // Usar datos de tabla normalizada
                    $multimediaData = [
                        'video' => $multimedia->video_config,
                        'audio' => $multimedia->audio_config,
                        'gallery' => $multimedia->gallery_images ?? [],
                        'design' => $multimedia->settings['design'] ?? $content->data['multimedia']['design'] ?? []
                    ];
                } else {
                    // Fallback a campos temporales para retrocompatibilidad
                    $multimediaData = [
                        'video' => array_merge(
                            ['url' => $content->video_url, 'type' => $content->video_type],
                            $content->data['multimedia']['video'] ?? []
                        ),
                        'audio' => array_merge(
                            ['url' => $content->audio_url, 'type' => $content->audio_type],
                            $content->data['multimedia']['audio'] ?? []
                        ),
                        'gallery' => $content->gallery_images ?? $content->data['multimedia']['gallery'] ?? [],
                        'design' => $content->data['multimedia']['design'] ?? []
                    ];
                }
                
                $data['multimedia'] = $multimediaData;
                $data['theme'] = $this->getGiftTheme($content);
                $data['currentSubtype'] = DynamicContent::getGiftSubtypes()[$content->gift_subtype] ?? [];
                
                // Datos de regalo
                if ($gift) {
                    $data['sender_name'] = $gift->sender_name;
                    $data['recipient_name'] = $gift->recipient_name;
                    $data['message'] = $gift->message;
                } else {
                    // Fallback
                    $data['sender_name'] = $content->sender_name ?? $content->data['from'] ?? null;
                    $data['recipient_name'] = $content->recipient_name ?? $content->data['to'] ?? null;
                    $data['message'] = $content->message ?? $content->data['love_message'] ?? null;
                }
                
                // Configuración JavaScript
                $data['jsConfig'] = [
                    'audio' => $multimediaData['audio'],
                    'video' => $multimediaData['video'],
                    'gallery' => $multimediaData['gallery'],
                    'theme' => [
                        'primary_gradient' => 'from-pink-600 to-purple-600',
                        'accent_color' => 'text-pink-600'
                    ]
                ];
                break;
                
            case DynamicContent::TYPE_MENU:
                // Usar tabla normalizada
                $menu = $content->menu;
                if ($menu) {
                    $data['restaurant_info'] = $menu->restaurant_info;
                    $data['menu_items'] = $menu->menuItems->map(function ($item) {
                        return [
                            'name' => $item->name,
                            'description' => $item->description,
                            'price' => $item->price,
                            'currency' => $item->currency,
                            'category' => $item->category,
                            'image' => $item->image_url,
                            'available' => $item->available,
                        ];
                    })->toArray();
                } else {
                    // Fallback
                    $data['restaurant_info'] = [
                        'phone' => $content->restaurant_phone,
                        'address' => $content->restaurant_address,
                        'hours' => $content->restaurant_hours,
                    ];
                    $data['menu_items'] = $content->menu_items ?? $content->data['menu_items'] ?? [];
                }
                break;
                
            case DynamicContent::TYPE_PROFILE:
                // Usar tabla normalizada
                $profile = $content->profile;
                if ($profile) {
                    $data['contact_info'] = $profile->contact_info;
                    $data['bio'] = $profile->bio;
                } else {
                    // Fallback
                    $data['contact_info'] = [
                        'email' => $content->contact_email,
                        'phone' => $content->contact_phone,
                        'website' => $content->contact_website,
                    ];
                    $data['bio'] = $content->bio ?? $content->data['bio'] ?? '';
                }
                
                // Enlaces sociales
                $data['social_links'] = $content->socialLinks->ordered()->map(function ($link) {
                    return [
                        'platform' => $link->platform,
                        'url' => $link->url,
                        'username' => $link->username,
                        'icon' => $link->platform_icon,
                        'color' => $link->platform_color,
                    ];
                })->toArray();
                
                // Habilidades agrupadas por categoría
                $data['skills'] = $content->skills->ordered()->groupBy('category')->map(function ($skills) {
                    return $skills->map(function ($skill) {
                        return [
                            'name' => $skill->name,
                            'level' => $skill->level,
                            'level_percentage' => $skill->level_percentage,
                            'level_description' => $skill->level_description,
                        ];
                    });
                })->toArray();
                break;
        }
        
        return $data;
    }

    /**
     * Obtener tema para contenido de tipo GIFT
     */
    private function getGiftTheme(DynamicContent $content): array
    {
        $subtypes = DynamicContent::getGiftSubtypes();
        $subtype = $subtypes[$content->gift_subtype] ?? $subtypes['general'];
        
        return [
            'primary_color' => $subtype['color'],
            'icon' => $subtype['icon'],
            'name' => $subtype['name'],
        ];
    }

    /**
     * Página de información sobre NFC
     */
    public function info(): View
    {
        return view('nfc.info');
    }

    /**
     * Endpoint para validar si un content_id existe y está activo
     */
    public function validateContent(string $contentId): \Illuminate\Http\JsonResponse
    {
        $content = DynamicContent::findActiveByContentId($contentId);
        
        return response()->json([
            'exists' => !is_null($content),
            'type' => $content?->type,
            'title' => $content?->title,
        ]);
    }

    /**
     * Endpoint para validar si un token_id existe y está activo
     */
    public function validateToken(string $tokenId): \Illuminate\Http\JsonResponse
    {
        $token = NfcToken::findActiveByTokenId($tokenId);
        
        return response()->json([
            'exists' => !is_null($token),
            'has_content' => $token?->hasContent() ?? false,
            'content_ready' => $token?->isContentReady() ?? false,
            'content_type' => $token?->content_type,
        ]);
    }
}