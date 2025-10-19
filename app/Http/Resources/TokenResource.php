<?php

namespace App\Http\Resources;

use App\Enums\ContentType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'token_id' => $this->token_id,
            'content_type' => $this->content_type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'dynamic_content' => $this->when($this->relationLoaded('dynamicContent'), function () {
                return [
                    'id' => $this->dynamicContent->id,
                    'title' => $this->dynamicContent->title,
                    'description' => $this->dynamicContent->description,
                    'content' => $this->getContentResource(),
                ];
            }),
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }

    /**
     * Get content resource based on content type
     */
    private function getContentResource()
    {
        if (! $this->dynamicContent || ! $this->dynamicContent->content) {
            return null;
        }

        $contentType = ContentType::fromString($this->content_type);

        return match ($contentType) {
            ContentType::BUSINESS, ContentType::MENU => new BusinessContentResource($this->dynamicContent->content),
            ContentType::PROFILE => new ProfileContentResource($this->dynamicContent->content),
            ContentType::GIFT => new GiftContentResource($this->dynamicContent->content),
            ContentType::TOURIST => new TouristContentResource($this->dynamicContent->content),
            ContentType::BUS_STOP => new BusStopContentResource($this->dynamicContent->content),
            ContentType::BUSINESS_GROUP => new BusinessGroupContentResource($this->dynamicContent->content),
        };
    }
}
