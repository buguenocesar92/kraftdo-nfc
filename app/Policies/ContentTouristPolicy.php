<?php

namespace App\Policies;

use App\Models\ContentTourist;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContentTouristPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow authenticated users to view tourist content
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContentTourist $contentTourist): bool
    {
        // Users can view tourist content they own or public content
        return $this->owns($user, $contentTourist) || $this->isPublicContent($contentTourist);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow verified users to create tourist content
        return $user !== null && $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContentTourist $contentTourist): bool
    {
        // Only owners can update their tourist content
        return $this->owns($user, $contentTourist);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContentTourist $contentTourist): bool
    {
        // Only owners can delete their tourist content
        return $this->owns($user, $contentTourist);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContentTourist $contentTourist): bool
    {
        // Only owners can restore their tourist content
        return $this->owns($user, $contentTourist);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContentTourist $contentTourist): bool
    {
        // Only admins can force delete tourist content
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage nearby spots.
     */
    public function manageNearbySpots(User $user, ContentTourist $contentTourist): bool
    {
        // Only tourist content owners can manage nearby spots
        return $this->owns($user, $contentTourist);
    }

    /**
     * Determine whether the user can publish/unpublish the tourist content.
     */
    public function publish(User $user, ContentTourist $contentTourist): bool
    {
        // Only owners can publish their tourist content
        return $this->owns($user, $contentTourist) && $this->isValidForPublication($contentTourist);
    }

    /**
     * Determine whether the user can access location data.
     */
    public function viewLocationData(User $user, ContentTourist $contentTourist): bool
    {
        // Users can view location data for public content or owned content
        return $this->view($user, $contentTourist) && $this->hasLocationData($contentTourist);
    }

    /**
     * Check if user owns the tourist content.
     */
    private function owns(User $user, ContentTourist $contentTourist): bool
    {
        return $contentTourist->dynamicContent?->user_id === $user->id;
    }

    /**
     * Check if tourist content is public (published and active).
     */
    private function isPublicContent(ContentTourist $contentTourist): bool
    {
        return $contentTourist->dynamicContent?->status === 'published' &&
               $contentTourist->dynamicContent?->is_active === true;
    }

    /**
     * Check if tourist content is valid for publication.
     */
    private function isValidForPublication(ContentTourist $contentTourist): bool
    {
        return !empty($contentTourist->location_name) &&
               !empty($contentTourist->location_address) &&
               ($contentTourist->latitude !== null && $contentTourist->longitude !== null);
    }

    /**
     * Check if tourist content has location data.
     */
    private function hasLocationData(ContentTourist $contentTourist): bool
    {
        return $contentTourist->latitude !== null && $contentTourist->longitude !== null;
    }
}
