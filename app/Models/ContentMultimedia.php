<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentMultimedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'video_url',
        'video_type',
        'audio_url',
        'audio_type',
        'gallery_images',
        'settings',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'settings' => 'array',
    ];

    /**
     * Relación con el contenido dinámico principal
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Obtener configuración de video con valores por defecto
     */
    public function getVideoConfigAttribute(): array
    {
        return array_merge([
            'url' => $this->video_url,
            'type' => $this->video_type,
            'autoplay' => false,
            'muted' => false,
        ], $this->settings['video'] ?? []);
    }

    /**
     * Obtener configuración de audio con valores por defecto
     */
    public function getAudioConfigAttribute(): array
    {
        return array_merge([
            'url' => $this->audio_url,
            'type' => $this->audio_type,
            'autoplay' => false,
            'loop' => false,
        ], $this->settings['audio'] ?? []);
    }
}
