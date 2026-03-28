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
        Schema::create('orden_examen_detalle', function (Blueprint $table) {
            $table->id();

            // --- ¡CORRECCIÓN! ---
            // Usamos foreignId() para que coincida automáticamente con el 'id' (BigInt) 
            // de la tabla 'ordenes_laboratorios' y le decimos que borre en cascada.
            $table->foreignId('orden_laboratorio_id')
                  ->constrained('ordenes_laboratorios') // ¡Asegúrate que la tabla referenciada esté en plural!
                  ->onDelete('cascade');
            
            // Esta ya estaba bien, pero la estandarizamos
            $table->foreignId('tipo_examen_id')->constrained('tipo_examenes');

            // --- ¡CAMPOS AÑADIDOS! ---
            // El laboratorista llenará estos campos
            $table->text('resultados')->nullable();
            $table->text('notas_laboratorista')->nullable();
            // --- FIN DE CAMPOS AÑADIDOS ---

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_examen_detalle');
    }
};