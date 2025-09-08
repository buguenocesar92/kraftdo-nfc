<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentProduct extends Model
{
    use HasFactory;

    protected $table = 'content_products';

    protected $fillable = [
        'dynamic_content_id',
        'product_price',
        'product_currency',
        'product_sku',
        'product_stock',
        'product_description',
        'product_features',
        'product_dimensions',
        'product_weight',
        'availability_status',
        'shipping_info',
        'warranty_info',
        'return_policy',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'product_stock' => 'integer',
        'product_features' => 'array',
        'product_dimensions' => 'array',
        'product_weight' => 'decimal:3',
        'shipping_info' => 'array',
    ];

    /**
     * Relación con el contenido dinámico padre
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Verificar si el producto está disponible
     */
    public function isAvailable(): bool
    {
        return $this->availability_status === 'available' && $this->product_stock > 0;
    }

    /**
     * Verificar si el producto está en stock
     */
    public function inStock(): bool
    {
        return $this->product_stock > 0;
    }

    /**
     * Obtener precio formateado
     */
    public function getFormattedPrice(): string
    {
        $currency = $this->product_currency ?? 'USD';
        $symbol = match($currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency . ' '
        };

        return $symbol . number_format($this->product_price, 2);
    }

    /**
     * Reducir stock del producto
     */
    public function reduceStock(int $quantity = 1): bool
    {
        if ($this->product_stock >= $quantity) {
            $this->decrement('product_stock', $quantity);
            return true;
        }

        return false;
    }

    /**
     * Incrementar stock del producto
     */
    public function increaseStock(int $quantity = 1): void
    {
        $this->increment('product_stock', $quantity);
    }
}