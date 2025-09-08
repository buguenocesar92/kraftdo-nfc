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
        // Tabla para contenido multimedia (compartido entre tipos)
        Schema::create('content_multimedia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->string('video_url')->nullable();
            $table->enum('video_type', ['file_upload', 'youtube', 'vimeo', 'direct'])->nullable();
            $table->string('audio_url')->nullable();
            $table->enum('audio_type', ['file_upload', 'youtube_music', 'spotify', 'soundcloud', 'direct'])->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('settings')->nullable()->comment('Configuraciones de multimedia (autoplay, loop, etc.)');
            $table->timestamps();
            
            $table->index('dynamic_content_id');
        });

        // Tabla específica para contenido GIFT
        Schema::create('content_gifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->string('sender_name')->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
            
            $table->unique('dynamic_content_id');
        });

        // Tabla específica para contenido MENU
        Schema::create('content_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->string('restaurant_name')->nullable();
            $table->string('restaurant_phone')->nullable();
            $table->text('restaurant_address')->nullable();
            $table->string('restaurant_hours')->nullable();
            $table->timestamps();
            
            $table->unique('dynamic_content_id');
        });

        // Tabla para items de menú
        Schema::create('content_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_menu_id')->constrained('content_menus')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('category')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['content_menu_id', 'category']);
            $table->index(['content_menu_id', 'sort_order']);
        });

        // Tabla específica para contenido PROFILE
        Schema::create('content_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_website')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
            
            $table->unique('dynamic_content_id');
        });

        // Tabla para enlaces sociales
        Schema::create('content_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->string('platform'); // instagram, linkedin, twitter, etc.
            $table->string('url');
            $table->string('username')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['dynamic_content_id', 'platform']);
        });

        // Tabla para habilidades
        Schema::create('content_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->string('name');
            $table->integer('level')->nullable()->comment('Nivel de 1-10');
            $table->string('category')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['dynamic_content_id', 'category']);
        });

        // Tabla específica para contenido EVENT
        Schema::create('content_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->string('event_location')->nullable();
            $table->datetime('event_start_date')->nullable();
            $table->datetime('event_end_date')->nullable();
            $table->string('event_organizer')->nullable();
            $table->decimal('ticket_price', 10, 2)->nullable();
            $table->string('ticket_currency', 3)->default('USD');
            $table->string('registration_url')->nullable();
            $table->timestamps();
            
            $table->unique('dynamic_content_id');
            $table->index('event_start_date');
        });

        // Tabla específica para contenido PRODUCT
        Schema::create('content_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('sku')->nullable();
            $table->integer('stock')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->string('brand')->nullable();
            $table->json('specifications')->nullable();
            $table->string('purchase_url')->nullable();
            $table->timestamps();
            
            $table->unique('dynamic_content_id');
            $table->index('sku');
        });

        // Tabla específica para contenido TOURIST
        Schema::create('content_tourist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
            $table->string('location_name');
            $table->text('location_address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('opening_hours')->nullable();
            $table->decimal('entrance_fee', 8, 2)->nullable();
            $table->string('fee_currency', 3)->default('USD');
            $table->string('website_url')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            
            $table->unique('dynamic_content_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_tourist');
        Schema::dropIfExists('content_products');
        Schema::dropIfExists('content_events');
        Schema::dropIfExists('content_skills');
        Schema::dropIfExists('content_social_links');
        Schema::dropIfExists('content_profiles');
        Schema::dropIfExists('content_menu_items');
        Schema::dropIfExists('content_menus');
        Schema::dropIfExists('content_gifts');
        Schema::dropIfExists('content_multimedia');
    }
};
