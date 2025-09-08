<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'contact_email',
        'contact_phone',
        'contact_website',
        'bio',
    ];

    /**
     * Relación con el contenido dinámico principal
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Obtener información de contacto como array
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'email' => $this->contact_email,
            'phone' => $this->contact_phone,
            'website' => $this->contact_website,
        ];
    }

    /**
     * Verificar si tiene información de contacto
     */
    public function hasContactInfo(): bool
    {
        return !empty($this->contact_email) || 
               !empty($this->contact_phone) || 
               !empty($this->contact_website);
    }
}
