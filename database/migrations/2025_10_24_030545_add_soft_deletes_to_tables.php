<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->softDeletes(); // Añade la columna deleted_at
        });
        Schema::table('medicos', function (Blueprint $table) {
            $table->softDeletes(); // Añade la columna deleted_at
        });
        Schema::table('especialidades', function (Blueprint $table) {
            $table->softDeletes(); // Añade la columna deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('medicos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('especialidades', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};