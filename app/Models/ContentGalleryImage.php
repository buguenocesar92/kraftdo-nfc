<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentGalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_multimedia_id',
        'image_path',
        'image_url',
        'alt_text',
        'caption',
        'sort_order',
        'type',
        'metadata',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    // Tipos de imagen
    public const TYPE_UPLOAD = 'upload';
    public const TYPE_URL = 'url';

    public const TYPES = [
        self::TYPE_UPLOAD => 'Archivo subido',
        self::TYPE_URL => 'URL externa',
    ];

    /**
     * Relación con ContentMultimedia
     */
    public function contentMultimedia(): BelongsTo
    {
        return $this->belongsTo(ContentMultimedia::class);
    }

    /**
     * Obtener la URL real de la imagen (archivo subido o URL externa)
     */
    public function getImageSourceAttribute(): ?string
    {
        if ($this->type === self::TYPE_UPLOAD && $this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        return $this->image_url;
    }

    /**
     * Obtener información completa de la imagen para APIs
     */
    public function getImageDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'src' => $this->image_source,
            'alt' => $this->alt_text,
            'caption' => $this->caption,
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Scope para ordenar por sort_order
     * @param mixed $query
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Scope para solo archivos subidos
     * @param mixed $query
     */
    public function scopeUploads($query)
    {
        return $query->where('type', self::TYPE_UPLOAD);
    }

    /**
     * Scope para solo URLs externas
     * @param mixed $query
     */
    public function scopeUrls($query)
    {
        return $query->where('type', self::TYPE_URL);
    }
}
