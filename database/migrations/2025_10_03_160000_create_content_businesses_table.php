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
        // Check if table already exists to avoid conflicts in production
        if (!Schema::hasTable('content_businesses')) {
            Schema::create('content_businesses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
                $table->string('business_name');
                $table->text('description')->nullable();
                $table->string('business_type')->nullable();
                $table->string('logo_url')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_website')->nullable();
                $table->text('address')->nullable();
                $table->string('google_maps_url')->nullable();
                $table->string('google_reviews_url')->nullable();
                $table->string('google_place_id')->nullable();
                $table->string('instagram_url')->nullable();
                $table->string('facebook_url')->nullable();
                $table->string('whatsapp_number')->nullable();
                $table->json('operating_hours')->nullable();
                $table->json('services')->nullable();
                $table->boolean('catalog_enabled')->default(false);
                $table->json('color_palette')->nullable();
                $table->timestamps();
                
                $table->unique('dynamic_content_id');
                $table->index('business_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_businesses');
    }
};
