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
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'last_draft_update' => 'datetime',
        'published_snapshot' => 'array',
    ];

    // Constantes para tipos de contenido
    public const TYPE_MENU = 'MENU';
    public const TYPE_GIFT = 'GIFT';
    public const TYPE_TOURIST = 'TOURIST';
    public const TYPE_PROFILE = 'PROFILE';
    public const TYPE_EVENT = 'EVENT';
    public const TYPE_PRODUCT = 'PRODUCT';

    public const TYPES = [
        self::TYPE_MENU => 'Menú de Restaurante',
        self::TYPE_GIFT => 'Regalo Personalizado',
        self::TYPE_TOURIST => 'Información Turística',
        self::TYPE_PROFILE => 'Perfil Personal',
        self::TYPE_EVENT => 'Evento',
        self::TYPE_PRODUCT => 'Producto',
    ];

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
    // MÉTODOS DE CONTENIDO
    // ========================================

    /**
     * Obtener colores del tipo
     */
    public static function getTypeColors(string $type): array
    {
        return match($type) {
            self::TYPE_MENU => ['primary' => '#FF6B35', 'secondary' => '#FFF3E0'],
            self::TYPE_GIFT => ['primary' => '#E91E63', 'secondary' => '#FCE4EC'],
            self::TYPE_TOURIST => ['primary' => '#2196F3', 'secondary' => '#E3F2FD'],
            self::TYPE_PROFILE => ['primary' => '#9C27B0', 'secondary' => '#F3E5F5'],
            self::TYPE_EVENT => ['primary' => '#FF9800', 'secondary' => '#FFF3E0'],
            self::TYPE_PRODUCT => ['primary' => '#4CAF50', 'secondary' => '#E8F5E8'],
            default => ['primary' => '#607D8B', 'secondary' => '#ECEFF1'],
        };
    }

    /**
     * Obtener icono del tipo
     */
    public static function getTypeIcon(string $type): string
    {
        return match($type) {
            self::TYPE_MENU => '🍽️',
            self::TYPE_GIFT => '🎁',
            self::TYPE_TOURIST => '🗺️',
            self::TYPE_PROFILE => '👤',
            self::TYPE_EVENT => '📅',
            self::TYPE_PRODUCT => '📦',
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
     * Obtener platos del menú como colección (solo para tipo MENU)
     */
    public function getDishesAttribute()
    {
        if ($this->type !== self::TYPE_MENU) {
            return collect([]);
        }

        $items = $this->data['menu_items'] ?? [];
        
        return collect($items)->map(function ($item, $index) {
            return (object) array_merge($item, [
                'id' => $index,
                'index' => $index,
                'content_id' => $this->id,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]);
        });
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