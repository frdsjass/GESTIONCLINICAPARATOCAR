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
    Schema::create('medicos', function (Blueprint $table) {
        $table->id();

        // Conecta al médico con su cuenta de usuario para el login.
        // Si el usuario es eliminado, el registro del médico también se elimina.
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // Conecta al médico con su especialidad.
        $table->foreignId('especialidad_id')->constrained('especialidades');

        // Matrícula profesional o un identificador único del colegio médico.
        $table->string('cedula_profesional')->unique();

        $table->string('telefono', 25)->nullable();
        $table->string('direccion')->nullable();

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
