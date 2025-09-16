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
        Schema::create('user_gallery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image_path')->comment('Ruta del archivo de imagen');
            $table->string('original_name')->comment('Nombre original del archivo');
            $table->string('alt_text')->nullable()->comment('Texto alternativo');
            $table->integer('file_size')->comment('Tamaño del archivo en bytes');
            $table->string('mime_type')->comment('Tipo MIME del archivo');
            $table->integer('width')->nullable()->comment('Ancho de la imagen en píxeles');
            $table->integer('height')->nullable()->comment('Alto de la imagen en píxeles');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índices
            $table->index(['user_id', 'is_active']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_gallery');
    }
};