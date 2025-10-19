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
            // Primero eliminar índices que referencian los campos
            $table->dropIndex(['type', 'sender_name']);
            $table->dropIndex(['type', 'restaurant_name']);
            $table->dropIndex(['type', 'contact_email']);

            // Eliminar campos que ahora están en tablas normalizadas

            // Campos de GIFT (ahora en content_gifts)
            $table->dropColumn([
                'sender_name',
                'recipient_name',
                'message',
            ]);

            // Campos de multimedia (ahora en content_multimedia)
            $table->dropColumn([
                'video_url',
                'video_type',
                'audio_url',
                'audio_type',
                'gallery_images',
            ]);

            // Campos de MENU (ahora en content_menus)
            $table->dropColumn([
                'restaurant_name',
                'restaurant_phone',
                'restaurant_address',
                'restaurant_hours',
                'menu_items',
            ]);

            // Campos de PROFILE (ahora en content_profiles y relacionadas)
            $table->dropColumn([
                'contact_email',
                'contact_phone',
                'contact_website',
                'social_links',
                'skills',
                'bio',
            ]);

            // Campos de EVENT (ahora en content_events)
            $table->dropColumn([
                'event_location',
                'event_start_date',
                'event_end_date',
                'event_organizer',
            ]);

            // Campos de PRODUCT (ahora en content_products)
            $table->dropColumn([
                'product_price',
                'product_currency',
                'product_sku',
                'product_stock',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_content', function (Blueprint $table) {
            // Campos específicos para GIFT
            $table->string('sender_name')->nullable()->comment('De: nombre del remitente');
            $table->string('recipient_name')->nullable()->comment('Para: nombre del destinatario');
            $table->text('message')->nullable()->comment('Mensaje principal del contenido');

            // Campos de multimedia
            $table->string('video_url')->nullable()->comment('URL del video');
            $table->enum('video_type', ['file_upload', 'youtube', 'vimeo', 'direct'])->nullable()->comment('Tipo de video');
            $table->string('audio_url')->nullable()->comment('URL del audio');
            $table->enum('audio_type', ['file_upload', 'youtube_music', 'spotify', 'soundcloud', 'direct'])->nullable()->comment('Tipo de audio');
            $table->json('gallery_images')->nullable()->comment('Array de imágenes de galería');

            // Campos específicos para MENU
            $table->string('restaurant_name')->nullable()->comment('Nombre del restaurante');
            $table->string('restaurant_phone')->nullable()->comment('Teléfono del restaurante');
            $table->text('restaurant_address')->nullable()->comment('Dirección del restaurante');
            $table->string('restaurant_hours')->nullable()->comment('Horarios del restaurante');
            $table->json('menu_items')->nullable()->comment('Items del menú');

            // Campos específicos para PROFILE
            $table->string('contact_email')->nullable()->comment('Email de contacto');
            $table->string('contact_phone')->nullable()->comment('Teléfono de contacto');
            $table->string('contact_website')->nullable()->comment('Sitio web');
            $table->json('social_links')->nullable()->comment('Enlaces a redes sociales');
            $table->json('skills')->nullable()->comment('Habilidades o competencias');
            $table->text('bio')->nullable()->comment('Biografía o descripción personal');

            // Campos específicos para EVENT
            $table->string('event_location')->nullable()->comment('Ubicación del evento');
            $table->datetime('event_start_date')->nullable()->comment('Fecha y hora de inicio');
            $table->datetime('event_end_date')->nullable()->comment('Fecha y hora de fin');
            $table->string('event_organizer')->nullable()->comment('Organizador del evento');

            // Campos específicos para PRODUCT
            $table->decimal('product_price', 10, 2)->nullable()->comment('Precio del producto');
            $table->string('product_currency', 3)->default('USD')->comment('Moneda del precio');
            $table->string('product_sku')->nullable()->comment('SKU del producto');
            $table->integer('product_stock')->nullable()->comment('Stock disponible');

            // Recrear índices
            $table->index(['type', 'sender_name']);
            $table->index(['type', 'restaurant_name']);
            $table->index(['type', 'contact_email']);
        });
    }
};
