<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\EnsureIsNotInactive; // <-- ¡AÑADIDO!
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\AttemptToAuthenticate; // <-- NECESARIO PARA EL PIPELINE
use Laravel\Fortify\Actions\PrepareAuthenticatedSession; // <-- NECESARIO PARA EL PIPELINE
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // --- PIPELINE DE AUTENTICACIÓN (LOGIN) ---
        Fortify::authenticateThrough(function (Request $request) {
            return array_filter([
                EnsureIsNotInactive::class, // <-- ¡NUESTRA REGLA DE BLOQUEO!
                config('fortify.limiters.login') ? null : RedirectIfTwoFactorAuthenticatable::class,
                AttemptToAuthenticate::class, // <-- ESTA ES LA REGLA QUE VERIFICA LA CONTRASEÑA
                PrepareAuthenticatedSession::class,
            ]);
        });
        // ------------------------------------------

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
