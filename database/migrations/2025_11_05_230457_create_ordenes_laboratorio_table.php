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
        // ¡CORRECCIÓN! El nombre de la tabla debe ser 'ordenes_laboratorios' (en plural)
        Schema::create('ordenes_laboratorios', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('medico_id')->constrained('medicos');
            $table->foreignId('cita_id')->nullable()->constrained('citas'); // Puede ser una orden sin cita previa

            $table->string('estado')->default('Pendiente'); // Ej: Pendiente, Muestra Tomada, Resultados Listos, Cancelada
            $table->text('notas_medico')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_laboratorios');
    }
};