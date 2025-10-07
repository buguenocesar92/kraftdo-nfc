<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_stop_id',
        'name',
        'route_number',
        'origin',
        'destination',
        'fare',
        'currency',
        'operator',
        'color',
        'is_active',
    ];

    protected $casts = [
        'fare' => 'integer',
        'is_active' => 'boolean',
    ];

    public function busStop(): BelongsTo
    {
        return $this->belongsTo(BusStop::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedFareAttribute(): string
    {
        if (!$this->fare) {
            return 'Consultar';
        }
        
        return '$' . number_format($this->fare, 0, ',', '.') . ' ' . $this->currency;
    }

    public function getFullRouteAttribute(): string
    {
        return "{$this->origin} → {$this->destination}";
    }
}