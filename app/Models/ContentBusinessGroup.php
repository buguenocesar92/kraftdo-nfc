<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentBusinessGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_content_id',
        'group_name',
        'description',
        'address',
        'location_coordinates',
        'contact_phone',
        'contact_email',
        'contact_website',
        'operating_hours',
        'banner_image',
        'logo_url',
        'group_type',
        'amenities',
        'special_instructions',
        'is_active',
    ];

    protected $casts = [
        'location_coordinates' => 'array',
        'operating_hours' => 'array',
        'amenities' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el contenido dinámico padre
     */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    /**
     * Relación muchos a muchos con los negocios miembros
     */
    public function memberBusinesses(): BelongsToMany
    {
        return $this->belongsToMany(
            ContentBusiness::class,
            'business_group_members',
            'business_group_id',
            'member_business_id'
        )
        ->withPivot([
            'display_order',
            'is_featured',
            'custom_position',
            'member_status',
            'member_notes'
        ])
        ->withTimestamps()
        ->orderByPivot('display_order');
    }

    /**
     * Relación directa con los registros de membresía
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(BusinessGroupMember::class, 'business_group_id');
    }

    /**
     * Solo negocios activos
     */
    public function activeMemberBusinesses(): BelongsToMany
    {
        return $this->memberBusinesses()
            ->wherePivot('member_status', 'active');
    }

    /**
     * Solo negocios destacados
     */
    public function featuredMemberBusinesses(): BelongsToMany
    {
        return $this->activeMemberBusinesses()
            ->wherePivot('is_featured', true);
    }

    /**
     * Obtener negocios por tipo (para food courts con diferentes tipos)
     */
    public function memberBusinessesByType(string $businessType): BelongsToMany
    {
        return $this->activeMemberBusinesses()
            ->where('content_businesses.business_type', $businessType);
    }

    /**
     * Obtener restaurantes del grupo
     */
    public function restaurants(): BelongsToMany
    {
        return $this->memberBusinessesByType('restaurant');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('group_type', $type);
    }

    public function scopeFoodCourts($query)
    {
        return $query->byType('food_court');
    }

    /**
     * Accessors & Mutators
     */
    public function getLogoUrlAttribute($value)
    {
        if (!$value) return null;
        
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return url("storage/{$value}");
    }

    public function getBannerImageAttribute($value)
    {
        if (!$value) return null;
        
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return url("storage/{$value}");
    }

    /**
     * Métodos de utilidad
     */
    public function addMember(ContentBusiness $business, array $options = []): BusinessGroupMember
    {
        return $this->memberships()->create(array_merge([
            'member_business_id' => $business->id,
            'display_order' => $this->memberships()->max('display_order') + 1,
            'is_featured' => false,
            'member_status' => 'active',
        ], $options));
    }

    public function removeMember(ContentBusiness $business): bool
    {
        return $this->memberships()
            ->where('member_business_id', $business->id)
            ->delete();
    }

    public function reorderMembers(array $businessIds): void
    {
        foreach ($businessIds as $order => $businessId) {
            $this->memberships()
                ->where('member_business_id', $businessId)
                ->update(['display_order' => $order]);
        }
    }

    /**
     * Accessor para operating_hours - convierte array a JSON string para formularios
     */
    public function getOperatingHoursForFormAttribute(): string
    {
        return $this->operating_hours ? json_encode($this->operating_hours, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    }

    /**
     * Accessor para location_coordinates - convierte array a JSON string para formularios
     */
    public function getLocationCoordinatesForFormAttribute(): string
    {
        return $this->location_coordinates ? json_encode($this->location_coordinates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    }

    /**
     * Accessor para amenities - convierte array a JSON string para formularios
     */
    public function getAmenitiesForFormAttribute(): string
    {
        return $this->amenities ? json_encode($this->amenities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    }

    /**
     * Mutator para operating_hours - convierte JSON string a array
     */
    public function setOperatingHoursAttribute($value)
    {
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['operating_hours'] = $decoded ? json_encode($decoded) : null;
        } else {
            $this->attributes['operating_hours'] = $value;
        }
    }

    /**
     * Mutator para location_coordinates - convierte JSON string a array
     */
    public function setLocationCoordinatesAttribute($value)
    {
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['location_coordinates'] = $decoded ? json_encode($decoded) : null;
        } else {
            $this->attributes['location_coordinates'] = $value;
        }
    }

    /**
     * Mutator para amenities - convierte JSON string a array
     */
    public function setAmenitiesAttribute($value)
    {
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['amenities'] = $decoded ? json_encode($decoded) : null;
        } else {
            $this->attributes['amenities'] = $value;
        }
    }

    /**
     * Obtener estadísticas del grupo
     */
    public function getStats(): array
    {
        return [
            'total_members' => $this->memberBusinesses()->count(),
            'active_members' => $this->activeMemberBusinesses()->count(),
            'featured_members' => $this->featuredMemberBusinesses()->count(),
            'restaurants' => $this->restaurants()->count(),
        ];
    }
}