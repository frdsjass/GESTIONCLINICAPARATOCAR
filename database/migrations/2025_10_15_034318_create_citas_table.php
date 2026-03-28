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
    Schema::create('citas', function (Blueprint $table) {
        $table->id();

        // Conexión con el paciente y el médico
        $table->foreignId('paciente_id')->constrained('pacientes');
        $table->foreignId('medico_id')->constrained('medicos');

        // Fecha y hora de la cita
        $table->dateTime('fecha_hora_inicio');
        $table->dateTime('fecha_hora_fin');

        $table->string('motivo_consulta');

        // Estado para gestionar el flujo de la cita
        $table->enum('estado', [
            'Programada', 
            'Confirmada', 
            'Completada', 
            'Cancelada', 
            'No Asistió'
        ])->default('Programada');

        $table->text('notas_recepcion')->nullable(); // Notas internas

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
