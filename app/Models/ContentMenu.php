<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'restaurant_name',
        'restaurant_phone',
        'restaurant_address',
        'restaurant_hours',
    ];

    /**
     * Relación con el contenido dinámico principal
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Relación con los items del menú
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(ContentMenuItem::class, 'content_menu_id');
    }

    /**
     * Obtener items del menú ordenados por categoría y orden
     */
    public function getOrderedItemsAttribute()
    {
        return $this->menuItems()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }

    /**
     * Obtener información del restaurante como array
     */
    public function getRestaurantInfoAttribute(): array
    {
        return [
            'name' => $this->restaurant_name,
            'phone' => $this->restaurant_phone,
            'address' => $this->restaurant_address,
            'hours' => $this->restaurant_hours,
        ];
    }
}
