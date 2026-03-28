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
    Schema::create('pacientes', function (Blueprint $table) {
        $table->id(); // ID único del paciente
        
        // Relación con la tabla 'users'. Un paciente puede tener un usuario para el login.
        // onDelete('set null') significa que si se borra el usuario, el paciente no se borra, solo se quita la conexión.
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

        $table->string('nombre_completo');
        $table->string('carnet_identidad', 20)->unique(); // Cédula de Identidad (única)
        $table->date('fecha_nacimiento');
        $table->enum('genero', ['Masculino', 'Femenino', 'Otro']);
        $table->string('telefono', 25);
        $table->string('direccion')->nullable();
        $table->string('email')->unique()->nullable(); // Email opcional del paciente

        $table->text('antecedentes_medicos')->nullable();
        $table->text('alergias')->nullable();

        $table->timestamps(); // Columnas created_at y updated_at
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
