<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dynamic_content_id' => $this->dynamic_content_id,
            'business_name' => $this->business_name,
            'description' => $this->description,
            'business_type' => $this->business_type,
            'logo_url' => $this->logo_url,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'contact_website' => $this->contact_website,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'google_maps_url' => $this->google_maps_url,
            'google_reviews_url' => $this->google_reviews_url,
            'google_place_id' => $this->google_place_id,
            'instagram_url' => $this->instagram_url,
            'facebook_url' => $this->facebook_url,
            'whatsapp_number' => $this->whatsapp_number,
            'operating_hours' => $this->operating_hours,
            'services' => $this->services,
            'catalog_enabled' => $this->catalog_enabled,
            'color_palette' => $this->color_palette,
            'products' => $this->when($this->relationLoaded('products'), function () {
                return $this->products;
            }),
            'multimedia' => $this->when($this->relationLoaded('multimedia'), function () {
                return $this->multimedia;
            }),
            'gallery_images' => $this->when($this->relationLoaded('galleryImages'), function () {
                return $this->galleryImages;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
