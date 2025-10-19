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
        Schema::table('content_tourist', function (Blueprint $table) {
            // Agregar campos que faltan para landing pages turísticas completas
            $table->string('place_type')->nullable()->after('location_name')->comment('monumento, naturaleza, patrimonio, plaza, etc.');
            $table->text('history')->nullable()->after('location_address')->comment('Historia completa del lugar');
            $table->json('practical_info')->nullable()->after('longitude')->comment('Horarios detallados, precios, accesos');
            $table->json('gallery_images')->nullable()->after('practical_info')->comment('Galería de múltiples imágenes');
            $table->string('slug')->nullable()->after('gallery_images')->comment('URL amigable para la landing page');

            // Actualizar campos existentes para ser más específicos
            $table->dropColumn(['opening_hours', 'entrance_fee', 'fee_currency', 'website_url', 'phone']);
        });

        // Agregar campos nuevos con información más detallada
        Schema::table('content_tourist', function (Blueprint $table) {
            $table->string('contact_phone')->nullable()->after('slug');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->string('website_url')->nullable()->after('contact_email');
            $table->json('opening_hours')->nullable()->after('website_url')->comment('Horarios estructurados por día');
            $table->json('pricing_info')->nullable()->after('opening_hours')->comment('Información de precios estructurada');
            $table->json('accessibility_info')->nullable()->after('pricing_info')->comment('Información de accesibilidad');
            $table->json('services')->nullable()->after('accessibility_info')->comment('Servicios disponibles');
            $table->json('attractions')->nullable()->after('services')->comment('Atracciones principales');
            $table->string('best_time_to_visit')->nullable()->after('attractions');
            $table->json('languages_spoken')->nullable()->after('best_time_to_visit');

            // Índices para búsqueda
            $table->index('place_type');
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_tourist', function (Blueprint $table) {
            $table->dropIndex(['place_type']);
            $table->dropUnique(['slug']);

            $table->dropColumn([
                'place_type',
                'history',
                'practical_info',
                'gallery_images',
                'slug',
                'contact_phone',
                'contact_email',
                'website_url',
                'opening_hours',
                'pricing_info',
                'accessibility_info',
                'services',
                'attractions',
                'best_time_to_visit',
                'languages_spoken',
            ]);
        });

        // Restaurar campos originales
        Schema::table('content_tourist', function (Blueprint $table) {
            $table->string('opening_hours')->nullable();
            $table->decimal('entrance_fee', 8, 2)->nullable();
            $table->string('fee_currency', 3)->default('USD');
            $table->string('website_url')->nullable();
            $table->string('phone')->nullable();
        });
    }
};
