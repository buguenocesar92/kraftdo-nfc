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
            'name' => $this->name,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'color_palette' => $this->color_palette,
            'skills' => $this->when($this->relationLoaded('skills'), function () {
                return $this->skills;
            }),
            'social_links' => $this->when($this->relationLoaded('socialLinks'), function () {
                return $this->socialLinks;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
