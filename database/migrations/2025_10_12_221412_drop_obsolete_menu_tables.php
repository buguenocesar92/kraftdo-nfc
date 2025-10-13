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
        // Eliminar las tablas obsoletas de menús
        Schema::dropIfExists('content_menu_items');
        Schema::dropIfExists('content_menus');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear las tablas en caso de rollback
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
    }
};