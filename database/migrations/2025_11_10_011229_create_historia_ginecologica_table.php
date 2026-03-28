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
        Schema::create('historia_ginecologica', function (Blueprint $table) {
            $table->id();
            
            // Llave foránea a la historia clínica principal
            $table->foreignId('historia_clinica_id')
                  ->constrained('historias_clinicas')
                  ->onDelete('cascade');

            // Campos específicos (con el campo 'abortos' eliminado)
            $table->date('fum')->nullable()->comment('Fecha de Última Menstruación');
            $table->integer('gesta')->nullable()->comment('Número de embarazos');
            $table->integer('para')->nullable()->comment('Número de partos');
            $table->string('ciclo_menstrual')->nullable();
            $table->date('ultimo_pap')->nullable()->comment('Fecha último Papanicolau');
            $table->text('notas_eco_mamaria')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_ginecologica');
    }
};