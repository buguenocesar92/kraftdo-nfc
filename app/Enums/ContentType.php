<?php

namespace App\Enums;

use App\Models\BusStop;
use App\Models\ContentBusiness;
use App\Models\ContentBusinessGroup;
use App\Models\ContentGift;
use App\Models\ContentProfile;
use App\Models\ContentTourist;

enum ContentType: string
{
    case GIFT = 'GIFT';
    case PROFILE = 'PROFILE';
    case BUSINESS = 'BUSINESS';
    case TOURIST = 'TOURIST';
    case BUS_STOP = 'BUS_STOP';
    case BUSINESS_GROUP = 'BUSINESS_GROUP';
    case MENU = 'MENU'; // Legacy compatibility

    /**
     * Get the model class for this content type
     */
    public function getModelClass(): string
    {
        return match ($this) {
            self::GIFT => ContentGift::class,
            self::PROFILE => ContentProfile::class,
            self::BUSINESS => ContentBusiness::class,
            self::TOURIST => ContentTourist::class,
            self::BUS_STOP => BusStop::class,
            self::BUSINESS_GROUP => ContentBusinessGroup::class,
            self::MENU => ContentBusiness::class, // Legacy - treat as business
        };
    }

    /**
     * Get the view name for this content type
     */
    public function getViewName(): string
    {
        return match ($this) {
            self::GIFT => 'token.gift',
            self::PROFILE => 'token.profile',
            self::BUSINESS => 'token.business',
            self::TOURIST => 'token.tourist',
            self::BUS_STOP => 'token.bus-stop',
            self::BUSINESS_GROUP => 'token.business-group',
            self::MENU => 'token.business', // Legacy
        };
    }

    /**
     * Get the relations to load for this content type
     */
    public function getRelations(): array
    {
        return match ($this) {
            self::GIFT => ['multimedia'],
            self::PROFILE => ['skills', 'socialLinks', 'galleryImages'],
            self::BUSINESS => ['products', 'socialLinks'],
            self::TOURIST => ['nearbySpots'],
            self::BUS_STOP => ['routes.schedules', 'utilityPhones'],
            self::BUSINESS_GROUP => ['memberBusinesses'],
            self::MENU => ['products'], // Legacy
        };
    }

    /**
     * Check if this content type is supported
     */
    public static function isSupported(string $type): bool
    {
        return in_array($type, array_column(self::cases(), 'value'));
    }

    /**
     * Get content type from string with validation
     */
    public static function fromString(string $type): self
    {
        return self::from($type);
    }

    /**
     * Get display name for admin
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::GIFT => 'Regalo',
            self::PROFILE => 'Perfil',
            self::BUSINESS => 'Negocio',
            self::TOURIST => 'Turístico',
            self::BUS_STOP => 'Paradero',
            self::BUSINESS_GROUP => 'Grupo de Negocios',
            self::MENU => 'Menú (Legacy)',
        };
    }
}
