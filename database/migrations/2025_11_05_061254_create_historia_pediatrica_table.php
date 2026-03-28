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
        Schema::create('historia_pediatrica', function (Blueprint $table) {
            $table->id();

            // Conexión con la consulta principal en historias_clinicas
            $table->foreignId('historia_clinica_id')
                  ->constrained('historias_clinicas')
                  ->onDelete('cascade'); // Si se borra la consulta, se borra esto

            // --- CAMPOS ESPECÍFICOS DE PEDIATRÍA ---
            $table->decimal('perimetro_cefalico_cm', 5, 2)->nullable();
            $table->text('esquema_vacunacion')->nullable();
            $table->text('desarrollo_psicomotor')->nullable();
            $table->text('alimentacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_pediatrica');
    }
};