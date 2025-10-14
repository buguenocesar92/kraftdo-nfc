<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table already exists to avoid conflicts in production
        if (!Schema::hasTable('routes')) {
            Schema::create('routes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bus_stop_id')->constrained('bus_stops')->onDelete('cascade');
                $table->string('name')->comment('Nombre de la línea (ej: Línea 1 - Centro/Hospital)');
                $table->string('route_number')->comment('Número de la línea (ej: 1, 2, 3)');
                $table->string('origin')->comment('Punto de origen del recorrido');
                $table->string('destination')->comment('Destino final del recorrido');
                $table->integer('fare')->nullable()->comment('Tarifa en centavos');
                $table->string('currency', 3)->default('CLP')->comment('Moneda de la tarifa');
                $table->string('operator')->nullable()->comment('Empresa operadora');
                $table->string('color', 7)->default('#007BFF')->comment('Color de identificación de la línea');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['bus_stop_id', 'is_active']);
                $table->index('route_number');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};