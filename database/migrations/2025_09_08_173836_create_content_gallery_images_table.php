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
        Schema::create('content_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_multimedia_id')->constrained('content_multimedia')->onDelete('cascade');
            $table->string('image_path')->nullable()->comment('Ruta del archivo de imagen subido');
            $table->string('image_url')->nullable()->comment('URL externa de la imagen');
            $table->string('alt_text')->nullable()->comment('Texto alternativo para accesibilidad');
            $table->string('caption')->nullable()->comment('Pie de foto/descripción');
            $table->integer('sort_order')->default(0)->comment('Orden de visualización');
            $table->enum('type', ['upload', 'url'])->default('upload')->comment('Tipo de imagen: archivo subido o URL externa');
            $table->json('metadata')->nullable()->comment('Metadatos adicionales (dimensiones, tamaño, etc.)');
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index('content_multimedia_id');
            $table->index(['content_multimedia_id', 'sort_order']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_gallery_images');
    }
};
