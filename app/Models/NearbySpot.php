<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NearbySpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_tourist_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'spot_type',
        'distance_km',
        'icon',
        'color',
        'additional_info',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
        'additional_info' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el contenido turístico padre
     */
    public function contentTourist(): BelongsTo
    {
        return $this->belongsTo(ContentTourist::class);
    }

    /**
     * Obtener coordenadas como string
     */
    public function getCoordinatesString(): string
    {
        return $this->latitude . ',' . $this->longitude;
    }

    /**
     * Obtener URL de Google Maps para este punto
     */
    public function getGoogleMapsUrl(): string
    {
        return "https://maps.google.com/?q={$this->getCoordinatesString()}";
    }

    /**
     * Calcular distancia desde otro punto (en metros)
     */
    public function calculateDistanceFrom(float $fromLat, float $fromLng): float
    {
        $earthRadius = 6371000; // metros

        $latDelta = deg2rad($this->latitude - $fromLat);
        $lngDelta = deg2rad($this->longitude - $fromLng);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($fromLat)) * cos(deg2rad($this->latitude)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Actualizar distancia desde el punto principal
     */
    public function updateDistanceFromMainPoint(): void
    {
        $tourist = $this->contentTourist;

        if ($tourist && $tourist->latitude && $tourist->longitude) {
            $distanceMeters = $this->calculateDistanceFrom($tourist->latitude, $tourist->longitude);
            $this->update(['distance_km' => round($distanceMeters / 1000, 2)]);
        }
    }

    /**
     * Scope para puntos activos
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar por tipo y luego por orden
     * @param mixed $query
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('spot_type')->orderBy('sort_order');
    }

    /**
     * Obtener tipos de punto disponibles
     */
    public static function getSpotTypes(): array
    {
        return [
            'restaurante' => [
                'label' => 'Restaurante',
                'icon' => 'utensils',
                'color' => '#FF6B35',
            ],
            'hotel' => [
                'label' => 'Hotel/Alojamiento',
                'icon' => 'bed',
                'color' => '#8B5CF6',
            ],
            'transporte' => [
                'label' => 'Transporte',
                'icon' => 'bus',
                'color' => '#06B6D4',
            ],
            'atraccion' => [
                'label' => 'Atracción Turística',
                'icon' => 'camera',
                'color' => '#F59E0B',
            ],
            'comercio' => [
                'label' => 'Comercio/Tienda',
                'icon' => 'shopping-bag',
                'color' => '#10B981',
            ],
            'servicio' => [
                'label' => 'Servicio',
                'icon' => 'wrench',
                'color' => '#6B7280',
            ],
            'salud' => [
                'label' => 'Centro de Salud',
                'icon' => 'plus-circle',
                'color' => '#DC2626',
            ],
            'banco' => [
                'label' => 'Banco/ATM',
                'icon' => 'credit-card',
                'color' => '#1F2937',
            ],
        ];
    }

    /**
     * Obtener información del tipo de punto
     */
    public function getSpotTypeInfo(): array
    {
        $types = self::getSpotTypes();

        return $types[$this->spot_type] ?? [
            'label' => ucfirst($this->spot_type),
            'icon' => 'map-pin',
            'color' => '#6B7280',
        ];
    }
}
