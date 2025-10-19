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
        Schema::table('dynamic_content', function (Blueprint $table) {
            // Agregar referencias opcionales a las tablas especializadas
            // Estas no son llaves foráneas estrictas porque dependen del tipo
            $table->unsignedBigInteger('multimedia_id')->nullable()->after('data');
            $table->unsignedBigInteger('gift_id')->nullable()->after('multimedia_id');
            $table->unsignedBigInteger('menu_id')->nullable()->after('gift_id');
            $table->unsignedBigInteger('profile_id')->nullable()->after('menu_id');
            $table->unsignedBigInteger('event_id')->nullable()->after('profile_id');
            $table->unsignedBigInteger('product_id')->nullable()->after('event_id');
            $table->unsignedBigInteger('tourist_id')->nullable()->after('product_id');

            // Agregar índices para mejorar rendimiento
            $table->index(['type', 'multimedia_id']);
            $table->index(['type', 'gift_id']);
            $table->index(['type', 'menu_id']);
            $table->index(['type', 'profile_id']);

            // Comentarios para claridad
            $table->comment('Tabla principal para contenido dinámico NFC con referencias a tablas especializadas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_content', function (Blueprint $table) {
            $table->dropIndex(['type', 'multimedia_id']);
            $table->dropIndex(['type', 'gift_id']);
            $table->dropIndex(['type', 'menu_id']);
            $table->dropIndex(['type', 'profile_id']);

            $table->dropColumn([
                'multimedia_id',
                'gift_id',
                'menu_id',
                'profile_id',
                'event_id',
                'product_id',
                'tourist_id',
            ]);
        });
    }
};
