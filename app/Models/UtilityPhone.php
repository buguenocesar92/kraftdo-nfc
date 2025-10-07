<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityPhone extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_stop_id',
        'name',
        'phone_number',
        'category',
        'description',
        'icon',
        'is_emergency',
        'is_active',
    ];

    protected $casts = [
        'is_emergency' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function busStop(): BelongsTo
    {
        return $this->belongsTo(BusStop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getCategoryNameAttribute(): string
    {
        $categories = [
            'emergencia' => 'Emergencias',
            'salud' => 'Salud',
            'municipal' => 'Municipal',
            'servicios' => 'Servicios Básicos',
            'transporte' => 'Transporte',
        ];

        return $categories[$this->category] ?? ucfirst($this->category);
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone_number;
        
        // Formatear números chilenos
        if (preg_match('/^\+56/', $phone)) {
            return $phone;
        }
        
        // Números de emergencia
        if (in_array($phone, ['132', '133', '134', '131'])) {
            return $phone;
        }
        
        // Otros números
        return $phone;
    }

    public function getCallLinkAttribute(): string
    {
        return "tel:{$this->phone_number}";
    }
}