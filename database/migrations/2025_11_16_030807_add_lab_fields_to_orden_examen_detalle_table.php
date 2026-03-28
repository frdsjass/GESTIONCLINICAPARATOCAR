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
        Schema::table('orden_examen_detalle', function (Blueprint $table) {
            // Añadimos el campo para "Valores de Referencia"
            // (después de la columna 'resultados')
            $table->string('valores_referencia')->nullable()->after('resultados');
            
            // Añadimos el campo para "Metodología"
            // (después de 'valores_referencia')
            $table->string('metodologia')->nullable()->after('valores_referencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orden_examen_detalle', function (Blueprint $table) {
            $table->dropColumn('valores_referencia');
            $table->dropColumn('metodologia');
        });
    }
};