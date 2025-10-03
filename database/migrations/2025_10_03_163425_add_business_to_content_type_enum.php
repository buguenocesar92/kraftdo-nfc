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
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS') NULL");
        } else {
            // Para otros drivers, usamos el método estándar de Laravel
            Schema::table('nfc_tokens', function (Blueprint $table) {
                $table->dropColumn('content_type');
            });
            
            Schema::table('nfc_tokens', function (Blueprint $table) {
                $table->enum('content_type', ['MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT', 'BUSINESS'])
                      ->nullable()
                      ->comment('Tipo de contenido del chip')
                      ->after('name');
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
            DB::statement("ALTER TABLE nfc_tokens MODIFY COLUMN content_type ENUM('MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT') NULL");
        } else {
            Schema::table('nfc_tokens', function (Blueprint $table) {
                $table->dropColumn('content_type');
            });
            
            Schema::table('nfc_tokens', function (Blueprint $table) {
                $table->enum('content_type', ['MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT'])
                      ->nullable()
                      ->comment('Tipo de contenido del chip')
                      ->after('name');
            });
        }
    }
};