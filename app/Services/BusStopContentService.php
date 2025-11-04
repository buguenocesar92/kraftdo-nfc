<?php

namespace App\Services;

use App\Models\BusStop;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusStopContentService
{
    /**
     * Create a new bus stop content
     */
    public function createBusStopContent(int $dynamicContentId, array $data): BusStop
    {
        return DB::transaction(function () use ($dynamicContentId, $data) {
            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Create bus stop content
            $busStopContent = BusStop::create([
                'dynamic_content_id' => $dynamicContentId,
                'stop_id' => $data['stop_id'] ?? null,
                'name' => $data['name'] ?? null,
                'address' => $data['address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'municipality_name' => $data['municipality_name'] ?? null,
                'municipality_logo_url' => $data['municipality_logo_url'] ?? null,
                'municipality_description' => $data['municipality_description'] ?? null,
                'municipality_website' => $data['municipality_website'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            return $busStopContent;
        });
    }

    /**
     * Get bus stop content by dynamic content ID
     */
    public function getBusStopContent(int $dynamicContentId): BusStop
    {
        // Verify user owns the dynamic content
        $dynamicContent = DynamicContent::where('id', $dynamicContentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return BusStop::where('dynamic_content_id', $dynamicContentId)
            ->with(['dynamicContent', 'routes', 'utilityPhones'])
            ->firstOrFail();
    }

    /**
     * Update bus stop content
     */
    public function updateBusStopContent(int $busStopId, array $data): BusStop
    {
        $busStopContent = BusStop::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($busStopId);

        if (!empty($data)) {
            $busStopContent->update($data);
        }

        return $busStopContent->fresh();
    }

    /**
     * Delete bus stop content
     */
    public function deleteBusStopContent(int $busStopId): bool
    {
        $busStopContent = BusStop::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($busStopId);

        return $busStopContent->delete();
    }

    /**
     * Check if user owns the bus stop content
     */
    public function userOwnsBusStopContent(int $busStopId): bool
    {
        return BusStop::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $busStopId)->exists();
    }
}