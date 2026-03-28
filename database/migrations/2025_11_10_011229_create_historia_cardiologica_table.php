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
        Schema::create('historia_cardiologica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')
                  ->constrained('historias_clinicas')
                  ->onDelete('cascade');

            // Campos específicos
            $table->string('riesgo_cardiovascular')->nullable();
            $table->text('resumen_ecg')->nullable()->comment('Resumen Electrocardiograma');
            $table->text('resumen_ecocardiograma')->nullable();
            $table->string('colesterol_total')->nullable()->comment('Ej: 210 mg/dL');
            $table->string('hdl')->nullable();
            $table->string('ldl')->nullable();
            $table->string('trigliceridos')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_cardiologica');
    }
};