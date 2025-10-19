<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
        'ticket_price',
        'ticket_currency',
        'registration_url',
    ];

    protected $casts = [
        'event_start_date' => 'datetime',
        'event_end_date' => 'datetime',
        'registration_required' => 'boolean',
        'ticket_price' => 'decimal:2',
        'contact_info' => 'array',
    ];

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-crear DynamicContent cuando se crea un ContentEvent
        static::creating(function ($event) {
            if (! $event->dynamic_content_id) {
                // Crear NFC Token
                $nfcToken = \App\Models\NfcToken::create([
                    'name' => 'Token: Evento ' . ($event->event_organizer ?? 'Sin nombre'),
                    'content_type' => 'EVENT',
                    'user_id' => auth()->id() ?? 1,
                    'is_active' => true,
                ]);

                $dynamicContent = DynamicContent::create([
                    'content_id' => Str::uuid()->toString(),
                    'type' => DynamicContent::TYPE_EVENT,
                    'title' => 'Evento: ' . ($event->event_organizer ?? 'Sin nombre'),
                    'description' => 'Evento en ' . ($event->event_location ?? 'ubicación por definir'),
                    'data' => [],
                    'nfc_token_id' => $nfcToken->id,
                    'is_active' => true,
                    'status' => 'published',
                    'user_id' => auth()->id() ?? 1,
                ]);

                $event->dynamic_content_id = $dynamicContent->id;
            }
        });

        // Actualizar DynamicContent cuando se actualiza el evento
        static::updated(function ($event) {
            if ($event->dynamicContent) {
                $event->dynamicContent->update([
                    'title' => 'Evento: ' . ($event->event_organizer ?? 'Sin nombre'),
                    'description' => 'Evento en ' . ($event->event_location ?? 'ubicación por definir'),
                    'is_active' => $event->isActive(),
                ]);
            }
        });
    }

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
        return $this->registration_required && ! empty($this->registration_url);
    }

    /**
     * Obtener duración del evento en horas
     */
    public function getDurationInHours(): ?int
    {
        if (! $this->event_start_date || ! $this->event_end_date) {
            return null;
        }

        return $this->event_start_date->diffInHours($this->event_end_date);
    }
}
