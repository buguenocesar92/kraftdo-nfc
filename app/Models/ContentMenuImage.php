<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContentMenuImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_business_id',
        'image_url',
        'title',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Relación con el negocio
     */
    public function contentBusiness(): BelongsTo
    {
        return $this->belongsTo(ContentBusiness::class);
    }

    /**
     * Scope para imágenes activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar por display_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }

    /**
     * Obtener la URL pública de la imagen
     */
    public function getPublicUrlAttribute(): string
    {
        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }

        return Storage::disk('public')->url($this->image_url);
    }
}
