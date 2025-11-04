<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TouristContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dynamic_content_id' => $this->dynamic_content_id,
            'location_name' => $this->location_name,
            'place_type' => $this->place_type,
            'location_address' => $this->location_address,
            'history' => $this->history,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'practical_info' => $this->practical_info,
            'gallery_images' => $this->gallery_images,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'website_url' => $this->website_url,
            'opening_hours' => $this->opening_hours,
            'pricing_info' => $this->pricing_info,
            'accessibility_info' => $this->accessibility_info,
            'services' => $this->services,
            'attractions' => $this->attractions,
            'best_time_to_visit' => $this->best_time_to_visit,
            'languages_spoken' => $this->languages_spoken,
            'is_currently_open' => $this->isCurrentlyOpen(),
            'coordinates_string' => $this->getCoordinatesString(),
            'google_maps_url' => $this->getGoogleMapsUrl(),
            'has_accessibility_info' => $this->hasAccessibilityInfo(),
            'today_hours' => $this->getTodayHours(),
            'main_image' => $this->getMainImage(),
            'has_gallery' => $this->hasGallery(),
            'map_data' => $this->getMapData(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'dynamic_content' => new DynamicContentResource($this->whenLoaded('dynamicContent')),
            'nearby_spots' => $this->whenLoaded('nearbySpots'),
        ];
    }
}
