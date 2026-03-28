<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // <-- Importante

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. CREAR TODOS LOS ROLES ---
        // Usamos firstOrCreate para evitar errores si ya existen
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Medico', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Recepcion', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Farmaceutico', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Paciente', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Laboratorista', 'guard_name' => 'web']);

        // --- 2. CREAR EL USUARIO ADMIN ---
        // Usamos firstOrCreate para evitar duplicados si corres el seeder de nuevo
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@clinica.com'], // Busca por email
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'), // Hashea la contraseña
            ]
        );

        // --- 3. ASIGNAR EL ROL DE ADMIN AL USUARIO ---
        $adminUser->assignRole('Admin');

        // Puedes añadir más usuarios de prueba aquí si quieres
        // Ejemplo de un médico:
        /*
        $medicoUser = User::firstOrCreate(
             ['email' => 'medico@clinica.com'],
             [
                'name' => 'Dr. Ejemplo',
                'password' => Hash::make('password'),
             ]
         );
         $medicoUser->assignRole('Medico');
        */
    }
}