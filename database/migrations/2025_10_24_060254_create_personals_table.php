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
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            
            // Conexión con la tabla Users. Si se borra el user, se borra el personal.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // --- ¡TUS NUEVOS CAMPOS! ---
            $table->string('carnet_identidad')->unique();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('estado', 20)->default('Activo'); // ¡Para Activo/Inactivo!
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
