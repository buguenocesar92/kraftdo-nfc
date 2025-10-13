<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar el enum en nfc_tokens para incluir BUSINESS_GROUP
        DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP', 'BUSINESS_GROUP') NULL COMMENT 'Tipo de contenido del chip'");
        
        // Actualizar el enum en dynamic_content para incluir BUSINESS_GROUP
        DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP', 'BUSINESS_GROUP') NOT NULL COMMENT 'Tipo de contenido dinámico'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios
        DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP') NULL COMMENT 'Tipo de contenido del chip'");
        
        DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP') NOT NULL COMMENT 'Tipo de contenido dinámico'");
    }
};
