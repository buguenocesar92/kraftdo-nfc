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
        Schema::table('content_products', function (Blueprint $table) {
            // Agregar columna para relación directa con ContentBusiness
            $table->unsignedBigInteger('content_business_id')->nullable()->after('dynamic_content_id');
            $table->foreign('content_business_id')->references('id')->on('content_businesses')->onDelete('cascade');
            
            // Llenar la columna con los datos existentes
            $table->index('content_business_id');
        });
        
        // Llenar los datos existentes solo si ambas tablas existen
        if (Schema::hasTable('content_businesses') && Schema::hasTable('content_products')) {
            $businesses = DB::table('content_businesses')->get();
            foreach ($businesses as $business) {
                DB::table('content_products')
                    ->where('dynamic_content_id', $business->dynamic_content_id)
                    ->update(['content_business_id' => $business->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_products', function (Blueprint $table) {
            $table->dropForeign(['content_business_id']);
            $table->dropIndex(['content_business_id']);
            $table->dropColumn('content_business_id');
        });
    }
};
