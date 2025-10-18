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
            // Remove the old PDF field if it exists
            if (Schema::hasColumn('content_businesses', 'menu_pdf_url')) {
                $table->dropColumn('menu_pdf_url');
            }
            
            // Don't add menu_images here since we're using a separate table now
            // The menu images are now in the content_menu_images table
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
            
            // Remove menu_images field if it exists
            if (Schema::hasColumn('content_businesses', 'menu_images')) {
                $table->dropColumn('menu_images');
            }
        });
    }
};
