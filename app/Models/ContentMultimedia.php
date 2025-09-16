<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentMultimedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'video_url',
        'video_file',
        'video_type',
        'audio_url',
        'audio_file',
        'audio_type',
        'gallery_images',
        'gallery_files',
        'settings',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'gallery_files' => 'array',
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
     * Relación con las imágenes de galería
     */
    public function galleryImages(): HasMany
    {
        return $this->hasMany(ContentGalleryImage::class);
    }

    /**
     * Obtener la URL real del video (archivo subido o URL externa)
     */
    public function getVideoSourceAttribute(): ?string
    {
        if ($this->video_type === 'file_upload' && $this->video_file) {
            return asset('storage/' . $this->video_file);
        }
        return $this->video_url;
    }

    /**
     * Obtener la URL real del audio (archivo subido o URL externa)
     */
    public function getAudioSourceAttribute(): ?string
    {
        if ($this->audio_type === 'file_upload' && $this->audio_file) {
            return asset('storage/' . $this->audio_file);
        }
        return $this->audio_url;
    }

    /**
     * Obtener todas las imágenes de la galería desde la tabla normalizada
     */
    public function getAllGalleryImagesAttribute(): array
    {
        return $this->galleryImages()
            ->ordered()
            ->get()
            ->map(function ($image) {
                return $image->image_data;
            })
            ->toArray();
    }

    /**
     * Obtener solo las URLs de las imágenes de galería (para retrocompatibilidad)
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        return $this->galleryImages()
            ->ordered()
            ->get()
            ->pluck('image_source')
            ->filter()
            ->toArray();
    }

    /**
     * Obtener configuración de video con valores por defecto
     */
    public function getVideoConfigAttribute(): array
    {
        return array_merge([
            'url' => $this->video_source,
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
            'url' => $this->audio_source,
            'type' => $this->audio_type,
            'autoplay' => false,
            'loop' => false,
        ], $this->settings['audio'] ?? []);
    }
}
