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
        // Check if table already exists to avoid conflicts in production
        if (! Schema::hasTable('nearby_spots')) {
            Schema::create('nearby_spots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('content_tourist_id')->constrained('content_tourist')->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('latitude', 10, 8);
                $table->decimal('longitude', 11, 8);
                $table->string('spot_type')->comment('restaurante, hotel, transporte, atraccion, etc.');
                $table->decimal('distance_km', 5, 2)->nullable()->comment('Distancia en kilómetros desde el punto principal');
                $table->string('icon')->nullable()->comment('Icono para mostrar en el mapa');
                $table->string('color', 7)->default('#3B82F6')->comment('Color del marcador en formato hex');
                $table->json('additional_info')->nullable()->comment('Información adicional como horarios, precios, etc.');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['content_tourist_id', 'spot_type']);
                $table->index(['latitude', 'longitude']);
                $table->index(['content_tourist_id', 'sort_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nearby_spots');
    }
};
