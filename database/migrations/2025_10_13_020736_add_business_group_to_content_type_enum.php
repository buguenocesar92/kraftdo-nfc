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
            // STEP 1: Migrate existing incompatible data first
            // Convert any 'MENU' types to 'BUSINESS' (legacy data migration)
            DB::table('nfc_tokens')->where('content_type', 'MENU')->update(['content_type' => 'BUSINESS']);
            DB::table('dynamic_content')->where('type', 'MENU')->update(['type' => 'BUSINESS']);
            
            // Convert any other potential legacy types
            $legacyTypes = ['RESTAURANT', 'FOOD', 'DINING'];
            foreach ($legacyTypes as $legacyType) {
                DB::table('nfc_tokens')->where('content_type', $legacyType)->update(['content_type' => 'BUSINESS']);
                DB::table('dynamic_content')->where('type', $legacyType)->update(['type' => 'BUSINESS']);
            }
            
            // STEP 2: First change to a temporary broader ENUM to include both old and new values
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP', 'BUSINESS_GROUP', 'MENU', 'RESTAURANT', 'FOOD', 'DINING') NULL COMMENT 'Tipo de contenido del chip'");
            DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP', 'BUSINESS_GROUP', 'MENU', 'RESTAURANT', 'FOOD', 'DINING') NOT NULL COMMENT 'Tipo de contenido dinámico'");
            
            // STEP 3: Now safely change to the final ENUM with only valid values
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS', 'BUS_STOP', 'BUSINESS_GROUP') NULL COMMENT 'Tipo de contenido del chip'");
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
