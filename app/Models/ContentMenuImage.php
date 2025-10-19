<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Obtener la URL pública de la imagen
     */
    public function getPublicUrlAttribute(): string
    {
        // Si la URL ya es completa (empieza con http), devolverla tal como está
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }

        // Si es una ruta relativa, construir la URL completa
        return config('app.url') . '/storage/' . $this->image_url;
    }

    /**
     * Scope para imágenes activas
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ordenar por display_order
     * @param mixed $query
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }
}
