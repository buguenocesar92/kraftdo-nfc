<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentMenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_menu_id',
        'name',
        'description',
        'price',
        'currency',
        'category',
        'image_url',
        'available',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'available' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relación con el menú
     */
    public function contentMenu(): BelongsTo
    {
        return $this->belongsTo(ContentMenu::class, 'content_menu_id');
    }

    /**
     * Obtener precio formateado
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    /**
     * Scope para items disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }

    /**
     * Scope por categoría
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
