<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrar datos de content_menus a content_businesses
        $menus = DB::table('content_menus')->get();
        
        foreach ($menus as $menu) {
            // Crear el registro de business para cada menu
            DB::table('content_businesses')->insert([
                'dynamic_content_id' => $menu->dynamic_content_id,
                'business_name' => $menu->restaurant_name ?? 'Restaurante',
                'description' => null,
                'business_type' => 'restaurant',
                'logo_url' => null,
                'contact_phone' => $menu->restaurant_phone,
                'contact_email' => null,
                'contact_website' => null,
                'address' => $menu->restaurant_address,
                'google_maps_url' => null,
                'google_reviews_url' => null,
                'google_place_id' => null,
                'instagram_url' => null,
                'facebook_url' => null,
                'whatsapp_number' => null,
                'operating_hours' => $menu->restaurant_hours ? json_encode(['general' => $menu->restaurant_hours]) : null,
                'services' => null,
                'catalog_enabled' => true,
                'color_palette' => null,
                'created_at' => $menu->created_at,
                'updated_at' => $menu->updated_at,
            ]);
        }

        // 2. Migrar datos de content_menu_items a content_products
        $menuItems = DB::table('content_menu_items')
            ->join('content_menus', 'content_menu_items.content_menu_id', '=', 'content_menus.id')
            ->join('content_businesses', 'content_menus.dynamic_content_id', '=', 'content_businesses.dynamic_content_id')
            ->select([
                'content_menu_items.*',
                'content_menus.dynamic_content_id',
                'content_businesses.id as content_business_id'
            ])
            ->get();

        foreach ($menuItems as $item) {
            DB::table('content_products')->insert([
                'dynamic_content_id' => $item->dynamic_content_id,
                'content_business_id' => $item->content_business_id,
                'name' => $item->name,
                'price' => $item->price,
                'currency' => $item->currency,
                'sku' => null,
                'stock' => $item->available ? 999 : 0, // Si está disponible, stock alto
                'in_stock' => $item->available,
                'brand' => $item->category, // Categoría como marca/tipo
                'specifications' => $item->description,
                'purchase_url' => null,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }

        // 3. Actualizar dynamic_content type de MENU a BUSINESS para restaurantes
        DB::table('dynamic_content')
            ->whereIn('id', function($query) {
                $query->select('dynamic_content_id')
                      ->from('content_menus');
            })
            ->update(['type' => 'BUSINESS']);

        // 4. Actualizar nfc_tokens content_type de MENU a BUSINESS para restaurantes
        DB::table('nfc_tokens')
            ->where('content_type', 'MENU')
            ->update(['content_type' => 'BUSINESS']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios si es necesario
        // Nota: Esto sería complejo ya que perdemos la información de separación
        // Entre restaurantes y negocios normales, pero podemos usar business_type
        
        // 1. Restaurar nfc_tokens
        DB::table('nfc_tokens')
            ->whereIn('token_id', function($query) {
                $query->select('nfc_tokens.token_id')
                      ->from('nfc_tokens')
                      ->join('dynamic_content', 'nfc_tokens.content_id', '=', 'dynamic_content.content_id')
                      ->join('content_businesses', 'dynamic_content.id', '=', 'content_businesses.dynamic_content_id')
                      ->where('content_businesses.business_type', 'restaurant');
            })
            ->update(['content_type' => 'MENU']);

        // 2. Restaurar dynamic_content
        DB::table('dynamic_content')
            ->whereIn('id', function($query) {
                $query->select('dynamic_content_id')
                      ->from('content_businesses')
                      ->where('business_type', 'restaurant');
            })
            ->update(['type' => 'MENU']);

        // 3. Eliminar productos que eran items de menú
        DB::table('content_products')
            ->whereIn('content_business_id', function($query) {
                $query->select('id')
                      ->from('content_businesses')
                      ->where('business_type', 'restaurant');
            })
            ->delete();

        // 4. Eliminar businesses que eran restaurantes
        DB::table('content_businesses')
            ->where('business_type', 'restaurant')
            ->delete();
    }
};