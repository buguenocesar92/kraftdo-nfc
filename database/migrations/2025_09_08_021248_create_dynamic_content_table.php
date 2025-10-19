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
        Schema::create('dynamic_content', function (Blueprint $table) {
            $table->id();
            $table->string('content_id')->unique()->comment('ID único del contenido (ABCD123, XYZ789, etc.)');
            $table->enum('type', ['MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT'])
                ->comment('Tipo de contenido NFC');
            $table->string('gift_subtype')->nullable()->comment('Subtipo de regalo (anniversary, birthday, etc.)');
            $table->string('tier')->default('sweet')->comment('Tier del contenido (sweet, luxury, forever)');
            $table->string('title')->comment('Título principal del contenido');
            $table->text('description')->nullable()->comment('Descripción del contenido');
            $table->json('data')->comment('Datos específicos del tipo en formato JSON');
            $table->string('image_url')->nullable()->comment('URL de la imagen principal');

            // Sistema de publicación
            $table->boolean('is_active')->default(true)->comment('Estado activo/inactivo');
            $table->enum('status', ['draft', 'published', 'paused'])->default('draft')->comment('Estado de publicación');
            $table->timestamp('published_at')->nullable()->comment('Fecha de publicación');
            $table->timestamp('last_draft_update')->nullable()->comment('Última actualización en borrador');
            $table->json('post_publish_modifications')->nullable()->comment('Modificaciones después de publicar');
            $table->json('published_snapshot')->nullable()->comment('Snapshot al momento de publicar');

            // Relaciones
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('nfc_token_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['type', 'is_active']);
            $table->index(['content_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['nfc_token_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_content');
    }
};
