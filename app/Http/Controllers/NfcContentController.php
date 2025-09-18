<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNfcAccountRequest;
use App\Models\DynamicContent;
use App\Models\NfcAnalytic;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

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
     * Mostrar contenido NFC por ID usando formato limpio /nfc/{id}
     * Detecta automáticamente si es content_id o token_id
     */
    public function showById(string $id): View|Response|RedirectResponse
    {
        // Primero intentar como content_id (UUID formato completo)
        if (preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $id)) {
            $content = DynamicContent::findActiveByContentId($id);
            
            if ($content) {
                // Registrar el acceso en analytics
                NfcAnalytic::recordAccess(
                    $id,
                    $content->type,
                    $content->nfc_token_id
                );

                // Seleccionar vista según el tipo de contenido
                $viewName = $this->getViewForContentType($content->type);
                
                // Preparar datos adicionales según el tipo de contenido
                $additionalData = $this->prepareViewData($content);
                
                return view($viewName, array_merge(compact('content'), $additionalData));
            }
        }
        
        // Intentar como token_id
        $token = NfcToken::findActiveByTokenId($id);
        
        if ($token && $token->hasContent()) {
            $content = $token->dynamicContent;
            
            if ($content->isPubliclyAccessible()) {
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
        }
        
        // Si llegamos aquí, buscar si existe un token sin contenido para onboarding
        $token = NfcToken::where('token_id', $id)->first();
        
        if ($token && !$token->user_id) {
            // Chip existe pero no está asignado - mostrar onboarding
            return $this->showOnboarding($token->content_type, $id);
        }
        
        if ($token && !$token->hasContent()) {
            // Chip asignado pero sin contenido
            return $this->showContentNotAvailable($token->content_type, $id, $token, 'Contenido no configurado');
        }
        
        // No encontrado
        abort(404, 'Contenido no encontrado');
    }

    /**
     * RETROCOMPATIBILIDAD: Manejar formato antiguo /nfc?TYPE=X&ID=uuid
     * Incluye sistema de onboarding para chips sin configurar
     */
    public function showLegacy(Request $request): View|Response|RedirectResponse
    {
        $type = strtoupper($request->get('TYPE', ''));
        $id = $request->get('ID', '');
        
        if (!$id) {
            return view('nfc.error', [
                'message' => 'URL inválida. Falta el parámetro ID.',
                'suggestions' => [
                    'Verifica que la URL esté completa',
                    'Asegúrate de escanear el chip correctamente',
                    'Contacta al administrador si el problema persiste'
                ]
            ]);
        }
        
        // Buscar token NFC - primero con TYPE si se proporciona, sino solo por ID
        $token = null;
        if ($type) {
            $token = NfcToken::where('token_id', $id)
                            ->where('content_type', $type)
                            ->first();
        } else {
            // Si no se proporciona TYPE, buscar solo por ID (para tokens existentes)
            $token = NfcToken::where('token_id', $id)->first();
            if ($token) {
                $type = $token->content_type; // Obtener el TYPE desde la BD
            }
        }
        
        // Si no hay token, mostrar error
        if (!$token) {
            return view('nfc.error', [
                'message' => 'Chip NFC no encontrado',
                'suggestions' => [
                    'Verifica que el ID del chip esté correcto: ' . $id,
                    'Asegúrate de escanear el chip NFC correctamente',
                    'Si el problema persiste, contacta al administrador',
                    $type ? 'Tipo especificado: ' . $type : 'No se especificó TYPE en la URL'
                ]
            ]);
        }
        
        // Si el token no está asignado a ningún usuario, mostrar onboarding
        if (!$token->user_id) {
            return $this->showOnboarding($type, $id);
        }
        
        // Si el chip existe pero está DESACTIVADO
        if (!$token->is_active) {
            return $this->showContentNotAvailable($type, $id, $token, 'Chip desactivado');
        }
        
        // Buscar contenido a través de la relación
        $content = $token->dynamicContent;
        
        // Si no hay contenido o no está publicado, mostrar mensaje apropiado
        if (!$content || !$content->isPubliclyAccessible()) {
            // Si hay contenido pero está en borrador, mostrar mensaje especial
            if ($content && $content->isDraft()) {
                return view('nfc.draft-preview', [
                    'type' => $type,
                    'id' => $id,
                    'message' => 'El contenido aún está en preparación... ¡Pronto estará listo! 🎁'
                ]);
            }
            
            return $this->showContentNotAvailable($type, $id, $token, 'Contenido no configurado');
        }
        
        // Registrar el acceso en analytics
        NfcAnalytic::recordAccess($id, $content->type, $token->id);
        
        // Actualizar último uso del token
        $token->updateLastUsed();
        
        // Seleccionar vista según el tipo de contenido
        $viewName = $this->getViewForContentType($content->type);
        
        // Preparar datos adicionales según el tipo de contenido
        $additionalData = $this->prepareViewData($content);
        
        return view($viewName, array_merge(compact('content', 'token'), $additionalData));
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
                
                unset($multimedia, $gift, $multimediaData);
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
                
                unset($menu);
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
                
                unset($profile);
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

    // ========================================
    // SISTEMA DE ONBOARDING
    // ========================================

    /**
     * Mostrar pantalla de onboarding cuando no hay contenido configurado
     */
    private function showOnboarding(string $type, string $id): View
    {
        $typeConfig = [
            'GIFT' => [
                'title' => 'Regalo Especial',
                'icon' => 'fas fa-gift',
                'color' => 'pink',
                'gradient' => 'from-pink-50 to-red-50',
                'border' => 'border-pink-200',
                'primary' => 'text-pink-600',
                'message' => 'Este es un regalo especial hecho con amor',
                'emoji' => '🎁💕'
            ],
            'MENU' => [
                'title' => 'Menú Digital',
                'icon' => 'fas fa-utensils',
                'color' => 'orange',
                'gradient' => 'from-orange-50 to-yellow-50',
                'border' => 'border-orange-200',
                'primary' => 'text-orange-600',
                'message' => 'Descubre nuestra deliciosa carta',
                'emoji' => '🍽️👨‍🍳'
            ],
            'PROFILE' => [
                'title' => 'Perfil Profesional',
                'icon' => 'fas fa-user',
                'color' => 'indigo',
                'gradient' => 'from-indigo-50 to-blue-50',
                'border' => 'border-indigo-200',
                'primary' => 'text-indigo-600',
                'message' => 'Conecta conmigo profesionalmente',
                'emoji' => '👤💼'
            ],
            'EVENT' => [
                'title' => 'Evento Especial',
                'icon' => 'fas fa-calendar-alt',
                'color' => 'green',
                'gradient' => 'from-green-50 to-emerald-50',
                'border' => 'border-green-200',
                'primary' => 'text-green-600',
                'message' => 'Un evento que no te puedes perder',
                'emoji' => '🎉📅'
            ],
            'TOURIST' => [
                'title' => 'Guía Turística',
                'icon' => 'fas fa-map-marked-alt',
                'color' => 'blue',
                'gradient' => 'from-blue-50 to-cyan-50',
                'border' => 'border-blue-200',
                'primary' => 'text-blue-600',
                'message' => 'Descubre lugares increíbles',
                'emoji' => '🗺️✈️'
            ],
            'PRODUCT' => [
                'title' => 'Producto Especial',
                'icon' => 'fas fa-shopping-cart',
                'color' => 'yellow',
                'gradient' => 'from-yellow-50 to-amber-50',
                'border' => 'border-yellow-200',
                'primary' => 'text-yellow-600',
                'message' => 'Un producto que te va a encantar',
                'emoji' => '🛍️✨'
            ]
        ];
        
        $config = $typeConfig[$type] ?? $typeConfig['GIFT'];
        
        return view('nfc.onboarding', [
            'type' => $type,
            'id' => $id,
            'config' => $config
        ]);
    }

    /**
     * Mostrar pantalla cuando el contenido no está disponible
     */
    private function showContentNotAvailable(string $type, string $id, ?NfcToken $token, string $reason = 'Contenido no disponible'): View
    {
        return view('nfc.content-not-available', [
            'type' => $type,
            'id' => $id,
            'token' => $token,
            'reason' => $reason
        ]);
    }

    /**
     * Mostrar formulario de onboarding con formato limpio /nfc/onboarding/{id}
     */
    public function onboardingById(string $id): View|RedirectResponse
    {
        // Buscar el token para obtener el TYPE
        $token = NfcToken::where('token_id', $id)->first();
        
        if (!$token) {
            // Si no existe el token, mostrar página de error amigable
            return view('nfc.error', [
                'message' => 'Chip NFC no encontrado',
                'suggestions' => [
                    'Verifica que el ID del chip esté correcto',
                    'Asegúrate de escanear el chip NFC correctamente',
                    'Si el problema persiste, contacta al administrador',
                    'Puedes intentar acceder usando el formato: /nfc?TYPE=GIFT&ID=' . $id
                ]
            ]);
        }
        
        $type = $token->content_type;
        
        return view('nfc.onboarding-form', [
            'type' => $type,
            'id' => $id
        ]);
    }

    /**
     * Mostrar formulario de onboarding
     */
    public function onboarding(Request $request): View|RedirectResponse
    {
        $type = strtoupper($request->get('TYPE', ''));
        $id = $request->get('ID');
        
        if (!$id) {
            return redirect()->route('nfc.legacy');
        }
        
        // Si no se proporciona TYPE, intentar obtenerlo desde la BD
        if (!$type) {
            $token = NfcToken::where('token_id', $id)->first();
            if ($token) {
                $type = $token->content_type;
            } else {
                // Si no encontramos el token y no tenemos TYPE, redirigir con error
                return redirect()->route('nfc.legacy')->with('error', 'Chip no encontrado. Se requiere TYPE para chips nuevos.');
            }
        }
        
        return view('nfc.onboarding-form', [
            'type' => $type,
            'id' => $id
        ]);
    }

    /**
     * Crear cuenta de usuario a través del onboarding
     */
    public function createAccount(CreateNfcAccountRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Verificar si el usuario ya existe
        $existingUser = User::where('email', $validated['email'])->first();
        
        if ($existingUser) {
            // Usuario existe - asignar nuevo chip a cuenta existente
            return $this->assignTokenToExistingUser($existingUser, $validated);
        }
        
        // Crear nuevo usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);
        
        // Asignar rol por defecto para usuarios NFC
        $this->assignDefaultNfcRole($user);
        
        // Asignar chip automáticamente
        $token = NfcToken::where('token_id', $validated['id'])
                         ->where('content_type', $validated['type'])
                         ->whereNull('user_id')
                         ->first();
        
        if ($token) {
            $token->update([
                'user_id' => $user->id,
                'purchased_at' => now(),
                'purchase_notes' => 'Asignado vía onboarding - primera compra'
            ]);
        }
        
        // Crear contenido básico en modo borrador
        $content = DynamicContent::create([
            'content_id' => $validated['id'],
            'type' => $validated['type'],
            'title' => 'Mi ' . ucfirst(strtolower($validated['type'])),
            'description' => "Contenido personalizado",
            'data' => $this->getDefaultDataForType($validated['type']),
            'status' => 'draft', // Comienza como borrador
            'is_active' => false, // Inactivo hasta que se publique
            'user_id' => $user->id,
            'nfc_token_id' => $token->id ?? null
        ]);
        
        // Login automático
        Auth::login($user);
        
        // Redirigir al dashboard con mensaje de éxito
        return redirect('/admin')
                        ->with('success', '¡Cuenta creada! Ahora personaliza tu contenido desde el panel de administración.');
    }

    /**
     * Asignar token a usuario autenticado (flujo simple)
     */
    public function assignTokenToAuthenticatedUser(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|in:GIFT,MENU,PROFILE,EVENT,TOURIST,PRODUCT',
            'id' => 'required|string|max:255',
        ]);
        
        $user = Auth::user();
        
        // Buscar chip disponible
        $token = NfcToken::where('token_id', $request->id)
                         ->where('content_type', $request->type)
                         ->whereNull('user_id')
                         ->first();
        
        if (!$token) {
            return redirect()->back()
                           ->withErrors(['error' => 'Este chip no está disponible para asignación.'])
                           ->withInput();
        }
        
        // Asignar chip al usuario
        $token->update([
            'user_id' => $user->id,
            'purchased_at' => now(),
            'purchase_notes' => 'Asignado a usuario autenticado'
        ]);
        
        // Crear contenido básico en modo borrador
        $content = DynamicContent::create([
            'content_id' => $request->id,
            'type' => $request->type,
            'title' => 'Mi ' . ucfirst(strtolower($request->type)) . ' #' . ($user->dynamicContents()->count() + 1),
            'description' => "Contenido personalizado",
            'data' => $this->getDefaultDataForType($request->type),
            'status' => 'draft', // Comienza como borrador
            'is_active' => false, // Inactivo hasta que se publique
            'user_id' => $user->id,
            'nfc_token_id' => $token->id
        ]);
        
        // Redirigir al dashboard con mensaje de éxito
        return redirect('/admin')
                        ->with('success', '¡Chip asignado a tu cuenta! Ahora personaliza tu contenido desde el panel de administración.');
    }

    /**
     * Asignar nuevo chip a usuario existente
     */
    private function assignTokenToExistingUser(User $user, array $validated): RedirectResponse
    {
        // Buscar chip disponible
        $token = NfcToken::where('token_id', $validated['id'])
                         ->where('content_type', $validated['type'])
                         ->whereNull('user_id')
                         ->first();
        
        if (!$token) {
            return redirect()->back()
                           ->withErrors(['error' => 'Este chip no está disponible para asignación.'])
                           ->withInput();
        }
        
        // Asignar chip al usuario existente
        $token->update([
            'user_id' => $user->id,
            'purchased_at' => now(),
            'purchase_notes' => 'Asignado vía onboarding - usuario existente'
        ]);
        
        // Crear contenido básico en modo borrador
        $content = DynamicContent::create([
            'content_id' => $validated['id'],
            'type' => $validated['type'],
            'title' => 'Mi ' . ucfirst(strtolower($validated['type'])) . ' #' . ($user->dynamicContents()->count() + 1),
            'description' => "Contenido personalizado",
            'data' => $this->getDefaultDataForType($validated['type']),
            'status' => 'draft', // Comienza como borrador
            'is_active' => false, // Inactivo hasta que se publique
            'user_id' => $user->id,
            'nfc_token_id' => $token->id
        ]);
        
        // Asignar rol por defecto si el usuario no tiene roles
        if ($user->roles()->count() === 0) {
            $this->assignDefaultNfcRole($user);
        }
        
        // Login automático
        Auth::login($user);
        
        // Redirigir al dashboard con mensaje de éxito
        return redirect('/admin')
                        ->with('success', '¡Nuevo chip asignado a tu cuenta! Ahora personaliza tu contenido desde el panel de administración.');
    }

    /**
     * Obtener datos por defecto según el tipo de contenido
     */
    private function getDefaultDataForType(string $type): array
    {
        return match($type) {
            'GIFT' => [
                'from' => 'Tu nombre',
                'to' => 'Persona especial',
                'love_message' => 'Un regalo especial hecho con amor'
            ],
            'MENU' => [
                'restaurant_info' => [
                    'address' => 'Av. Principal 123, Santiago',
                    'phone' => '+56 9 1234 5678',
                    'hours' => 'Lun-Dom 12:00-22:00',
                    'whatsapp' => '+56 9 1234 5678'
                ],
                'menu_items' => []
            ],
            'PROFILE' => [
                'bio' => 'Tu biografía profesional',
                'location' => 'Tu ubicación',
                'job_title' => 'Tu cargo',
                'company' => 'Tu empresa'
            ],
            'EVENT' => [
                'event_info' => [
                    'date' => 'Fecha del evento',
                    'time' => 'Hora del evento',
                    'location' => 'Lugar del evento'
                ],
                'contact' => [
                    'organizer' => 'Organizador',
                    'phone' => 'Teléfono de contacto'
                ]
            ],
            'TOURIST' => [
                'location_name' => 'Nombre del lugar',
                'description' => 'Descripción turística',
                'highlights' => ['Punto destacado 1', 'Punto destacado 2']
            ],
            'PRODUCT' => [
                'product_name' => 'Nombre del producto',
                'description' => 'Descripción del producto',
                'price' => 'Precio',
                'features' => ['Característica 1', 'Característica 2']
            ],
            default => []
        };
    }

    /**
     * Asignar rol por defecto a usuarios creados vía onboarding NFC
     */
    private function assignDefaultNfcRole(User $user): void
    {
        try {
            // Asignar rol "NFC" específico para usuarios registrados vía onboarding
            $nfcRole = \Spatie\Permission\Models\Role::where('name', 'NFC')->first();
            
            if ($nfcRole) {
                $user->assignRole($nfcRole);
                \Log::info('Rol NFC asignado a usuario', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => 'NFC'
                ]);
            } else {
                // Fallback: Si no existe el rol NFC, crear uno temporal
                \Log::warning('Rol NFC no encontrado, creando fallback');
                
                $fallbackRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'NFC']);
                
                // Asignar permisos básicos si es un rol nuevo
                if ($fallbackRole->wasRecentlyCreated) {
                    $basicPermissions = \Spatie\Permission\Models\Permission::whereIn('name', [
                        'access_admin_panel'
                    ])->get();
                    
                    if ($basicPermissions->isNotEmpty()) {
                        $fallbackRole->syncPermissions($basicPermissions);
                    }
                }
                
                $user->assignRole($fallbackRole);
            }
        } catch (\Exception $e) {
            // Si hay algún problema con los roles, registrar el error pero continuar
            // No queremos que falle la creación del usuario por problemas de permisos
            \Log::error('Error asignando rol NFC a usuario', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }
}