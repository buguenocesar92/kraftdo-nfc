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
        Schema::table('content_businesses', function (Blueprint $table) {
            // Remove the old PDF field
            $table->dropColumn('menu_pdf_url');
            
            // Add new field for multiple menu images
            $table->json('menu_images')->nullable()->after('catalog_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_businesses', function (Blueprint $table) {
            // Restore the old PDF field
            $table->string('menu_pdf_url')->nullable()->after('catalog_enabled');
            
            // Remove the new images field
            $table->dropColumn('menu_images');
        });
    }
};
