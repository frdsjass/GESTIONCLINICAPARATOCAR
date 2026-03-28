<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'nombre_completo' => ['required', 'string', 'max:255'],
            'carnet_identidad' => ['required', 'string', 'max:20', 'unique:pacientes'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'string', 'in:Masculino,Femenino,Otro'],
            'telefono' => ['required', 'string', 'max:25'],
            
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'carnet_identidad.unique' => 'Este carnet de identidad ya está registrado.',
            'email.unique' => 'Este email ya está registrado.',
        ])->validate();

        return DB::transaction(function () use ($input) {
            
            $user = User::create([
                'name' => $input['nombre_completo'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $user->assignRole('Paciente');

            Paciente::create([
                'user_id' => $user->id,
                'nombre_completo' => $input['nombre_completo'],
                'carnet_identidad' => $input['carnet_identidad'],
                'fecha_nacimiento' => $input['fecha_nacimiento'],
                'genero' => $input['genero'],
                'telefono' => $input['telefono'],
                'email' => $input['email'],
                'estado' => 'Activo',
            ]);

            $this->createTeam($user);

            return $user;
        });
    }

    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}