<?php

namespace App\Policies;

use App\Models\BusStop;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusStopPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow authenticated users to view bus stop content
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BusStop $contentBusStop): bool
    {
        // Bus stop content is typically public information
        return $this->owns($user, $contentBusStop) || $this->isPublicContent($contentBusStop);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow municipal officials or verified users to create bus stop content
        return $user !== null && ($user->hasRole(['admin', 'municipal_official']) || $user->hasVerifiedEmail());
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BusStop $contentBusStop): bool
    {
        // Only owners or municipal officials can update bus stop content
        return $this->owns($user, $contentBusStop) || $user->hasRole(['admin', 'municipal_official']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BusStop $contentBusStop): bool
    {
        // Only owners or authorized officials can delete bus stop content
        return $this->owns($user, $contentBusStop) || $user->hasRole(['admin', 'municipal_official']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BusStop $contentBusStop): bool
    {
        // Only owners or authorized officials can restore bus stop content
        return $this->owns($user, $contentBusStop) || $user->hasRole(['admin', 'municipal_official']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BusStop $contentBusStop): bool
    {
        // Only admins can force delete bus stop content
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage routes and schedules.
     */
    public function manageRoutes(User $user, BusStop $contentBusStop): bool
    {
        // Only owners or transportation officials can manage routes
        return $this->owns($user, $contentBusStop) || $user->hasRole(['admin', 'municipal_official', 'transport_manager']);
    }

    /**
     * Determine whether the user can manage utility phone numbers.
     */
    public function manageUtilityPhones(User $user, BusStop $contentBusStop): bool
    {
        // Only owners or municipal officials can manage utility phones
        return $this->owns($user, $contentBusStop) || $user->hasRole(['admin', 'municipal_official']);
    }

    /**
     * Determine whether the user can publish/unpublish the bus stop content.
     */
    public function publish(User $user, BusStop $contentBusStop): bool
    {
        // Only owners or officials can publish bus stop content
        return ($this->owns($user, $contentBusStop) || $user->hasRole(['admin', 'municipal_official'])) 
               && $this->isValidForPublication($contentBusStop);
    }

    /**
     * Determine whether the user can access real-time data.
     */
    public function viewRealTimeData(User $user, BusStop $contentBusStop): bool
    {
        // Real-time data is typically public for active bus stops
        return $this->isPublicContent($contentBusStop) && $this->hasRealTimeData($contentBusStop);
    }

    /**
     * Check if user owns the bus stop content.
     */
    private function owns(User $user, BusStop $contentBusStop): bool
    {
        return $contentBusStop->dynamicContent?->user_id === $user->id;
    }

    /**
     * Check if bus stop content is public (published and active).
     */
    private function isPublicContent(BusStop $contentBusStop): bool
    {
        return $contentBusStop->dynamicContent?->status === 'published' &&
               $contentBusStop->dynamicContent?->is_active === true;
    }

    /**
     * Check if bus stop content is valid for publication.
     */
    private function isValidForPublication(BusStop $contentBusStop): bool
    {
        return !empty($contentBusStop->stop_id) &&
               !empty($contentBusStop->name) &&
               !empty($contentBusStop->address) &&
               ($contentBusStop->latitude !== null && $contentBusStop->longitude !== null);
    }

    /**
     * Check if bus stop has real-time data capabilities.
     */
    private function hasRealTimeData(BusStop $contentBusStop): bool
    {
        // Check if bus stop has routes with schedules
        return $contentBusStop->routes()->exists();
    }
}
