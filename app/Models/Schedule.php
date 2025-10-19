<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'day_of_week',
        'departure_times',
        'frequency_minutes',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'departure_times' => 'array',
        'frequency_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, string $day)
    {
        return $query->where('day_of_week', $day);
    }

    public function getDayNameAttribute(): string
    {
        $days = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return $days[$this->day_of_week] ?? ucfirst($this->day_of_week);
    }

    public function getFormattedTimesAttribute(): string
    {
        if (empty($this->departure_times)) {
            return 'Sin horarios definidos';
        }

        return implode(', ', $this->departure_times);
    }

    public function getNextDepartureAttribute(): ?string
    {
        if (empty($this->departure_times)) {
            return null;
        }

        $currentTime = now()->format('H:i');

        foreach ($this->departure_times as $time) {
            if ($time > $currentTime) {
                return $time;
            }
        }

        return $this->departure_times[0] ?? null;
    }
}
