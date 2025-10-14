<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table already exists to avoid conflicts in production
        if (!Schema::hasTable('utility_phones')) {
            Schema::create('utility_phones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bus_stop_id')->constrained('bus_stops')->onDelete('cascade');
                $table->string('name')->comment('Nombre del servicio (ej: Bomberos Machalí)');
                $table->string('phone_number')->comment('Número telefónico');
                $table->enum('category', ['emergencia', 'salud', 'municipal', 'servicios', 'transporte'])->comment('Categoría del servicio');
                $table->text('description')->nullable()->comment('Descripción del servicio');
                $table->string('icon', 10)->nullable()->comment('Emoji o icono representativo');
                $table->boolean('is_emergency')->default(false)->comment('¿Es un número de emergencia?');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['bus_stop_id', 'category', 'is_active']);
                $table->index('is_emergency');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_phones');
    }
};