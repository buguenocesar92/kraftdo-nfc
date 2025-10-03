<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}