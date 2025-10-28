<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ContentGift extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'title',
        'sender_name',
        'recipient_name',
        'message',
        'theme',
        'special_date',
        'delivery_date',
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
            'dynamic_content_id', // Local key on ContentGift table
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
            'dynamic_content_id', // Local key on ContentGift table
            'id' // Local key on ContentMultimedia table
        );
    }
}
