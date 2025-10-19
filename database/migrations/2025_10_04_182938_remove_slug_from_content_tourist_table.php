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
        Schema::table('content_tourist', function (Blueprint $table) {
            // Primero eliminar el índice único antes de eliminar la columna
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_tourist', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->unique('slug');
        });
    }
};
