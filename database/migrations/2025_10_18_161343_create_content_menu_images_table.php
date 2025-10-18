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
        Schema::create('content_menu_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_business_id')->constrained('content_businesses')->onDelete('cascade');
            $table->string('image_url'); // Ruta de la imagen
            $table->string('title')->nullable(); // Título opcional (ej: "Entrantes", "Platos Principales")
            $table->text('description')->nullable(); // Descripción opcional
            $table->integer('display_order')->default(0); // Orden de visualización
            $table->boolean('is_active')->default(true); // Para ocultar/mostrar temporalmente
            $table->timestamps();
            
            // Índices para performance
            $table->index(['content_business_id', 'display_order']);
            $table->index(['content_business_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_menu_images');
    }
};
