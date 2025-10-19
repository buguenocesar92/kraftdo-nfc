<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_group_id',
        'member_business_id',
        'display_order',
        'is_featured',
        'custom_position',
        'member_status',
        'member_notes',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'custom_position' => 'array',
        'display_order' => 'integer',
    ];

    /**
     * Relación con el grupo de negocios
     */
    public function businessGroup(): BelongsTo
    {
        return $this->belongsTo(ContentBusinessGroup::class, 'business_group_id');
    }

    /**
     * Relación con el negocio miembro
     */
    public function memberBusiness(): BelongsTo
    {
        return $this->belongsTo(ContentBusiness::class, 'member_business_id');
    }

    /**
     * Scopes
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->where('member_status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
