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
        Schema::table('content_products', function (Blueprint $table) {
            // Primero eliminar la foreign key constraint
            $table->dropForeign(['dynamic_content_id']);

            // Eliminar la restricción unique en dynamic_content_id
            $table->dropUnique(['dynamic_content_id']);

            // Agregar un índice normal (no único) para optimizar consultas
            $table->index('dynamic_content_id');

            // Recrear la foreign key constraint
            $table->foreign('dynamic_content_id')->references('id')->on('dynamic_content')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_products', function (Blueprint $table) {
            // Revertir: eliminar foreign key, índice normal y agregar la restricción unique
            $table->dropForeign(['dynamic_content_id']);
            $table->dropIndex(['dynamic_content_id']);
            $table->unique('dynamic_content_id');
            $table->foreign('dynamic_content_id')->references('id')->on('dynamic_content')->onDelete('cascade');
        });
    }
};
