<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ContentProduct extends Model
{
    use HasFactory;

    protected $table = 'content_products';

    protected $fillable = [
        'dynamic_content_id',
        'content_business_id',
        'name',
        'price',
        'currency',
        'sku',
        'stock',
        'in_stock',
        'brand',
        'specifications',
        'purchase_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'in_stock' => 'boolean',
        'specifications' => 'string',
    ];

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-crear DynamicContent cuando se crea un ContentProduct
        static::creating(function ($product) {
            if (!$product->dynamic_content_id) {
                $dynamicContent = DynamicContent::create([
                    'content_id' => Str::uuid()->toString(),
                    'type' => DynamicContent::TYPE_PRODUCT,
                    'title' => $product->name,
                    'description' => $product->specifications ?? 'Producto de ' . ($product->brand ?? 'marca'),
                    'data' => [],
                    'is_active' => true,
                    'status' => 'published',
                    'user_id' => auth()->id() ?? 1,
                ]);
                
                $product->dynamic_content_id = $dynamicContent->id;
            }
        });

        // Actualizar DynamicContent cuando se actualiza el producto
        static::updated(function ($product) {
            if ($product->dynamicContent) {
                $product->dynamicContent->update([
                    'title' => $product->name,
                    'description' => $product->specifications ?? 'Producto de ' . ($product->brand ?? 'marca'),
                    'is_active' => $product->in_stock,
                ]);
            }
        });
    }

    /**
     * Relación con el contenido dinámico padre
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Relación directa con ContentBusiness
     */
    public function contentBusiness(): BelongsTo
    {
        return $this->belongsTo(ContentBusiness::class);
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