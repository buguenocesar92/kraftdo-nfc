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
            DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS') NOT NULL");
        } else {
            // Para SQLite, no modificamos el enum ya que es más flexible
            if (Schema::hasTable('dynamic_content') && Schema::hasColumn('dynamic_content', 'type')) {
                // SQLite permite valores adicionales sin modificar la estructura
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
            DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT') NOT NULL");
        } else {
            // Para SQLite en down, no hacemos nada
        }
    }
};
