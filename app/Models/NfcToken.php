<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NfcToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token_id',
        'user_id',
        'name',
        'content_type',
        'customization_plan',
        'purchase_price',
        'purchased_at',
        'purchase_notes',
        'purchase_currency',
        'cost_per_view',
        'total_investment_views',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'purchased_at' => 'datetime',
        'purchase_price' => 'decimal:2',
        'cost_per_view' => 'decimal:4',
        'total_investment_views' => 'integer',
    ];

    /**
     * Boot method para generar token_id automáticamente
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($token) {
            if (empty($token->token_id)) {
                $token->token_id = self::generateUniqueTokenId();
            }
        });
    }

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Relación con el usuario propietario del chip
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el contenido dinámico específico de este token
     */
    public function dynamicContent(): HasOne
    {
        return $this->hasOne(DynamicContent::class, 'nfc_token_id');
    }

    /**
     * Relación con las analíticas de este token
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(NfcAnalytic::class);
    }

    // ========================================
    // MÉTODOS DE NEGOCIO
    // ========================================

    /**
     * Actualizar último uso del chip
     */
    public function updateLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Obtener el contenido del token o crear uno vacío
     */
    public function getOrCreateContent(): DynamicContent
    {
        if (!$this->dynamicContent) {
            $this->dynamicContent()->create([
                'content_id' => DynamicContent::generateUniqueContentId($this->content_type ?? 'PROFILE'),
                'type' => $this->content_type ?? 'PROFILE',
                'title' => $this->name ?? 'Contenido NFC',
                'description' => '',
                'data' => [],
                'user_id' => $this->user_id,
            ]);
        }

        return $this->dynamicContent;
    }

    /**
     * Verificar si el token tiene contenido configurado
     */
    public function hasContent(): bool
    {
        return $this->dynamicContent()->exists();
    }

    /**
     * Verificar si el contenido está completamente configurado
     */
    public function isContentReady(): bool
    {
        if (!$this->hasContent()) {
            return false;
        }

        $content = $this->dynamicContent;
        return !empty($content->title) && !empty($content->description);
    }

    /**
     * Verificar si el token está asignado
     */
    public function isAssigned(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Incrementar contador de vistas para ROI
     */
    public function incrementViews(): void
    {
        $this->increment('total_investment_views');
        $this->updateCostPerView();
    }

    /**
     * Calcular y actualizar el costo por vista
     */
    public function updateCostPerView(): void
    {
        if ($this->total_investment_views > 0 && $this->purchase_price > 0) {
            $this->update([
                'cost_per_view' => $this->purchase_price / $this->total_investment_views
            ]);
        }
    }

    /**
     * Obtener ROI y métricas financieras
     */
    public function getROI(): array
    {
        $totalViews = $this->total_investment_views;
        $purchasePrice = (float) $this->purchase_price;
        $costPerView = (float) $this->cost_per_view;

        $estimatedValue = $totalViews * 0.10; // Valor estimado por vista
        $roi = $purchasePrice > 0 ? (($estimatedValue - $purchasePrice) / $purchasePrice) * 100 : 0;

        return [
            'total_views' => $totalViews,
            'purchase_price' => $purchasePrice,
            'cost_per_view' => $costPerView,
            'estimated_value' => $estimatedValue,
            'roi_percentage' => round($roi, 2),
            'break_even_views' => $purchasePrice > 0 ? ceil($purchasePrice / 0.10) : 0,
            'views_to_break_even' => max(0, ceil($purchasePrice / 0.10) - $totalViews),
        ];
    }

    // ========================================
    // PLANES DE PERSONALIZACIÓN
    // ========================================

    /**
     * Obtener todos los planes de personalización disponibles
     */
    public static function getCustomizationPlans(): array
    {
        return [
            'BASIC' => [
                'name' => 'Básico',
                'description' => 'Solo texto y música de fondo',
                'price_multiplier' => 1.0,
                'features' => [
                    'text_message' => true,
                    'background_music' => true,
                    'recipient_sender' => true,
                    'gift_subtype' => true,
                    'image_upload' => false,
                    'video_upload' => false,
                    'gallery' => false,
                    'social_links' => false,
                    'custom_design' => false,
                    'multimedia_advanced' => false,
                ]
            ],
            'STANDARD' => [
                'name' => 'Estándar',
                'description' => 'Incluye imagen y diseño básico',
                'price_multiplier' => 1.5,
                'features' => [
                    'text_message' => true,
                    'background_music' => true,
                    'recipient_sender' => true,
                    'gift_subtype' => true,
                    'image_upload' => true,
                    'video_upload' => false,
                    'gallery' => false,
                    'social_links' => true,
                    'custom_design' => true,
                    'multimedia_advanced' => false,
                ]
            ],
            'PREMIUM' => [
                'name' => 'Premium',
                'description' => 'Incluye video y galería de fotos',
                'price_multiplier' => 2.0,
                'features' => [
                    'text_message' => true,
                    'background_music' => true,
                    'recipient_sender' => true,
                    'gift_subtype' => true,
                    'image_upload' => true,
                    'video_upload' => true,
                    'gallery' => true,
                    'social_links' => true,
                    'custom_design' => true,
                    'multimedia_advanced' => false,
                ]
            ],
            'DELUXE' => [
                'name' => 'Deluxe',
                'description' => 'Todas las opciones disponibles',
                'price_multiplier' => 3.0,
                'features' => [
                    'text_message' => true,
                    'background_music' => true,
                    'recipient_sender' => true,
                    'gift_subtype' => true,
                    'image_upload' => true,
                    'video_upload' => true,
                    'gallery' => true,
                    'social_links' => true,
                    'custom_design' => true,
                    'multimedia_advanced' => true,
                ]
            ]
        ];
    }

    /**
     * Obtener el plan de personalización actual
     */
    public function getCustomizationPlan(): array
    {
        $plans = self::getCustomizationPlans();
        return $plans[$this->customization_plan] ?? $plans['BASIC'];
    }

    /**
     * Verificar si una característica está disponible en el plan actual
     */
    public function hasFeature(string $feature): bool
    {
        $plan = $this->getCustomizationPlan();
        return $plan['features'][$feature] ?? false;
    }

    /**
     * Obtener el precio ajustado según el plan de personalización
     */
    public function getAdjustedPrice(): float
    {
        $plan = $this->getCustomizationPlan();
        return ((float) $this->purchase_price) * $plan['price_multiplier'];
    }

    /**
     * Obtener las características disponibles para el plan actual
     */
    public function getAvailableFeatures(): array
    {
        $plan = $this->getCustomizationPlan();
        return array_filter($plan['features'], function($available) {
            return $available === true;
        });
    }

    // ========================================
    // MÉTODOS DE QUERY
    // ========================================

    /**
     * Buscar token activo por su ID
     */
    public static function findActiveByTokenId(string $tokenId): ?self
    {
        return self::where('token_id', $tokenId)
                  ->where('is_active', true)
                  ->first();
    }

    /**
     * Scope para tokens activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para tokens asignados
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope para tokens no asignados
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Generar token_id único usando UUID
     */
    public static function generateUniqueTokenId(): string
    {
        do {
            $tokenId = Str::uuid()->toString();
        } while (self::where('token_id', $tokenId)->exists());

        return $tokenId;
    }
}