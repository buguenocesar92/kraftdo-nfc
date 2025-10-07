<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->json('departure_times')->comment('Array de horarios de salida [06:00, 06:30, ...]');
            $table->integer('frequency_minutes')->nullable()->comment('Frecuencia en minutos entre servicios');
            $table->text('notes')->nullable()->comment('Notas adicionales sobre el horario');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['route_id', 'day_of_week', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};