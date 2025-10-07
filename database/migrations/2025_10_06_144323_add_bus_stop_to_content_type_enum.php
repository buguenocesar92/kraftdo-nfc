<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar BUS_STOP al enum de content_type en nfc_tokens (incluye BUSINESS existente)
        DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP') NULL COMMENT 'Tipo de contenido del chip'");
        
        // Actualizar la tabla dynamic_content (sin 's')
        if (Schema::hasTable('dynamic_content')) {
            DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP') NOT NULL COMMENT 'Tipo de contenido dinámico'");
        }
    }

    public function down(): void
    {
        // Revertir cambios (mantener BUSINESS)
        DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS') NULL COMMENT 'Tipo de contenido del chip'");
        
        if (Schema::hasTable('dynamic_content')) {
            DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS') NOT NULL COMMENT 'Tipo de contenido dinámico'");
        }
    }
};