<?php

namespace App\Services;

use App\Models\ContentBusiness;
use App\Models\ContentProduct;
use App\Models\DynamicContent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessContentService
{
    /**
     * Create business content for a dynamic content
     */
    public function createBusinessContent(int $dynamicContentId, array $data): ContentBusiness
    {
        return DB::transaction(function () use ($dynamicContentId, $data) {
            // Verify user owns the dynamic content
            $dynamicContent = DynamicContent::where('id', $dynamicContentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Create business content
            $businessContent = ContentBusiness::create([
                'dynamic_content_id' => $dynamicContentId,
                'business_name' => $data['business_name'],
                'description' => $data['description'] ?? null,
                'business_type' => $data['business_type'] ?? null,
                'logo_url' => $data['logo_url'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'contact_website' => $data['contact_website'] ?? null,
                'address' => $data['address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'google_maps_url' => $data['google_maps_url'] ?? null,
                'google_reviews_url' => $data['google_reviews_url'] ?? null,
                'google_place_id' => $data['google_place_id'] ?? null,
                'instagram_url' => $data['instagram_url'] ?? null,
                'facebook_url' => $data['facebook_url'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'operating_hours' => $data['operating_hours'] ?? null,
                'services' => $data['services'] ?? null,
                'catalog_enabled' => $data['catalog_enabled'] ?? false,
                'color_palette' => $data['color_palette'] ?? null,
            ]);

            // Update dynamic content type
            $dynamicContent->update(['content_type' => 'business']);

            return $businessContent->load(['dynamicContent', 'multimedia', 'galleryImages', 'products']);
        });
    }

    /**
     * Get business content by dynamic content ID
     */
    public function getBusinessContent(int $dynamicContentId): ContentBusiness
    {
        // Verify user owns the dynamic content
        $dynamicContent = DynamicContent::where('id', $dynamicContentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return ContentBusiness::where('dynamic_content_id', $dynamicContentId)
            ->with(['products', 'multimedia', 'galleryImages'])
            ->firstOrFail();
    }

    /**
     * Update business content
     */
    public function updateBusinessContent(int $businessId, array $data): ContentBusiness
    {
        return DB::transaction(function () use ($businessId, $data) {
            $businessContent = ContentBusiness::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($businessId);

            $businessContent->update($data);

            return $businessContent->fresh();
        });
    }

    /**
     * Delete business content
     */
    public function deleteBusinessContent(int $businessId): bool
    {
        return DB::transaction(function () use ($businessId) {
            $businessContent = ContentBusiness::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($businessId);

            return $businessContent->delete();
        });
    }

    // ========================================
    // BUSINESS PRODUCTS METHODS
    // ========================================

    /**
     * Get products for a business
     */
    public function getBusinessProducts(int $businessId): \Illuminate\Database\Eloquent\Collection
    {
        $businessContent = ContentBusiness::whereHas('dynamicContent', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($businessId);

        return ContentProduct::where('content_business_id', $businessContent->id)
            ->orderBy('name')
            ->get();
    }

    /**
     * Create product for a business
     */
    public function createBusinessProduct(int $businessId, array $data): ContentProduct
    {
        return DB::transaction(function () use ($businessId, $data) {
            $businessContent = ContentBusiness::whereHas('dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($businessId);

            return ContentProduct::create([
                'content_business_id' => $businessContent->id,
                'dynamic_content_id' => $businessContent->dynamic_content_id,
                'name' => $data['name'],
                'price' => $data['price'] ?? null,
                'currency' => $data['currency'] ?? 'USD',
                'sku' => $data['sku'] ?? null,
                'stock' => $data['stock'] ?? 0,
                'in_stock' => $data['in_stock'] ?? true,
                'brand' => $data['brand'] ?? null,
                'specifications' => $data['specifications'] ?? null,
                'purchase_url' => $data['purchase_url'] ?? null,
            ]);
        });
    }

    /**
     * Update business product
     */
    public function updateBusinessProduct(int $productId, array $data): ContentProduct
    {
        return DB::transaction(function () use ($productId, $data) {
            $product = ContentProduct::whereHas('contentBusiness.dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($productId);

            $product->update($data);

            return $product->fresh();
        });
    }

    /**
     * Delete business product
     */
    public function deleteBusinessProduct(int $productId): bool
    {
        return DB::transaction(function () use ($productId) {
            $product = ContentProduct::whereHas('contentBusiness.dynamicContent', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($productId);

            return $product->delete();
        });
    }
}