<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dynamic_content_id' => $this->dynamic_content_id,
            'message' => $this->message,
            'sender_name' => $this->sender_name,
            'recipient_name' => $this->recipient_name,
            'multimedia' => $this->when($this->relationLoaded('multimedia'), function () {
                return $this->multimedia ? [
                    'id' => $this->multimedia->id,
                    'video_url' => $this->multimedia->video_url,
                    'audio_url' => $this->multimedia->audio_url,
                    'settings' => $this->multimedia->settings,
                    'gallery_images' => $this->when(
                        $this->multimedia->relationLoaded('galleryImages'),
                        function () {
                            return $this->multimedia->galleryImages->map(function ($image) {
                                return [
                                    'id' => $image->id,
                                    'image_url' => $image->image_url,
                                    'alt_text' => $image->alt_text,
                                    'sort_order' => $image->sort_order,
                                ];
                            });
                        }
                    ),
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
