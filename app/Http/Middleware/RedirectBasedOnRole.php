<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // ... (Tu lógica de logout y no-logueado está perfecta) ...
        if ($request->routeIs('logout')) {
            return $next($request);
        }
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // --- Lógica para Admin ---
        if ($user->hasRole('Admin')) {
            // (Tu lógica de Admin está bien)
            if ($request->routeIs('doctor.dashboard') || $request->routeIs('reception.dashboard') || $request->routeIs('pharmacy.dashboard')) {
                if (!$request->routeIs('dashboard')) {
                        return redirect()->route('dashboard');
                }
            }
        }
        // --- Lógica para Medico ---
        elseif ($user->hasRole('Medico')) {
            // (Tu lógica de Médico está bien)
            if ($request->is('/')) {
                 return $next($request);
            }
            $allowedDoctorPatterns = [
                'doctor.*', 
                'profile.show', 
                'livewire.update',
            ];
            $isAllowedPattern = false;
            foreach ($allowedDoctorPatterns as $pattern) {
                if ($request->routeIs($pattern)) {
                    $isAllowedPattern = true;
                    break;
                }
            }
            if (!$isAllowedPattern && !$request->routeIs('doctor.dashboard')) {
               return redirect()->route('doctor.dashboard');
            }
        }
        
        // --- Lógica para Recepcion ---
        elseif ($user->hasRole('Recepcion')) {
            
            // --- ¡AQUÍ ESTÁ LA CORRECCIÓN #1! ---
            // Si el Recepcionista intenta ir al dashboard principal (del Admin)...
            if ($request->routeIs('dashboard')) {
                // ...¡redirígelo a SU PROPIO dashboard!
                return redirect()->route('reception.dashboard');
            }
            
            if ($request->is('/')) { return $next($request); } // Permitir raíz
            
            // --- ¡AQUÍ ESTÁ LA CORRECCIÓN #2! ---
            // Cambiamos 'dashboard' por 'reception.*' para que permita ver su *propio* dashboard
            $allowedPatterns = ['appointments', 'patients.manage', 'reception.*', 'profile.show', 'livewire.update'];
            
            $isAllowed = false;
            foreach ($allowedPatterns as $pattern) { 
                if ($request->routeIs($pattern)) { 
                    $isAllowed = true; 
                    break; 
                } 
            }
            
            // Si no está permitido Y no está en su dashboard, lo redirige
            if (!$isAllowed && !$request->routeIs('reception.dashboard')) { 
                 return redirect()->route('reception.dashboard');
            }
        }
        
        // --- Lógica para Farmaceutico ---
        elseif ($user->hasRole('Farmaceutico')) {
             // (Tu lógica de Farmaceutico está bien, pero añadimos la redirección)
             if ($request->routeIs('dashboard')) {
                 return redirect()->route('pharmacy.dashboard');
             }
             if ($request->is('/')) { return $next($request); }
             $allowedPatterns = ['pharmacy.*', 'profile.show', 'livewire.update'];
             $isAllowed = false;
             foreach ($allowedPatterns as $pattern) { if ($request->routeIs($pattern)) { $isAllowed = true; break; } }
             if (!$isAllowed && !$request->routeIs('pharmacy.dashboard')) {
                  return redirect()->route('pharmacy.dashboard');
             }
        }
        
        // ===================================
        // --- Lógica para Laboratorista (¡MODIFICADA!) ---
        // ===================================
        elseif ($user->hasRole('Laboratorista')) {
            
            // --- ¡CAMBIO #1! ---
            // Comentamos este bloque. YA NO queremos redirigir 'dashboard'
            // a 'laboratorio.dashboard'. Ahora 'dashboard' es una página permitida.
            /*
            if ($request->routeIs('dashboard')) {
                return redirect()->route('laboratorio.dashboard');
            }
            */
            
            if ($request->is('/')) { return $next($request); }

            // --- ¡CAMBIO #2! ---
            // Añadimos 'dashboard' a las rutas permitidas para este rol.
            $allowedPatterns = ['dashboard', 'laboratorio.*', 'profile.show', 'livewire.update'];
            
            $isAllowed = false;
            foreach ($allowedPatterns as $pattern) { if ($request->routeIs($pattern)) { $isAllowed = true; break; } }
            
            // Esta lógica ahora funciona: si la ruta no es 'dashboard' O 'laboratorio.*',
            // lo redirigirá a la cola de trabajo (su "fallback").
            if (!$isAllowed && !$request->routeIs('laboratorio.dashboard')) {
                return redirect()->route('laboratorio.dashboard');
            }
        }
        
        // --- Lógica para Paciente ---
        elseif ($user->hasRole('Paciente')) {
             // (Tu lógica de Paciente está bien, pero añadimos la redirección)
             if ($request->routeIs('dashboard')) {
                 return redirect()->route('paciente.dashboard');
             }
            if ($request->is('/')) { return $next($request); }
            $allowedPatterns = ['paciente.*', 'profile.show', 'livewire.update'];
            $isAllowed = false;
            foreach ($allowedPatterns as $pattern) { if ($request->routeIs($pattern)) { $isAllowed = true; break; } }
            if (!$isAllowed && !$request->routeIs('paciente.dashboard')) {
                 return redirect()->route('paciente.dashboard');
            }
        }
        
        // Si ninguna condición redirigió, dejar pasar
        return $next($request);
    }
}