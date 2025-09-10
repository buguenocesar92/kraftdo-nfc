<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'name',
        'contact_email',
        'contact_phone',
        'contact_website',
        'bio',
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
            'dynamic_content_id', // Local key on ContentProfile table
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
            'dynamic_content_id', // Local key on ContentProfile table
            'id' // Local key on ContentMultimedia table
        );
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
            'dynamic_content_id', // Local key on ContentProfile table
            'id' // Local key on DynamicContent table
        );
    }

    /**
     * Obtener información de contacto como array
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'email' => $this->contact_email,
            'phone' => $this->contact_phone,
            'website' => $this->contact_website,
        ];
    }

    /**
     * Verificar si tiene información de contacto
     */
    public function hasContactInfo(): bool
    {
        return !empty($this->contact_email) || 
               !empty($this->contact_phone) || 
               !empty($this->contact_website);
    }
}
