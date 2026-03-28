<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A9 9 0 1118.879 7.5M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Mi Perfil
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto space-y-10">

            {{-- Información del Perfil --}}
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-center mb-6 border-b pb-3">
                        <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full mr-3">
                            <i class="fas fa-user text-lg"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800">Información de Perfil</h3>
                    </div>
                    @livewire('profile.update-profile-information-form')
                </div>
            @endif

            {{-- Actualizar Contraseña --}}
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-center mb-6 border-b pb-3">
                        <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full mr-3">
                            <i class="fas fa-lock text-lg"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800">Actualizar Contraseña</h3>
                    </div>
                    @livewire('profile.update-password-form')
                </div>
            @endif

            {{-- Autenticación de Dos Factores --}}
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-center mb-6 border-b pb-3">
                        <div class="bg-green-100 text-green-600 p-3 rounded-full mr-3">
                            <i class="fas fa-shield-alt text-lg"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800">Autenticación de Dos Pasos</h3>
                    </div>
                    @livewire('profile.two-factor-authentication-form')
                </div>
            @endif

            {{-- Cierre de Sesiones --}}
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="flex items-center mb-6 border-b pb-3">
                    <div class="bg-red-100 text-red-600 p-3 rounded-full mr-3">
                        <i class="fas fa-sign-out-alt text-lg"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Cerrar Sesiones en Otros Navegadores</h3>
                </div>
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            {{-- ¡ELIMINADO! La sección de "Eliminar Cuenta" se ha ido. --}}

        </div>
    </div>
</x-app-layout>