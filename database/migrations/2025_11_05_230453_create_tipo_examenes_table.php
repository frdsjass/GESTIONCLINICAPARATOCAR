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
        // Esta tabla es el "catálogo" de exámenes que ofrece la clínica
        Schema::create('tipo_examenes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "Hemograma Completo"
            $table->text('descripcion')->nullable(); // Ej: "Incluye recuento de glóbulos rojos, blancos, etc."
            $table->decimal('precio', 10, 2)->nullable(); // Precio del exámen
            $table->string('estado')->default('Activo'); // Para poder activarlos o desactivarlos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_examenes');
    }
};