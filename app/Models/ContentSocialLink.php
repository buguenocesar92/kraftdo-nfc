<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'platform',
        'url',
        'username',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // Plataformas soportadas
    public const PLATFORMS = [
        'instagram' => [
            'name' => 'Instagram',
            'icon' => 'fab fa-instagram',
            'color' => 'text-pink-600',
            'base_url' => 'https://instagram.com/',
        ],
        'linkedin' => [
            'name' => 'LinkedIn', 
            'icon' => 'fab fa-linkedin',
            'color' => 'text-blue-600',
            'base_url' => 'https://linkedin.com/in/',
        ],
        'twitter' => [
            'name' => 'Twitter/X',
            'icon' => 'fab fa-x-twitter',
            'color' => 'text-gray-800',
            'base_url' => 'https://x.com/',
        ],
        'facebook' => [
            'name' => 'Facebook',
            'icon' => 'fab fa-facebook',
            'color' => 'text-blue-700',
            'base_url' => 'https://facebook.com/',
        ],
        'tiktok' => [
            'name' => 'TikTok',
            'icon' => 'fab fa-tiktok',
            'color' => 'text-black',
            'base_url' => 'https://tiktok.com/@',
        ],
        'youtube' => [
            'name' => 'YouTube',
            'icon' => 'fab fa-youtube',
            'color' => 'text-red-600',
            'base_url' => 'https://youtube.com/@',
        ],
        'github' => [
            'name' => 'GitHub',
            'icon' => 'fab fa-github',
            'color' => 'text-gray-800',
            'base_url' => 'https://github.com/',
        ],
        'website' => [
            'name' => 'Sitio Web',
            'icon' => 'fas fa-globe',
            'color' => 'text-blue-500',
            'base_url' => '',
        ],
    ];

    /**
     * Relación con el contenido dinámico principal
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Obtener información de la plataforma
     */
    public function getPlatformInfoAttribute(): array
    {
        return self::PLATFORMS[$this->platform] ?? self::PLATFORMS['website'];
    }

    /**
     * Obtener icono de la plataforma
     */
    public function getPlatformIconAttribute(): string
    {
        return $this->platform_info['icon'];
    }

    /**
     * Obtener color de la plataforma
     */
    public function getPlatformColorAttribute(): string
    {
        return $this->platform_info['color'];
    }

    /**
     * Scope ordenado por sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('platform');
    }
}
