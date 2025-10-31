<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dynamic_content_id' => $this->dynamic_content_id,
            'name' => $this->name,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'contact_website' => $this->contact_website,
            'bio' => $this->bio,
            'profession' => $this->profession,
            'company' => $this->company,
            'location' => $this->location,
            'color_palette' => $this->color_palette,
            'social_links' => $this->when($this->relationLoaded('socialLinks'), function () {
                return $this->socialLinks;
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
