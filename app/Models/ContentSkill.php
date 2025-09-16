<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'name',
        'level',
        'category',
        'sort_order',
    ];

    protected $casts = [
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Relación con el contenido dinámico principal
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Obtener nivel como porcentaje
     */
    public function getLevelPercentageAttribute(): int
    {
        return min(100, max(0, ($this->level ?? 0) * 10));
    }

    /**
     * Obtener descripción del nivel
     */
    public function getLevelDescriptionAttribute(): string
    {
        return match(true) {
            $this->level >= 9 => 'Experto',
            $this->level >= 7 => 'Avanzado',
            $this->level >= 5 => 'Intermedio',
            $this->level >= 3 => 'Básico',
            default => 'Principiante',
        };
    }

    /**
     * Scope ordenado por categoría y orden
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('category')
                    ->orderBy('sort_order')
                    ->orderBy('name');
    }

    /**
     * Scope por categoría
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
