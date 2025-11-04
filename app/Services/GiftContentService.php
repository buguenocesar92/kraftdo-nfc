<?php

namespace App\Services;

use App\Models\ContentGift;
use App\Models\ContentGalleryImage;
use App\Models\ContentMultimedia;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GiftContentService
{
    /**
     * Create a new gift content
     */
    public function createGiftContent(int $dynamicContentId, array $data): ContentGift
    {
        return DB::transaction(function () use ($dynamicContentId, $data) {
            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Create gift content
            $giftContent = ContentGift::create([
                'dynamic_content_id' => $dynamicContentId,
                'message' => $data['message'] ?? null,
                'recipient_name' => $data['recipient_name'] ?? null,
                'sender_name' => $data['sender_name'] ?? null,
            ]);

            // Create ContentMultimedia for the gift to enable file uploads
            ContentMultimedia::firstOrCreate(
                ['dynamic_content_id' => $dynamicContentId],
                ['settings' => ['theme' => 'romantic']]
            );

            return $giftContent;
        });
    }

    /**
     * Get gift content by dynamic content ID
     */
    public function getGiftContent(int $dynamicContentId): ContentGift
    {
        // Verify user owns the dynamic content
        $dynamicContent = DynamicContent::where('id', $dynamicContentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return ContentGift::where('dynamic_content_id', $dynamicContentId)
            ->with(['multimedia.galleryImages' => function ($query) {
                $query->orderBy('sort_order')->orderBy('id');
            }])
            ->firstOrFail();
    }

    /**
     * Update gift content
     */
    public function updateGiftContent(int $giftId, array $data): ContentGift
    {
        $giftContent = ContentGift::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($giftId);

        if (!empty($data)) {
            $giftContent->update($data);
        }

        return $giftContent->fresh();
    }

    /**
     * Delete gift content
     */
    public function deleteGiftContent(int $giftId): bool
    {
        $giftContent = ContentGift::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($giftId);

        return $giftContent->delete();
    }

    /**
     * Check if user owns the gift content
     */
    public function userOwnsGiftContent(int $giftId): bool
    {
        return ContentGift::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $giftId)->exists();
    }

    // ========================================
    // GIFT GALLERY METHODS
    // ========================================

    /**
     * Get gallery images for a gift
     */
    public function getGiftGallery(int $giftId): \Illuminate\Database\Eloquent\Collection
    {
        $giftContent = ContentGift::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($giftId);

        // Get multimedia for this gift
        $multimedia = ContentMultimedia::where('dynamic_content_id', $giftContent->dynamic_content_id)->first();
        
        if (!$multimedia) {
            return collect();
        }

        return ContentGalleryImage::where('content_multimedia_id', $multimedia->id)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Create gallery item for a gift
     */
    public function createGiftGalleryItem(int $giftId, array $data): ContentGalleryImage
    {
        return DB::transaction(function () use ($giftId, $data) {
            $giftContent = ContentGift::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($giftId);

            // Get or create multimedia for this gift
            $multimedia = ContentMultimedia::firstOrCreate([
                'dynamic_content_id' => $giftContent->dynamic_content_id
            ], [
                'settings' => []
            ]);

            return ContentGalleryImage::create([
                'content_multimedia_id' => $multimedia->id,
                'image_path' => $data['image_path'],
                'image_url' => $data['image_url'] ?? null,
                'alt_text' => $data['alt_text'] ?? 'Imagen de galería',
                'caption' => $data['caption'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'type' => $data['type'] ?? 'gallery',
            ]);
        });
    }

    /**
     * Delete gift gallery item
     */
    public function deleteGiftGalleryItem(int $itemId): bool
    {
        return DB::transaction(function () use ($itemId) {
            $galleryImage = ContentGalleryImage::whereHas('contentMultimedia.dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($itemId);

            return $galleryImage->delete();
        });
    }
}