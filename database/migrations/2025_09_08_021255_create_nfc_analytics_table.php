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
        Schema::create('nfc_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('content_id')->index()->comment('ID del contenido accedido');
            $table->string('content_type')->comment('GIFT, MENU, PROFILE, etc.');
            $table->foreignId('nfc_token_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device_type')->nullable()->comment('mobile, desktop, tablet');
            $table->string('browser')->nullable();
            $table->string('referrer')->nullable();
            $table->boolean('is_unique_visit')->default(false)->comment('Primera vez que esta IP accede');
            $table->timestamp('accessed_at');
            $table->timestamps();

            // Índices para consultas rápidas
            $table->index(['content_id', 'content_type']);
            $table->index(['nfc_token_id', 'accessed_at']);
            $table->index(['ip_address', 'content_id']);
            $table->index('accessed_at');
            $table->index(['device_type', 'accessed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfc_analytics');
    }
};