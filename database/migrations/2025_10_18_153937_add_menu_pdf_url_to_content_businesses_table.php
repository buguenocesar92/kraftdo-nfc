<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('content_businesses', function (Blueprint $table) {
            $table->string('menu_pdf_url')->nullable()->after('catalog_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_businesses', function (Blueprint $table) {
            $table->dropColumn('menu_pdf_url');
        });
    }
};
