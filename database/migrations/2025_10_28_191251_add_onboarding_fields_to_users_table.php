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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false)->after('email_verified_at');
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_completed');
            $table->json('onboarding_progress')->nullable()->after('onboarding_completed_at');
            $table->boolean('is_first_time_user')->default(true)->after('onboarding_progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'onboarding_completed',
                'onboarding_completed_at',
                'onboarding_progress',
                'is_first_time_user'
            ]);
        });
    }
};