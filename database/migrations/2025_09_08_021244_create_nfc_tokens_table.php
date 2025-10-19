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
        Schema::create('nfc_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token_id')->unique()->comment('ID única del chip NFC');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable()->comment('Nombre descriptivo del chip');
            $table->enum('content_type', ['MENU', 'GIFT', 'TOURIST', 'PROFILE', 'EVENT', 'PRODUCT'])
                ->nullable()->comment('Tipo de contenido del chip');
            $table->enum('customization_plan', ['BASIC', 'STANDARD', 'PREMIUM', 'DELUXE'])
                ->default('BASIC')->comment('Plan de personalización');

            // Campos de pricing y ROI
            $table->decimal('purchase_price', 10, 2)->nullable()->comment('Precio de compra');
            $table->timestamp('purchased_at')->nullable()->comment('Fecha de compra');
            $table->text('purchase_notes')->nullable()->comment('Notas de compra');
            $table->string('purchase_currency', 3)->default('USD')->comment('Moneda de compra');
            $table->decimal('cost_per_view', 10, 4)->nullable()->comment('Costo por visualización');
            $table->integer('total_investment_views')->default(0)->comment('Total de vistas para ROI');

            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['user_id', 'is_active']);
            $table->index('content_type');
            $table->index('token_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfc_tokens');
    }
};
