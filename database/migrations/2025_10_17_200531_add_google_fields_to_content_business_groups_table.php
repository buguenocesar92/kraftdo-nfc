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
        Schema::table('content_business_groups', function (Blueprint $table) {
            $table->string('google_place_id')->nullable()->after('contact_website');
            $table->string('google_reviews_url')->nullable()->after('google_place_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_business_groups', function (Blueprint $table) {
            $table->dropColumn(['google_place_id', 'google_reviews_url']);
        });
    }
};
