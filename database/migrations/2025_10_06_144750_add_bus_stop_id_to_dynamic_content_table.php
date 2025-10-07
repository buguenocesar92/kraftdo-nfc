<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dynamic_content', function (Blueprint $table) {
            $table->foreignId('bus_stop_id')->nullable()->after('tourist_id')->constrained('bus_stops')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('dynamic_content', function (Blueprint $table) {
            $table->dropForeign(['bus_stop_id']);
            $table->dropColumn('bus_stop_id');
        });
    }
};