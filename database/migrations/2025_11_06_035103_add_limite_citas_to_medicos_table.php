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
        Schema::table('medicos', function (Blueprint $table) {
            // Añadimos la columna para el límite de citas diarias
            $table->integer('limite_citas_dia')->default(20)->after('direccion'); // La ponemos después de 'direccion' (o donde quieras)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicos', function (Blueprint $table) {
            $table->dropColumn('limite_citas_dia');
        });
    }
};