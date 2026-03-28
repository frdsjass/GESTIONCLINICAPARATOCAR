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
        Schema::create('historia_gastro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')
                  ->constrained('historias_clinicas')
                  ->onDelete('cascade');

            // Campos específicos
            $table->text('resumen_endoscopia')->nullable();
            $table->text('resumen_colonoscopia')->nullable();
            $table->string('prueba_h_pylori')->nullable()->comment('Ej: Positivo, Negativo');
            $table->text('dieta_intolerancias')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_gastro');
    }
};