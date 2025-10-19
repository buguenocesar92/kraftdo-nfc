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
        Schema::table('content_profiles', function (Blueprint $table) {
            $table->string('profession')->nullable()->after('bio');
            $table->string('company')->nullable()->after('profession');
            $table->string('location')->nullable()->after('company');
            $table->string('contact_info')->nullable()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_profiles', function (Blueprint $table) {
            $table->dropColumn(['profession', 'company', 'location', 'contact_info']);
        });
    }
};
