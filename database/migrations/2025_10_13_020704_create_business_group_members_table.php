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
        Schema::create('business_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_group_id')->constrained('content_business_groups')->onDelete('cascade');
            $table->foreignId('member_business_id')->constrained('content_businesses')->onDelete('cascade');
            $table->integer('display_order')->default(0); // Para ordenar en el grid
            $table->boolean('is_featured')->default(false); // Para destacar algunos negocios
            $table->json('custom_position')->nullable(); // Para posiciones específicas en mapa
            $table->string('member_status')->default('active'); // active, inactive, seasonal
            $table->text('member_notes')->nullable(); // notas específicas del miembro
            $table->timestamps();
            
            // Evitar duplicados
            $table->unique(['business_group_id', 'member_business_id'], 'unique_group_member');
            $table->index(['business_group_id', 'display_order']);
            $table->index(['member_business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_group_members');
    }
};