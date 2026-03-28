<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ];

        if ($user->hasRole('Paciente')) {
            $validationRules = array_merge($validationRules, [
                'carnet_identidad' => ['required', 'string', 'max:20', Rule::unique('pacientes')->ignore($user->paciente->id)],
                'fecha_nacimiento' => ['required', 'date'],
                'genero' => ['required', 'string', 'in:Masculino,Femenino,Otro'],
                'telefono' => ['required', 'string', 'max:25'],
                'direccion' => ['nullable', 'string', 'max:255'],
                'antecedentes_medicos' => ['nullable', 'string'],
                'alergias' => ['nullable', 'string'],
            ]);
        }

        Validator::make($input, $validationRules)->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();

            if ($user->hasRole('Paciente')) {
                $this->updatePacienteProfile($user, $input);
            }
        }
    }

    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        if ($user->hasRole('Paciente')) {
            $this->updatePacienteProfile($user, $input);
        }
        
        $user->sendEmailVerificationNotification();
    }

    protected function updatePacienteProfile(User $user, array $input): void
    {
        if ($user->paciente) {
            $user->paciente->forceFill([
                'nombre_completo' => $input['name'],
                'email' => $input['email'],
                'carnet_identidad' => $input['carnet_identidad'],
                'fecha_nacimiento' => $input['fecha_nacimiento'],
                'genero' => $input['genero'],
                'telefono' => $input['telefono'],
                'direccion' => $input['direccion'] ?? null,
                'antecedentes_medicos' => $input['antecedentes_medicos'] ?? null,
                'alergias' => $input['alergias'] ?? null,
            ])->save();
        }
    }
}