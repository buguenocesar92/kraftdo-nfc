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
        // Check if we're using MySQL to use ENUM, otherwise use string
        if (DB::getDriverName() === 'mysql') {
            // MySQL: Actualizar el enum en nfc_tokens para incluir BUSINESS_GROUP
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP', 'BUSINESS_GROUP') NULL COMMENT 'Tipo de contenido del chip'");
            
            // MySQL: Actualizar el enum en dynamic_content para incluir BUSINESS_GROUP
            DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP', 'BUSINESS_GROUP') NOT NULL COMMENT 'Tipo de contenido dinámico'");
        } else {
            // SQLite and other databases: Change to string type
            Schema::table('nfc_tokens', function (Blueprint $table) {
                $table->string('content_type')->nullable()->change();
            });
            
            Schema::table('dynamic_content', function (Blueprint $table) {
                $table->string('type')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if we're using MySQL to use ENUM, otherwise use string
        if (DB::getDriverName() === 'mysql') {
            // MySQL: Revertir los cambios
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP') NULL COMMENT 'Tipo de contenido del chip'");
            
            DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP') NOT NULL COMMENT 'Tipo de contenido dinámico'");
        } else {
            // SQLite and other databases: Keep as string (no change needed for rollback)
            // The validation is handled at the application level
        }
    }
};
