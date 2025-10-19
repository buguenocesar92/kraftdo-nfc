<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table already exists to avoid conflicts in production
        if (! Schema::hasTable('content_business_groups')) {
            Schema::create('content_business_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
                $table->string('group_name'); // "Ecoparque Machalí"
                $table->text('description')->nullable();
                $table->string('address')->nullable();
                $table->json('location_coordinates')->nullable(); // {lat: -34.xxx, lng: -70.xxx}
                $table->string('contact_phone')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_website')->nullable();
                $table->json('operating_hours')->nullable(); // {"monday": "09:00-18:00", ...}
                $table->string('banner_image')->nullable();
                $table->string('logo_url')->nullable();
                $table->string('group_type')->default('food_court'); // food_court, mall, market, fair
                $table->json('amenities')->nullable(); // ["parking", "wifi", "restrooms"]
                $table->text('special_instructions')->nullable(); // horarios especiales, eventos, etc.
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['group_type', 'is_active']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_business_groups');
    }
};
