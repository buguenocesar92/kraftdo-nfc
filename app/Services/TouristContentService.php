<?php

namespace App\Services;

use App\Models\ContentTourist;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TouristContentService
{
    /**
     * Create a new tourist content
     */
    public function createTouristContent(int $dynamicContentId, array $data): ContentTourist
    {
        return DB::transaction(function () use ($dynamicContentId, $data) {
            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Create tourist content
            $touristContent = ContentTourist::create([
                'dynamic_content_id' => $dynamicContentId,
                'location_name' => $data['location_name'] ?? null,
                'place_type' => $data['place_type'] ?? 'recreativo',
                'location_address' => $data['location_address'] ?? null,
                'history' => $data['history'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'practical_info' => $data['practical_info'] ?? [],
                'gallery_images' => $data['gallery_images'] ?? [],
                'contact_phone' => $data['contact_phone'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'website_url' => $data['website_url'] ?? null,
                'opening_hours' => $data['opening_hours'] ?? [],
                'pricing_info' => $data['pricing_info'] ?? [],
                'accessibility_info' => $data['accessibility_info'] ?? [],
                'services' => $data['services'] ?? [],
                'attractions' => $data['attractions'] ?? [],
                'best_time_to_visit' => $data['best_time_to_visit'] ?? null,
                'languages_spoken' => $data['languages_spoken'] ?? [],
            ]);

            return $touristContent;
        });
    }

    /**
     * Get tourist content by dynamic content ID
     */
    public function getTouristContent(int $dynamicContentId): ContentTourist
    {
        // Verify user owns the dynamic content
        $dynamicContent = DynamicContent::where('id', $dynamicContentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return ContentTourist::where('dynamic_content_id', $dynamicContentId)
            ->with(['dynamicContent', 'nearbySpots'])
            ->firstOrFail();
    }

    /**
     * Update tourist content
     */
    public function updateTouristContent(int $touristId, array $data): ContentTourist
    {
        $touristContent = ContentTourist::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($touristId);

        if (!empty($data)) {
            $touristContent->update($data);
        }

        return $touristContent->fresh();
    }

    /**
     * Delete tourist content
     */
    public function deleteTouristContent(int $touristId): bool
    {
        $touristContent = ContentTourist::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($touristId);

        return $touristContent->delete();
    }

    /**
     * Check if user owns the tourist content
     */
    public function userOwnsTouristContent(int $touristId): bool
    {
        return ContentTourist::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $touristId)->exists();
    }
}