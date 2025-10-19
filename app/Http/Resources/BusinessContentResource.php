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
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'logo' => $this->logo,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'operating_hours' => $this->operating_hours,
            'catalog_enabled' => $this->catalog_enabled,
            'menu_images' => $this->menu_images,
            'social_links' => $this->social_links,
            'products' => $this->when($this->relationLoaded('products'), function () {
                return $this->products;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
