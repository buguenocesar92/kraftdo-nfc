<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para MySQL, necesitamos modificar el enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS') NULL");
        } else {
            // Para SQLite, primero verificar si la tabla y columna existen
            if (Schema::hasTable('nfc_tokens') && Schema::hasColumn('nfc_tokens', 'content_type')) {
                // En SQLite, intentamos recrear la tabla, pero solo si es necesario
                // Por simplicidad en tests, no hacemos nada ya que SQLite es más flexible con tipos
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el enum al estado anterior
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT') NULL");
        } else {
            // Para SQLite en down, tampoco hacemos nada
            // SQLite es más flexible con los tipos de datos
        }
    }
};
