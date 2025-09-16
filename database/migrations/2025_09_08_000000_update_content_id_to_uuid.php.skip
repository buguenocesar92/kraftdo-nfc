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
        Schema::table('dynamic_content', function (Blueprint $table) {
            // Actualizar content_id para soportar UUIDs (36 caracteres)
            $table->string('content_id', 36)->change()->comment('UUID único del contenido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_content', function (Blueprint $table) {
            // Revertir a longitud anterior
            $table->string('content_id')->change()->comment('ID único del contenido (ABCD123, XYZ789, etc.)');
        });
    }
};