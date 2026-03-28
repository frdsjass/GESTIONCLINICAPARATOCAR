<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Especialidad;

class RolesAndSpecialtiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- CREACIÓN DE ROLES (Con firstOrCreate) ---
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Medico']);
        Role::firstOrCreate(['name' => 'Recepcion']);
        Role::firstOrCreate(['name' => 'Farmaceutico']);
        Role::firstOrCreate(['name' => 'Paciente']);
        Role::firstOrCreate(['name' => 'Laboratorista']); // <-- ¡El nuevo!
        
        // --- CREACIÓN DE ESPECIALIDADES (¡ESTA ES LA CORRECCIÓN!) ---
        // Esto busca por 'nombre'. Si no lo encuentra, lo crea. Si ya existe, no hace nada.
        Especialidad::firstOrCreate(['nombre' => 'Medicina General'], ['descripcion' => 'Atención primaria y diagnóstico general.']);
        Especialidad::firstOrCreate(['nombre' => 'Pediatría'], ['descripcion' => 'Atención médica especializada en niños y adolescentes.']);
        Especialidad::firstOrCreate(['nombre' => 'Cardiología'], ['descripcion' => 'Diagnóstico y tratamiento de enfermedades del corazón.']);
        Especialidad::firstOrCreate(['nombre' => 'Dermatología'], ['descripcion' => 'Tratamiento de enfermedades de la piel, cabello y uñas.']);
        Especialidad::firstOrCreate(['nombre' => 'Ginecología'], ['descripcion' => 'Salud del sistema reproductor femenino.']);
        Especialidad::firstOrCreate(['nombre' => 'Traumatología'], ['descripcion' => 'Tratamiento de lesiones del aparato locomotor.']);
    }
}