<?php

namespace App\Services;

use App\Models\ContentGift;
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
}