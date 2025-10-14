<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DynamicContent extends Model
{
    use HasFactory;

    protected $table = 'dynamic_content';

    protected $fillable = [
        'content_id',
        'type',
        'gift_subtype',
        'tier',
        'title',
        'description',
        'data',
        'image_url',
        'is_active',
        'status',
        'published_at',
        'last_draft_update',
        'post_publish_modifications',
        'published_snapshot',
        'user_id',
        'nfc_token_id',
        
        // Referencias a tablas especializadas
        'multimedia_id',
        'gift_id',
        'menu_id',
        'profile_id',
        'event_id',
        'product_id',
        'tourist_id',
        'business_id',
        'bus_stop_id',
        'business_group_id',
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'last_draft_update' => 'datetime',
        'post_publish_modifications' => 'array',
        'published_snapshot' => 'array',
    ];

    // Constantes para tipos de contenido
    public const TYPE_GIFT = 'GIFT';
    public const TYPE_BUSINESS = 'BUSINESS'; // Incluye negocios y restaurantes
    public const TYPE_PROFILE = 'PROFILE';
    public const TYPE_TOURIST = 'TOURIST';
    public const TYPE_EVENT = 'EVENT';
    public const TYPE_PRODUCT = 'PRODUCT';
    
    // Constantes para tipos especializados
    public const TYPE_BUS_STOP = 'BUS_STOP';
    public const TYPE_BUSINESS_GROUP = 'BUSINESS_GROUP'; // Para agrupar múltiples negocios
    public const TYPE_PORTFOLIO = 'PORTFOLIO';
    public const TYPE_CONTACT = 'CONTACT';
    public const TYPE_MULTIMEDIA = 'MULTIMEDIA';
    public const TYPE_SOCIAL = 'SOCIAL';
    public const TYPE_REVIEW = 'REVIEW';
    public const TYPE_CUSTOM = 'CUSTOM';

    public const TYPES = [
        // Tipos activos (con recursos especializados)
        self::TYPE_GIFT => '🎁 Regalo Personalizado',
        self::TYPE_PROFILE => '👤 Perfil Personal', 
        self::TYPE_BUSINESS => '🏢 Negocio / Restaurante',
        self::TYPE_TOURIST => '🗺️ Información Turística',
        self::TYPE_EVENT => '📅 Evento',
        self::TYPE_PRODUCT => '📦 Producto',
        
        // Tipos especializados
        self::TYPE_BUS_STOP => '🚌 Paradero de Transporte',
        self::TYPE_BUSINESS_GROUP => '🏪 Grupo de Negocios',
        self::TYPE_PORTFOLIO => '🎨 Portafolio Creativo',
        self::TYPE_CONTACT => '📞 Información de Contacto',
        self::TYPE_MULTIMEDIA => '📱 Contenido Multimedia',
        self::TYPE_SOCIAL => '🌐 Redes Sociales',
        self::TYPE_REVIEW => '⭐ Reseñas y Testimonios',
        self::TYPE_CUSTOM => '⚙️ Contenido Personalizado',
    ];

    /**
     * Obtener solo los tipos que tienen recursos especializados implementados
     */
    public static function getActiveTypes(): array
    {
        return [
            self::TYPE_GIFT => self::TYPES[self::TYPE_GIFT],
            self::TYPE_PROFILE => self::TYPES[self::TYPE_PROFILE],
            self::TYPE_BUSINESS => self::TYPES[self::TYPE_BUSINESS],
            self::TYPE_TOURIST => self::TYPES[self::TYPE_TOURIST],
            self::TYPE_EVENT => self::TYPES[self::TYPE_EVENT],
            self::TYPE_PRODUCT => self::TYPES[self::TYPE_PRODUCT],
            self::TYPE_BUS_STOP => self::TYPES[self::TYPE_BUS_STOP],
            self::TYPE_BUSINESS_GROUP => self::TYPES[self::TYPE_BUSINESS_GROUP],
        ];
    }

    /**
     * Obtener tipos futuros que están listos para implementar
     */
    public static function getFutureTypes(): array
    {
        return [
            self::TYPE_PORTFOLIO => self::TYPES[self::TYPE_PORTFOLIO],
            self::TYPE_CONTACT => self::TYPES[self::TYPE_CONTACT],
            self::TYPE_MULTIMEDIA => self::TYPES[self::TYPE_MULTIMEDIA],
            self::TYPE_SOCIAL => self::TYPES[self::TYPE_SOCIAL],
            self::TYPE_REVIEW => self::TYPES[self::TYPE_REVIEW],
            self::TYPE_CUSTOM => self::TYPES[self::TYPE_CUSTOM],
        ];
    }

    /**
     * Relación con el usuario propietario del contenido
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el token NFC físico
     */
    public function nfcToken(): BelongsTo
    {
        return $this->belongsTo(NfcToken::class);
    }

    // ========================================
    // RELACIONES CON TABLAS NORMALIZADAS
    // ========================================

    /**
     * Relación con contenido multimedia
     */
    public function multimedia()
    {
        return $this->hasOne(ContentMultimedia::class);
    }

    /**
     * Relación con contenido gift
     */
    public function gift()
    {
        return $this->hasOne(ContentGift::class);
    }

    /**
     * Relación con contenido menu (DEPRECATED - usar business() en su lugar)
     * COMENTADO: La tabla content_menus fue eliminada
     */
    // public function menu()
    // {
    //     // DEPRECATED: MENU type migrated to BUSINESS type
    //     return $this->hasOne(ContentMenu::class);
    // }

    /**
     * Relación con contenido profile
     */
    public function profile()
    {
        return $this->hasOne(ContentProfile::class);
    }

    /**
     * Relación con enlaces sociales
     */
    public function socialLinks()
    {
        return $this->hasMany(ContentSocialLink::class);
    }

    /**
     * Relación con habilidades
     */
    public function skills()
    {
        return $this->hasMany(ContentSkill::class);
    }

    /**
     * Relación con contenido event
     */
    public function event()
    {
        return $this->hasOne(ContentEvent::class);
    }

    /**
     * Relación con contenido product
     */
    public function product()
    {
        return $this->hasOne(ContentProduct::class);
    }

    /**
     * Relación con contenido tourist
     */
    public function tourist()
    {
        return $this->hasOne(ContentTourist::class);
    }

    /**
     * Relación con contenido business
     */
    public function business()
    {
        return $this->hasOne(ContentBusiness::class);
    }

    /**
     * Relación con contenido bus stop
     */
    public function busStop()
    {
        return $this->hasOne(BusStop::class);
    }

    /**
     * Relación con contenido business group
     */
    public function businessGroup()
    {
        return $this->hasOne(ContentBusinessGroup::class);
    }

    // ========================================
    // MÉTODOS PARA SINCRONIZACIÓN DE REFERENCIAS
    // ========================================

    /**
     * Sincronizar referencias después de crear contenido especializado
     */
    public function syncReferences(): void
    {
        $updates = [];
        
        // Multimedia
        if ($this->multimedia) {
            $updates['multimedia_id'] = $this->multimedia->id;
        }
        
        // Tipo específico
        switch ($this->type) {
            case self::TYPE_GIFT:
                if ($this->gift) $updates['gift_id'] = $this->gift->id;
                break;
            // MENU type deprecated - now handled by BUSINESS type
            // case 'MENU': // DEPRECATED - tabla eliminada
            //     if ($this->menu) $updates['menu_id'] = $this->menu->id;
            //     break;
            case self::TYPE_PROFILE:
                if ($this->profile) $updates['profile_id'] = $this->profile->id;
                break;
            case self::TYPE_EVENT:
                if ($this->event) $updates['event_id'] = $this->event->id;
                break;
            case self::TYPE_PRODUCT:
                if ($this->product) $updates['product_id'] = $this->product->id;
                break;
            case self::TYPE_TOURIST:
                if ($this->tourist) $updates['tourist_id'] = $this->tourist->id;
                break;
            case self::TYPE_BUSINESS:
                if ($this->business) $updates['business_id'] = $this->business->id;
                break;
            case self::TYPE_BUS_STOP:
                if ($this->busStop) $updates['bus_stop_id'] = $this->busStop->id;
                break;
            case self::TYPE_BUSINESS_GROUP:
                if ($this->businessGroup) $updates['business_group_id'] = $this->businessGroup->id;
                break;
        }
        
        if (!empty($updates)) {
            $this->update($updates);
        }
    }

    /**
     * Crear o actualizar contenido multimedia
     */
    public function createOrUpdateMultimedia(array $data): ContentMultimedia
    {
        $multimedia = $this->multimedia ?? new ContentMultimedia(['dynamic_content_id' => $this->id]);
        $multimedia->fill($data);
        $multimedia->save();
        
        // Sincronizar referencia
        $this->update(['multimedia_id' => $multimedia->id]);
        
        return $multimedia;
    }

    /**
     * Crear o actualizar contenido gift
     */
    public function createOrUpdateGift(array $data): ContentGift
    {
        $gift = $this->gift ?? new ContentGift(['dynamic_content_id' => $this->id]);
        $gift->fill($data);
        $gift->save();
        
        // Sincronizar referencia
        $this->update(['gift_id' => $gift->id]);
        
        return $gift;
    }

    /**
     * Crear o actualizar contenido menu (DEPRECATED - usar createOrUpdateBusiness en su lugar)
     * COMENTADO: La tabla content_menus fue eliminada
     */
    // public function createOrUpdateMenu(array $data): ContentMenu
    // {
    //     // DEPRECATED: MENU type migrated to BUSINESS type
    //     $menu = $this->menu ?? new ContentMenu(['dynamic_content_id' => $this->id]);
    //     $menu->fill($data);
    //     $menu->save();
    //     
    //     // Sincronizar referencia
    //     $this->update(['menu_id' => $menu->id]);
    //     
    //     return $menu;
    // }

    /**
     * Crear o actualizar contenido profile
     */
    public function createOrUpdateProfile(array $data): ContentProfile
    {
        $profile = $this->profile ?? new ContentProfile(['dynamic_content_id' => $this->id]);
        $profile->fill($data);
        $profile->save();
        
        // Sincronizar referencia
        $this->update(['profile_id' => $profile->id]);
        
        return $profile;
    }

    /**
     * Crear o actualizar contenido event
     */
    public function createOrUpdateEvent(array $data): ContentEvent
    {
        $event = $this->event ?? new ContentEvent(['dynamic_content_id' => $this->id]);
        $event->fill($data);
        $event->save();
        
        // Sincronizar referencia
        $this->update(['event_id' => $event->id]);
        
        return $event;
    }

    /**
     * Crear o actualizar contenido product
     */
    public function createOrUpdateProduct(array $data): ContentProduct
    {
        $product = $this->product ?? new ContentProduct(['dynamic_content_id' => $this->id]);
        $product->fill($data);
        $product->save();
        
        // Sincronizar referencia
        $this->update(['product_id' => $product->id]);
        
        return $product;
    }

    /**
     * Crear o actualizar contenido tourist
     */
    public function createOrUpdateTourist(array $data): ContentTourist
    {
        $tourist = $this->tourist ?? new ContentTourist(['dynamic_content_id' => $this->id]);
        $tourist->fill($data);
        $tourist->save();
        
        // Sincronizar referencia
        $this->update(['tourist_id' => $tourist->id]);
        
        return $tourist;
    }

    /**
     * Crear o actualizar contenido business
     */
    public function createOrUpdateBusiness(array $data): ContentBusiness
    {
        $business = $this->business ?? new ContentBusiness(['dynamic_content_id' => $this->id]);
        $business->fill($data);
        $business->save();
        
        // Sincronizar referencia
        $this->update(['business_id' => $business->id]);
        
        return $business;
    }

    /**
     * Crear o actualizar contenido business group
     */
    public function createOrUpdateBusinessGroup(array $data): ContentBusinessGroup
    {
        $businessGroup = $this->businessGroup ?? new ContentBusinessGroup(['dynamic_content_id' => $this->id]);
        $businessGroup->fill($data);
        $businessGroup->save();
        
        // Sincronizar referencia
        $this->update(['business_group_id' => $businessGroup->id]);
        
        return $businessGroup;
    }

    // ========================================
    // MÉTODOS DE CONTENIDO
    // ========================================

    /**
     * Obtener colores del tipo
     */
    public static function getTypeColors(string $type): array
    {
        return match($type) {
            self::TYPE_GIFT => ['primary' => '#E91E63', 'secondary' => '#FCE4EC'],
            self::TYPE_BUSINESS => ['primary' => '#FF6B35', 'secondary' => '#FFF3E0'], // Incluye restaurantes
            self::TYPE_PROFILE => ['primary' => '#9C27B0', 'secondary' => '#F3E5F5'],
            self::TYPE_TOURIST => ['primary' => '#2196F3', 'secondary' => '#E3F2FD'],
            self::TYPE_EVENT => ['primary' => '#FF9800', 'secondary' => '#FFF3E0'],
            self::TYPE_PRODUCT => ['primary' => '#4CAF50', 'secondary' => '#E8F5E8'],
            self::TYPE_BUS_STOP => ['primary' => '#673AB7', 'secondary' => '#F3E5F5'],
            self::TYPE_BUSINESS_GROUP => ['primary' => '#795548', 'secondary' => '#EFEBE9'],
            default => ['primary' => '#607D8B', 'secondary' => '#ECEFF1'],
        };
    }

    /**
     * Obtener icono del tipo
     */
    public static function getTypeIcon(string $type): string
    {
        return match($type) {
            self::TYPE_GIFT => '🎁',
            self::TYPE_BUSINESS => '🏢', // Incluye restaurantes 🍽️
            self::TYPE_PROFILE => '👤',
            self::TYPE_TOURIST => '🗺️',
            self::TYPE_EVENT => '📅',
            self::TYPE_PRODUCT => '📦',
            self::TYPE_BUS_STOP => '🚌',
            self::TYPE_BUSINESS_GROUP => '🏪',
            default => '📄',
        };
    }

    /**
     * Buscar contenido activo por content_id
     */
    public static function findActiveByContentId(string $contentId, ?string $type = null): ?self
    {
        $query = self::where('content_id', $contentId)
                    ->where('is_active', true)
                    ->where('status', 'published');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->first();
    }

    /**
     * Obtener contenido por tipo
     */
    public static function getByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('type', $type)
                  ->where('is_active', true)
                  ->where('status', 'published')
                  ->orderBy('created_at', 'desc')
                  ->get();
    }

    /**
     * Generar content_id único usando UUID
     */
    public static function generateUniqueContentId(string $type): string
    {
        do {
            $contentId = Str::uuid()->toString();
        } while (self::where('content_id', $contentId)->exists());

        return $contentId;
    }

    /**
     * Obtener configuración de subtipos de regalo
     */
    public static function getGiftSubtypes(): array
    {
        return [
            'anniversary' => [
                'name' => 'Aniversario',
                'icon' => '💕',
                'color' => '#FF6B9D',
                'description' => 'Celebra momentos especiales juntos',
            ],
            'birthday' => [
                'name' => 'Cumpleaños',
                'icon' => '🎂',
                'color' => '#FFD93D',
                'description' => 'Un día especial para celebrar',
            ],
            'graduation' => [
                'name' => 'Graduación',
                'icon' => '🎓',
                'color' => '#6BCF7F',
                'description' => 'Logros académicos importantes',
            ],
            'wedding' => [
                'name' => 'Boda',
                'icon' => '💒',
                'color' => '#FF9999',
                'description' => 'El día más especial',
            ],
            'valentine' => [
                'name' => 'San Valentín',
                'icon' => '❤️',
                'color' => '#FF1744',
                'description' => 'Amor y romance',
            ],
            'mother_day' => [
                'name' => 'Día de la Madre',
                'icon' => '🌸',
                'color' => '#FF80AB',
                'description' => 'Para la persona más especial',
            ],
            'father_day' => [
                'name' => 'Día del Padre',
                'icon' => '👔',
                'color' => '#64B5F6',
                'description' => 'Honrando al héroe de casa',
            ],
            'christmas' => [
                'name' => 'Navidad',
                'icon' => '🎄',
                'color' => '#4CAF50',
                'description' => 'Época de dar y compartir',
            ],
            'general' => [
                'name' => 'General',
                'icon' => '🎁',
                'color' => '#9C27B0',
                'description' => 'Para cualquier ocasión especial',
            ],
        ];
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Obtener nombre del tipo en español
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Desconocido';
    }

    /**
     * Obtener colores del tipo
     */
    public function getColorsAttribute(): array
    {
        return self::getTypeColors($this->type);
    }

    /**
     * Obtener icono del tipo
     */
    public function getIconAttribute(): string
    {
        return self::getTypeIcon($this->type);
    }

    /**
     * Obtener items del menú como colección (para negocios tipo restaurante)
     * @deprecated Use ContentBusiness->directProducts() instead
     */
    public function getDishesAttribute()
    {
        // Método deprecado - usar ContentBusiness->directProducts() en su lugar
        if ($this->type !== self::TYPE_BUSINESS) {
            return collect([]);
        }

        // Intentar obtener productos de la relación business
        if ($this->business && $this->business->isRestaurant()) {
            return $this->business->directProducts;
        }

        return collect([]);
    }

    /**
     * Obtener datos de multimedia con retrocompatibilidad
     */
    public function getMultimediaDataAttribute()
    {
        $multimedia = $this->multimedia;
        
        return [
            'video' => array_merge(
                ['url' => $multimedia?->video_url, 'type' => $multimedia?->video_type],
                $this->data['multimedia']['video'] ?? []
            ),
            'audio' => array_merge(
                ['url' => $multimedia?->audio_url, 'type' => $multimedia?->audio_type],
                $this->data['multimedia']['audio'] ?? []
            ),
            'gallery' => $multimedia?->gallery_images ?? $this->data['multimedia']['gallery'] ?? [],
            'design' => $this->data['multimedia']['design'] ?? []
        ];
    }

    /**
     * Obtener nombre del remitente con retrocompatibilidad
     */
    public function getSenderDisplayNameAttribute()
    {
        return $this->gift?->sender_name ?? $this->data['from'] ?? null;
    }

    /**
     * Obtener nombre del destinatario con retrocompatibilidad
     */
    public function getRecipientDisplayNameAttribute()
    {
        return $this->gift?->recipient_name ?? $this->data['to'] ?? null;
    }

    /**
     * Obtener mensaje con retrocompatibilidad
     */
    public function getDisplayMessageAttribute()
    {
        return $this->gift?->message ?? $this->data['love_message'] ?? $this->data['gift_message'] ?? null;
    }

    // ========================================
    // MÉTODOS PARA GESTIÓN DE ESTADOS
    // ========================================

    /**
     * Verificar si está en modo borrador
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Verificar si está publicado
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Verificar si está pausado
     */
    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    /**
     * Verificar si es accesible públicamente
     */
    public function isPubliclyAccessible(): bool
    {
        return $this->isPublished() && $this->is_active;
    }

    /**
     * Publicar el contenido
     */
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
            'published_snapshot' => $this->createSnapshot(),
            'is_active' => true
        ]);
    }

    /**
     * Pausar el contenido
     */
    public function pause(): void
    {
        $this->update([
            'status' => 'paused'
        ]);
    }

    /**
     * Actualizar en modo borrador
     */
    public function updateDraft(array $data): void
    {
        $data['last_draft_update'] = now();
        
        // Si está publicado, contar como modificación post-publicación
        if ($this->isPublished()) {
            $data['post_publish_modifications'] = $this->post_publish_modifications + 1;
        }
        
        $this->update($data);
    }

    /**
     * Verificar si puede modificar
     */
    public function canModify(): bool
    {
        // En borrador: modificaciones ilimitadas
        if ($this->isDraft()) {
            return true;
        }

        // Publicado: verificar límites según tier
        return $this->getRemainingModifications() > 0;
    }

    /**
     * Obtener modificaciones restantes
     */
    public function getRemainingModifications(): int
    {
        if ($this->isDraft()) {
            return 999; // Ilimitado en borrador
        }

        // Modelo simplificado: una vez publicado, no se puede modificar
        return 0;
    }

    /**
     * Crear snapshot del contenido actual
     */
    private function createSnapshot(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'data' => $this->data,
            'image_url' => $this->image_url,
            'created_at' => now()->toISOString()
        ];
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope para contenido público
     */
    public function scopePublic($query)
    {
        return $query->where('status', 'published')
                    ->where('is_active', true);
    }

    /**
     * Scope para borradores
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope por tipo
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}