<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\RedirectBasedOnRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            // Verificar si es una petición de login
            if ($request->is('login') && $request->isMethod('post')) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                $minutes = ceil($retryAfter / 60);
                
                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'Límite de intentos alcanzado. Por favor, inténtalo de nuevo en ' . $minutes . ' ' . ($minutes > 1 ? 'minutos' : 'minuto') . '.'
                    ]);
            }
            
            // Para cualquier otro caso, mostrar la página de error por defecto
            return response()->view('errors.429', [], 429);
        });
        
    })->create();