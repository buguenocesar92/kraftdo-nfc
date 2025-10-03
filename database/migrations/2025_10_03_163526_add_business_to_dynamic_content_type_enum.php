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
        // Para MySQL, necesitamos modificar el enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE dynamic_content MODIFY COLUMN type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS') NOT NULL");
        } else {
            // Para otros drivers, usamos el método estándar de Laravel
            Schema::table('dynamic_content', function (Blueprint $table) {
                $table->dropColumn('type');
            });
            
            Schema::table('dynamic_content', function (Blueprint $table) {
                $table->enum('type', ['MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS'])
                      ->comment('Tipo de contenido NFC')
                      ->after('content_id');
            });
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
            Schema::table('dynamic_content', function (Blueprint $table) {
                $table->dropColumn('type');
            });
            
            Schema::table('dynamic_content', function (Blueprint $table) {
                $table->enum('type', ['MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT'])
                      ->comment('Tipo de contenido NFC')
                      ->after('content_id');
            });
        }
    }
};