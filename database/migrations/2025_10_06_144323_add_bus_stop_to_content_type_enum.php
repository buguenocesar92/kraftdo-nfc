<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Solo aplicar cambios en MySQL (producción), SQLite no soporta MODIFY COLUMN
        if (DB::getDriverName() === 'mysql') {
            // Agregar BUS_STOP al enum de content_type en nfc_tokens (incluye BUSINESS existente)
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP') NULL COMMENT 'Tipo de contenido del chip'");

            // Actualizar la tabla dynamic_content (sin 's')
            if (Schema::hasTable('dynamic_content')) {
                DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP') NOT NULL COMMENT 'Tipo de contenido dinámico'");
            }
        }
        // Para SQLite (tests), no hacemos nada ya que los enums se manejan como strings
    }

    public function down(): void
    {
        // Solo revertir cambios en MySQL
        if (DB::getDriverName() === 'mysql') {
            // Revertir cambios (mantener BUSINESS)
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS') NULL COMMENT 'Tipo de contenido del chip'");

            if (Schema::hasTable('dynamic_content')) {
                DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS') NOT NULL COMMENT 'Tipo de contenido dinámico'");
            }
        }
        // Para SQLite (tests), no hacemos nada
    }
};
