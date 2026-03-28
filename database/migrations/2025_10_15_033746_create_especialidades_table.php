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
    Schema::create('especialidades', function (Blueprint $table) {
        $table->id(); // Crea una columna 'id' autoincremental
        $table->string('nombre', 100)->unique(); // Campo para "Cardiología", etc. y debe ser único.
        $table->text('descripcion')->nullable(); // Una descripción opcional.
        $table->timestamps(); // Crea las columnas created_at y updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especialidades');
    }
};
