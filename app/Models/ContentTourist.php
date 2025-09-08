<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentTourist extends Model
{
    use HasFactory;

    protected $table = 'content_tourist';

    protected $fillable = [
        'dynamic_content_id',
        'location_name',
        'location_address',
        'location_coordinates',
        'opening_hours',
        'contact_phone',
        'contact_email',
        'website_url',
        'description',
        'attractions',
        'services',
        'accessibility_info',
        'pricing_info',
        'best_time_to_visit',
        'languages_spoken',
        'nearby_places',
    ];

    protected $casts = [
        'location_coordinates' => 'array', // [lat, lng]
        'opening_hours' => 'array',
        'attractions' => 'array',
        'services' => 'array',
        'accessibility_info' => 'array',
        'pricing_info' => 'array',
        'languages_spoken' => 'array',
        'nearby_places' => 'array',
    ];

    /**
     * Relación con el contenido dinámico padre
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
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

        if (is_array($todayHours)) {
            $open = $todayHours['open'] ?? null;
            $close = $todayHours['close'] ?? null;

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
        if (!$this->location_coordinates || !isset($this->location_coordinates['lat'], $this->location_coordinates['lng'])) {
            return null;
        }

        return $this->location_coordinates['lat'] . ',' . $this->location_coordinates['lng'];
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
    public function getTodayHours(): ?array
    {
        if (!$this->opening_hours) {
            return null;
        }

        $today = strtolower(now()->format('l'));
        return $this->opening_hours[$today] ?? null;
    }
}