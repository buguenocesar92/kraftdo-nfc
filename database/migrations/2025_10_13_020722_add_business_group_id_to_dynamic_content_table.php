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
        Schema::table('dynamic_content', function (Blueprint $table) {
            $table->unsignedBigInteger('business_group_id')->nullable()->after('bus_stop_id');
            
            $table->index('business_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_content', function (Blueprint $table) {
            $table->dropIndex(['business_group_id']);
            $table->dropColumn('business_group_id');
        });
    }
};