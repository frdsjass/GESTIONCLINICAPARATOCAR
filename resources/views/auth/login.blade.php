<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            {{-- Dejamos el slot del logo vacío para que se centre bien --}}
        </x-slot>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#0A2342]">Clínica Más Cerca del Cielo</h1>
            <h2 class="text-xl text-gray-600 mt-2">Iniciar Sesión</h2>
        </div>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                
                {{-- ============ INICIO DE LA CORRECCIÓN ============ --}}
                {{-- Esta línea es la que muestra el error de "throttle" (límite de intentos) --}}
                <x-input-error for="email" class="mt-2" />
                {{-- ============ FIN DE LA CORRECCIÓN ============ --}}
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Contraseña') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error for="password" class="mt-2" /> {{-- (Añadido por buena práctica) --}}
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Recuérdame') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif

                <x-button class="ms-4" style="background-color: #4FBDBA; color: white;">
                    {{ __('Iniciar Sesión') }}
                </x-button>
            </div>
            
            <div class="text-center mt-6 border-t pt-4">
                 <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('register') }}">
                    ¿No tienes una cuenta? Regístrate aquí
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>