<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;

class EnsureIsNotInactive
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        // 1. Buscamos al usuario por email
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $estado = 'Activo'; // Valor por defecto

            // 2. Revisar el rol para saber qué tabla consultar
            if ($user->hasRole('Medico')) {
                // Usaremos la relación 'medico'
                $estado = $user->medico?->estado; 
            } elseif ($user->hasRole('Recepcion') || $user->hasRole('Farmaceutico')) {
                // Usaremos la relación 'personal'
                $estado = $user->personal?->estado;
            } elseif ($user->hasRole('Paciente')) {
                // Usaremos la relación 'paciente'
                $estado = $user->paciente?->estado;
            } 
            // NOTA: Los Admin no tienen estado en una tabla de detalles.

            // 3. Bloquear si el estado es Inactivo
            if ($estado === 'Inactivo') {
                // Deniega el login y manda un mensaje de error
                throw ValidationException::withMessages([
                    Fortify::username() => [__('Tu cuenta ha sido deshabilitada. Contacta al administrador.')],
                ]);
            }
        }

        // 4. Si el usuario está activo (o no se encontró/se dejará pasar), continúa
        return $next($request);
    }
}
