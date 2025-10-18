<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo proceder si la columna menu_images existe
        if (Schema::hasColumn('content_businesses', 'menu_images')) {
            // Migrar datos del JSON array a la nueva tabla
            $businesses = \App\Models\ContentBusiness::whereNotNull('menu_images')->get();
            
            foreach ($businesses as $business) {
                if (!empty($business->menu_images) && is_array($business->menu_images)) {
                    foreach ($business->menu_images as $index => $imageUrl) {
                        \App\Models\ContentMenuImage::create([
                            'content_business_id' => $business->id,
                            'image_url' => $imageUrl,
                            'title' => null,
                            'description' => null,
                            'display_order' => $index,
                            'is_active' => true,
                        ]);
                    }
                }
            }
            
            // Remover el campo JSON después de migrar los datos
            Schema::table('content_businesses', function (Blueprint $table) {
                $table->dropColumn('menu_images');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Solo proceder si la columna menu_images NO existe
        if (!Schema::hasColumn('content_businesses', 'menu_images')) {
            // Restaurar el campo JSON
            Schema::table('content_businesses', function (Blueprint $table) {
                $table->json('menu_images')->nullable()->after('catalog_enabled');
            });
            
            // Migrar los datos de vuelta a JSON (opcional, para rollback)
            $businesses = \App\Models\ContentBusiness::with('menuImages')->get();
            
            foreach ($businesses as $business) {
                $menuImages = $business->menuImages->pluck('image_url')->toArray();
                if (!empty($menuImages)) {
                    $business->update(['menu_images' => $menuImages]);
                }
            }
            
            // Eliminar registros de la tabla
            \App\Models\ContentMenuImage::truncate();
        }
    }
};
