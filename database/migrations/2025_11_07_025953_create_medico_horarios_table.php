<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico_horarios', function (Blueprint $table) {
            $table->id();
            
            // 1. El ID del médico al que pertenece este horario
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('cascade');
            
            // 2. El día de la semana (Lunes, Martes, etc.)
            // Usamos un número: 1=Lunes, 2=Martes, ... 7=Domingo
            $table->unsignedTinyInteger('dia_semana'); 
            
            // 3. Las horas de inicio y fin
            $table->time('hora_inicio');
            $table->time('hora_fin');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico_horarios');
    }
};