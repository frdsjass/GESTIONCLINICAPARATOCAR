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
        Schema::create('historia_traumatologica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')
                  ->constrained('historias_clinicas')
                  ->onDelete('cascade');

            // Campos específicos
            $table->string('tipo_lesion')->nullable()->comment('Ej: Fractura, Esguince');
            $table->string('zona_afectada')->nullable()->comment('Ej: Rodilla derecha');
            $table->string('rango_movilidad')->nullable()->comment('Ej: Limitado por dolor');
            $table->string('reflejos')->nullable()->comment('Ej: Conservados');
            $table->text('resumen_pruebas_imagen')->nullable()->comment('Resumen de Rayos X, Tomografía, etc.');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_traumatologica');
    }
};