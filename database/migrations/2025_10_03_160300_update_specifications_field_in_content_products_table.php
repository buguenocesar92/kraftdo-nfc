<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remover la restricción JSON del campo specifications para permitir texto simple
        // Solo aplicar en MySQL, SQLite no soporta MODIFY
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE content_products MODIFY specifications LONGTEXT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir: volver a agregar la restricción JSON
        // Solo aplicar en MySQL, SQLite no soporta MODIFY
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE content_products MODIFY specifications LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`))');
        }
    }
};
