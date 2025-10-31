<?php

namespace App\Services;

use App\Models\ContentProfile;
use App\Models\ContentSocialLink;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileContentService
{
    /**
     * Create profile content for a dynamic content
     */
    public function createProfileContent(int $dynamicContentId, array $data): ContentProfile
    {
        return DB::transaction(function () use ($dynamicContentId, $data) {
            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Create profile content
            $profileContent = ContentProfile::create([
                'dynamic_content_id' => $dynamicContentId,
                'name' => $data['name'],
                'contact_email' => $data['contact_email'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'contact_website' => $data['contact_website'] ?? null,
                'bio' => $data['bio'] ?? null,
                'profession' => $data['profession'] ?? null,
                'company' => $data['company'] ?? null,
                'location' => $data['location'] ?? null,
                'color_palette' => $data['color_palette'] ?? null,
            ]);

            // Update dynamic content type
            $dynamicContent->update(['content_type' => 'profile']);

            return $profileContent->load(['dynamicContent', 'multimedia', 'galleryImages']);
        });
    }

    /**
     * Get profile content by dynamic content ID
     */
    public function getProfileContent(int $dynamicContentId): ContentProfile
    {
        // Verify user owns the dynamic content
        $dynamicContent = DynamicContent::where('id', $dynamicContentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return ContentProfile::where('dynamic_content_id', $dynamicContentId)
            ->with(['socialLinks'])
            ->firstOrFail();
    }

    /**
     * Update profile content
     */
    public function updateProfileContent(int $profileId, array $data): ContentProfile
    {
        return DB::transaction(function () use ($profileId, $data) {
            $profileContent = ContentProfile::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($profileId);

            $profileContent->update($data);

            return $profileContent->fresh();
        });
    }

    /**
     * Get social links for a profile
     */
    public function getSocialLinks(int $profileId): \Illuminate\Database\Eloquent\Collection
    {
        $profileContent = ContentProfile::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($profileId);

        return ContentSocialLink::where('dynamic_content_id', $profileContent->dynamic_content_id)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Create social link for a profile
     */
    public function createSocialLink(int $profileId, array $data): ContentSocialLink
    {
        return DB::transaction(function () use ($profileId, $data) {
            $profileContent = ContentProfile::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($profileId);

            return ContentSocialLink::create([
                'dynamic_content_id' => $profileContent->dynamic_content_id,
                'platform' => $data['platform'],
                'url' => $data['url'],
                'username' => $data['username'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });
    }

    /**
     * Delete social link
     */
    public function deleteSocialLink(int $linkId): bool
    {
        return DB::transaction(function () use ($linkId) {
            $socialLink = ContentSocialLink::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($linkId);

            return $socialLink->delete();
        });
    }
}