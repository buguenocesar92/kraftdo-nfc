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
        Schema::table('content_multimedia', function (Blueprint $table) {
            // Campos para archivos de video subidos
            $table->string('video_file')->nullable()->after('video_url')->comment('Ruta del archivo de video subido');
            
            // Campos para archivos de audio subidos
            $table->string('audio_file')->nullable()->after('audio_url')->comment('Ruta del archivo de audio subido');
            
            // Campo para múltiples archivos de galería subidos
            $table->json('gallery_files')->nullable()->after('gallery_images')->comment('Array de rutas de archivos de galería subidos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_multimedia', function (Blueprint $table) {
            $table->dropColumn(['video_file', 'audio_file', 'gallery_files']);
        });
    }
};