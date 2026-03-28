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
        // --- ¡CORREGIDO! ---
        // El nombre de la tabla es 'orden_laboratorios' (plural)
        $tableName = 'ordenes_laboratorios'; 

        Schema::table($tableName, function (Blueprint $table) {
            // Campo para resultados numéricos/texto (ej: "Glucosa: 110 mg/dL")
            $table->longText('resultados_texto')->nullable()->after('notas_medico');
            
            // Campo para guardar la ruta del archivo PDF
            $table->string('resultado_pdf_path')->nullable()->after('resultados_texto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // --- ¡CORREGIDO! ---
        $tableName = 'ordenes_laboratorios'; 

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('resultados_texto');
            $table->dropColumn('resultado_pdf_path');
        });
    }
};