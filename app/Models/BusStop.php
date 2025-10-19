<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'stop_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'municipality_name',
        'municipality_logo_url',
        'municipality_description',
        'municipality_website',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }

    public function utilityPhones(): HasMany
    {
        return $this->hasMany(UtilityPhone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->municipality_name}";
    }
}
