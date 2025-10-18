<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentBusiness extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'business_name',
        'description',
        'business_type',
        'logo_url',
        'contact_phone',
        'contact_email',
        'contact_website',
        'address',
        'latitude',
        'longitude',
        'google_maps_url',
        'google_reviews_url',
        'google_place_id',
        'instagram_url',
        'facebook_url',
        'whatsapp_number',
        'operating_hours',
        'services',
        'catalog_enabled',
        'color_palette',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'services' => 'array',
        'catalog_enabled' => 'boolean',
        'color_palette' => 'array',
    ];

    /**
     * Relación con el contenido dinámico principal
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Relación directa con multimedia a través de DynamicContent
     */
    public function multimedia(): HasOneThrough
    {
        return $this->hasOneThrough(
            ContentMultimedia::class,
            DynamicContent::class,
            'id', // Foreign key on DynamicContent table
            'dynamic_content_id', // Foreign key on ContentMultimedia table
            'dynamic_content_id', // Local key on ContentBusiness table
            'id' // Local key on DynamicContent table
        );
    }

    /**
     * Relación directa con imágenes de galería a través de DynamicContent y Multimedia
     */
    public function galleryImages(): HasManyThrough
    {
        return $this->hasManyThrough(
            ContentGalleryImage::class,
            ContentMultimedia::class,
            'dynamic_content_id', // Foreign key on ContentMultimedia table
            'content_multimedia_id', // Foreign key on ContentGalleryImage table
            'dynamic_content_id', // Local key on ContentBusiness table
            'id' // Local key on ContentMultimedia table
        );
    }

    /**
     * Relación directa con productos a través de DynamicContent
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            ContentProduct::class,
            DynamicContent::class,
            'id', // Foreign key on DynamicContent table
            'dynamic_content_id', // Foreign key on ContentProduct table
            'dynamic_content_id', // Local key on ContentBusiness table
            'id' // Local key on DynamicContent table
        );
    }

    /**
     * Relación directa con productos para Filament (HasMany)
     */
    public function directProducts(): HasMany
    {
        return $this->hasMany(ContentProduct::class, 'content_business_id');
    }

    /**
     * Relación directa con enlaces sociales a través de DynamicContent
     */
    public function socialLinks(): HasManyThrough
    {
        return $this->hasManyThrough(
            ContentSocialLink::class,
            DynamicContent::class,
            'id', // Foreign key on DynamicContent table
            'dynamic_content_id', // Foreign key on ContentSocialLink table
            'dynamic_content_id', // Local key on ContentBusiness table
            'id' // Local key on DynamicContent table
        );
    }

    /**
     * Relación con las imágenes del menú
     */
    public function menuImages(): HasMany
    {
        return $this->hasMany(ContentMenuImage::class, 'content_business_id');
    }

    /**
     * Relación con las imágenes activas del menú, ordenadas
     */
    public function activeMenuImages(): HasMany
    {
        return $this->menuImages()->active()->ordered();
    }

    /**
     * Obtener información de contacto completa como array
     */
    public function getContactInfoArrayAttribute(): array
    {
        return [
            'phone' => $this->contact_phone,
            'email' => $this->contact_email,
            'website' => $this->contact_website,
            'address' => $this->address,
            'whatsapp' => $this->whatsapp_number,
        ];
    }

    /**
     * Obtener URLs de redes sociales como array
     */
    public function getSocialMediaArrayAttribute(): array
    {
        return [
            'instagram' => $this->instagram_url,
            'facebook' => $this->facebook_url,
            'whatsapp' => $this->getWhatsappUrlAttribute(),
        ];
    }

    /**
     * Generar URL de WhatsApp automáticamente
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        if ($this->whatsapp_number) {
            $number = preg_replace('/[^0-9]/', '', $this->whatsapp_number);
            return "https://wa.me/{$number}";
        }
        return null;
    }

    /**
     * Generar URL de Google Maps desde la dirección
     */
    public function getGoogleMapsAutoUrlAttribute(): string
    {
        if ($this->google_maps_url) {
            return $this->google_maps_url;
        }
        
        if ($this->address) {
            return 'https://maps.google.com/maps?q=' . urlencode($this->address);
        }
        
        return '';
    }

    /**
     * Verificar si tiene información de contacto
     */
    public function hasContactInfo(): bool
    {
        return !empty($this->contact_phone) ||
               !empty($this->contact_email) || 
               !empty($this->contact_website) ||
               !empty($this->address);
    }

    /**
     * Verificar si tiene redes sociales configuradas
     */
    public function hasSocialMedia(): bool
    {
        return !empty($this->instagram_url) ||
               !empty($this->facebook_url) ||
               !empty($this->whatsapp_number);
    }

    /**
     * Verificar si tiene catálogo de productos habilitado
     */
    public function hasCatalog(): bool
    {
        return $this->catalog_enabled && $this->products()->count() > 0;
    }

    /**
     * Verificar si tiene imágenes de menú configuradas
     */
    public function hasMenuImages(): bool
    {
        return $this->activeMenuImages()->count() > 0;
    }

    /**
     * Verificar si debe mostrar catálogo de productos o imágenes de menú
     * Prioridad: Imágenes de menú > Catálogo de productos
     */
    public function shouldShowProductCatalog(): bool
    {
        return $this->catalog_enabled && !$this->hasMenuImages() && $this->products()->count() > 0;
    }

    /**
     * Verificar si debe mostrar las imágenes del menú
     */
    public function shouldShowMenuImages(): bool
    {
        return $this->catalog_enabled && $this->hasMenuImages();
    }

    /**
     * Obtener las URLs públicas de las imágenes del menú
     */
    public function getMenuImagesPublicUrlsAttribute(): array
    {
        return $this->activeMenuImages->map(function ($menuImage) {
            return $menuImage->public_url;
        })->toArray();
    }

    /**
     * Obtener horarios de atención formateados
     */
    public function getFormattedOperatingHours(): array
    {
        $hours = $this->operating_hours ?? [];
        $formatted = [];
        
        $days = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes', 
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];

        // Manejar dos formatos posibles de operating_hours
        if (is_array($hours) && !empty($hours)) {
            // Formato 1: Array de objetos [{"day":"monday","hours":"09:00-18:00"}]
            if (isset($hours[0]) && is_array($hours[0]) && isset($hours[0]['day'])) {
                foreach ($hours as $schedule) {
                    $dayKey = $schedule['day'] ?? null;
                    $hoursValue = $schedule['hours'] ?? null;
                    
                    if ($dayKey && $hoursValue && isset($days[$dayKey])) {
                        $formatted[$days[$dayKey]] = $hoursValue;
                    }
                }
            }
            // Formato 2: Array asociativo ["monday" => "09:00-18:00"]
            else {
                foreach ($days as $key => $day) {
                    if (isset($hours[$key])) {
                        $formatted[$day] = $hours[$key];
                    }
                }
            }
        }

        return $formatted;
    }

    /**
     * Verificar si está abierto según horarios
     */
    public function isOpenNow(): bool
    {
        $hours = $this->operating_hours ?? [];
        $currentDay = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        $dayHours = null;

        // Manejar dos formatos posibles de operating_hours
        if (is_array($hours) && !empty($hours)) {
            // Formato 1: Array de objetos [{"day":"monday","hours":"09:00-18:00"}]
            if (isset($hours[0]) && is_array($hours[0]) && isset($hours[0]['day'])) {
                foreach ($hours as $schedule) {
                    if (($schedule['day'] ?? null) === $currentDay) {
                        $dayHours = $schedule['hours'] ?? null;
                        break;
                    }
                }
            }
            // Formato 2: Array asociativo ["monday" => "09:00-18:00"]
            else {
                $dayHours = $hours[$currentDay] ?? null;
            }
        }

        if (empty($dayHours) || $dayHours === 'Cerrado') {
            return false;
        }

        // Formato esperado: "09:00-18:00"
        if (preg_match('/(\d{2}:\d{2})-(\d{2}:\d{2})/', $dayHours, $matches)) {
            $openTime = $matches[1];
            $closeTime = $matches[2];
            
            return $currentTime >= $openTime && $currentTime <= $closeTime;
        }

        return false;
    }

    // ========== MÉTODOS ESPECÍFICOS PARA RESTAURANTES ==========

    /**
     * Verificar si es un restaurante
     */
    public function isRestaurant(): bool
    {
        return $this->business_type === 'restaurant';
    }

    /**
     * Obtener items del menú (productos) organizados por categoría
     */
    public function getMenuItemsByCategoryAttribute(): array
    {
        if (!$this->isRestaurant()) {
            return [];
        }

        return $this->directProducts()
            ->orderBy('brand') // brand contiene la categoría para restaurantes
            ->orderBy('name')
            ->get()
            ->groupBy('brand')
            ->toArray();
    }

    /**
     * Obtener items del menú disponibles
     */
    public function getAvailableMenuItemsAttribute()
    {
        if (!$this->isRestaurant()) {
            return collect();
        }

        return $this->directProducts()
            ->where('in_stock', true)
            ->orderBy('brand')
            ->orderBy('name')
            ->get();
    }

    /**
     * Obtener categorías del menú únicas
     */
    public function getMenuCategoriesAttribute(): array
    {
        if (!$this->isRestaurant()) {
            return [];
        }

        return $this->directProducts()
            ->distinct('brand')
            ->pluck('brand')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Scope para filtrar solo restaurantes
     */
    public function scopeRestaurants($query)
    {
        return $query->where('business_type', 'restaurant');
    }

    /**
     * Scope para filtrar negocios (no restaurantes)
     */
    public function scopeBusinesses($query)
    {
        return $query->where('business_type', '!=', 'restaurant')->orWhereNull('business_type');
    }

    /**
     * Relación many-to-many con grupos de negocios
     */
    public function businessGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            ContentBusinessGroup::class,
            'business_group_members',
            'member_business_id',
            'business_group_id'
        )
        ->withPivot([
            'display_order',
            'is_featured',
            'custom_position',
            'member_status',
            'member_notes'
        ])
        ->withTimestamps();
    }

    /**
     * Alias para businessGroups() - para compatibilidad con Filament
     */
    public function contentBusinessGroups(): BelongsToMany
    {
        return $this->businessGroups();
    }
}