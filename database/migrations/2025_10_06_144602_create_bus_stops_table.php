<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Check if table already exists to avoid conflicts in production
        if (! Schema::hasTable('bus_stops')) {
            Schema::create('bus_stops', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
                $table->string('stop_id')->unique()->comment('ID único del paradero (ej: PAR001)');
                $table->string('name')->comment('Nombre del paradero');
                $table->text('address')->comment('Dirección completa del paradero');
                $table->decimal('latitude', 10, 8)->comment('Latitud GPS');
                $table->decimal('longitude', 11, 8)->comment('Longitud GPS');

                // Información de la municipalidad
                $table->string('municipality_name')->comment('Nombre de la municipalidad');
                $table->string('municipality_logo_url')->nullable()->comment('URL del logo municipal');
                $table->text('municipality_description')->nullable()->comment('Descripción de la comuna');
                $table->string('municipality_website')->nullable()->comment('Sitio web municipal');

                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['stop_id', 'is_active']);
                $table->index('municipality_name');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_stops');
    }
};
