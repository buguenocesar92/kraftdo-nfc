<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentTourist extends Model
{
    use HasFactory;

    protected $table = 'content_tourist';

    protected $fillable = [
        'dynamic_content_id',
        'location_name',
        'place_type',
        'location_address',
        'history',
        'latitude',
        'longitude',
        'practical_info',
        'gallery_images',
        'contact_phone',
        'contact_email',
        'website_url',
        'opening_hours',
        'pricing_info',
        'accessibility_info',
        'services',
        'attractions',
        'best_time_to_visit',
        'languages_spoken',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'practical_info' => 'array',
        'gallery_images' => 'array',
        'opening_hours' => 'array',
        'pricing_info' => 'array',
        'accessibility_info' => 'array',
        'services' => 'array',
        'attractions' => 'array',
        'languages_spoken' => 'array',
    ];

    /**
     * Relación con el contenido dinámico padre
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Relación con los puntos cercanos
     */
    public function nearbySpots(): HasMany
    {
        return $this->hasMany(NearbySpot::class);
    }

    /**
     * Obtener puntos cercanos activos ordenados
     */
    public function activeNearbySpots(): HasMany
    {
        return $this->nearbySpots()->active()->ordered();
    }

    /**
     * Verificar si el lugar está actualmente abierto
     */
    public function isCurrentlyOpen(): ?bool
    {
        if (!$this->opening_hours) {
            return null; // No se conocen los horarios
        }

        $now = now();
        $dayOfWeek = strtolower($now->format('l')); // monday, tuesday, etc.
        $currentTime = $now->format('H:i');

        $todayHours = $this->opening_hours[$dayOfWeek] ?? null;

        if (!$todayHours || $todayHours === 'closed') {
            return false;
        }

        // Si es array (formato nuevo)
        if (is_array($todayHours)) {
            $open = $todayHours['open'] ?? null;
            $close = $todayHours['close'] ?? null;

            if ($open && $close) {
                return $currentTime >= $open && $currentTime <= $close;
            }
        }

        // Si es string (formato actual: "06:00 - 20:00")
        if (is_string($todayHours) && str_contains($todayHours, ' - ')) {
            [$open, $close] = explode(' - ', $todayHours);
            $open = trim($open);
            $close = trim($close);

            if ($open && $close) {
                return $currentTime >= $open && $currentTime <= $close;
            }
        }

        return null;
    }

    /**
     * Obtener coordenadas como string para Google Maps
     */
    public function getCoordinatesString(): ?string
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        return $this->latitude . ',' . $this->longitude;
    }

    /**
     * Generar URL de Google Maps
     */
    public function getGoogleMapsUrl(): ?string
    {
        $coords = $this->getCoordinatesString();
        
        if (!$coords) {
            return null;
        }

        return "https://maps.google.com/?q={$coords}";
    }

    /**
     * Verificar si tiene información de accesibilidad
     */
    public function hasAccessibilityInfo(): bool
    {
        return !empty($this->accessibility_info) && is_array($this->accessibility_info);
    }

    /**
     * Obtener el horario de hoy
     */
    public function getTodayHours(): mixed
    {
        if (!$this->opening_hours) {
            return null;
        }

        $today = strtolower(now()->format('l'));
        return $this->opening_hours[$today] ?? null;
    }


    /**
     * Obtener tipos de lugares disponibles
     */
    public static function getPlaceTypes(): array
    {
        return [
            'monumento' => 'Monumento Histórico',
            'naturaleza' => 'Lugar Natural',
            'patrimonio' => 'Patrimonio Cultural',
            'plaza' => 'Plaza/Parque',
            'museo' => 'Museo',
            'iglesia' => 'Iglesia/Templo',
            'mirador' => 'Mirador',
            'arquitectura' => 'Arquitectura',
            'arqueologico' => 'Sitio Arqueológico',
            'recreativo' => 'Lugar Recreativo',
        ];
    }

    /**
     * Obtener datos del mapa para Alpine.js
     */
    public function getMapData(): array
    {
        if (!$this->latitude || !$this->longitude) {
            return [];
        }

        return [
            'center' => [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ],
            'zoom' => 15,
            'mainMarker' => [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
                'title' => $this->location_name,
                'description' => $this->dynamicContent?->description ?? '',
                'type' => 'main',
                'color' => '#DC2626',
                'icon' => 'map-pin'
            ],
            'nearbySpots' => $this->activeNearbySpots->map(function ($spot) {
                $typeInfo = $spot->getSpotTypeInfo();
                return [
                    'lat' => (float) $spot->latitude,
                    'lng' => (float) $spot->longitude,
                    'title' => $spot->name,
                    'description' => $spot->description,
                    'type' => $spot->spot_type,
                    'distance' => $spot->distance_km,
                    'color' => $spot->color ?: $typeInfo['color'],
                    'icon' => $spot->icon ?: $typeInfo['icon'],
                    'additional_info' => $spot->additional_info,
                ];
            })->values()->toArray(),
        ];
    }

    /**
     * Obtener primera imagen de la galería
     */
    public function getMainImage(): ?string
    {
        if ($this->gallery_images && is_array($this->gallery_images) && count($this->gallery_images) > 0) {
            return Storage::url($this->gallery_images[0]);
        }

        return $this->dynamicContent?->image_url ? Storage::url($this->dynamicContent->image_url) : null;
    }

    /**
     * Verificar si tiene galería de imágenes
     */
    public function hasGallery(): bool
    {
        return $this->gallery_images && is_array($this->gallery_images) && count($this->gallery_images) > 1;
    }

    /**
     * Scope para buscar por lugar tipo
     */
    public function scopeByPlaceType($query, string $type)
    {
        return $query->where('place_type', $type);
    }

}