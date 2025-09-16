<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentEvent extends Model
{
    use HasFactory;

    protected $table = 'content_events';

    protected $fillable = [
        'dynamic_content_id',
        'event_location',
        'event_start_date',
        'event_end_date',
        'event_organizer',
        'event_description',
        'event_capacity',
        'registration_required',
        'registration_url',
        'ticket_price',
        'contact_info',
    ];

    protected $casts = [
        'event_start_date' => 'datetime',
        'event_end_date' => 'datetime',
        'registration_required' => 'boolean',
        'ticket_price' => 'decimal:2',
        'contact_info' => 'array',
    ];

    /**
     * Relación con el contenido dinámico padre
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Verificar si el evento está activo
     */
    public function isActive(): bool
    {
        return $this->event_end_date === null || $this->event_end_date->isFuture();
    }

    /**
     * Verificar si el evento requiere registro
     */
    public function requiresRegistration(): bool
    {
        return $this->registration_required && !empty($this->registration_url);
    }

    /**
     * Obtener duración del evento en horas
     */
    public function getDurationInHours(): ?int
    {
        if (!$this->event_start_date || !$this->event_end_date) {
            return null;
        }

        return $this->event_start_date->diffInHours($this->event_end_date);
    }
}