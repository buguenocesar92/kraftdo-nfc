<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo agregar la columna si no existe
        if (!Schema::hasColumn('content_products', 'name')) {
            Schema::table('content_products', function (Blueprint $table) {
                $table->string('name')->nullable()->after('content_business_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_products', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
