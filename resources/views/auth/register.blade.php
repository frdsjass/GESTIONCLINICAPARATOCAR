<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
        </x-slot>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#0A2342]">Clínica Más Cerca del Cielo</h1>
            <h2 class="text-xl text-gray-600 mt-2">Crear Cuenta de Paciente</h2>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <h3 class="text-lg font-medium text-gray-900 mb-4">Datos del Paciente</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label for="nombre_completo" value="{{ __('Nombre Completo') }}" />
                    <x-input id="nombre_completo" class="block mt-1 w-full" type="text" name="nombre_completo" :value="old('nombre_completo')" required autofocus autocomplete="name" />
                </div>

                <div>
                    <x-label for="carnet_identidad" value="{{ __('Carnet de Identidad (C.I.)') }}" />
                    <x-input id="carnet_identidad" class="block mt-1 w-full" type="text" name="carnet_identidad" :value="old('carnet_identidad')" required />
                </div>

                <div>
                    <x-label for="fecha_nacimiento" value="{{ __('Fecha de Nacimiento') }}" />
                    <x-input id="fecha_nacimiento" class="block mt-1 w-full" type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento')" required />
                </div>
                
                <div>
                    <x-label for="genero" value="{{ __('Género') }}" />
                    <select id="genero" name="genero" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Seleccione...</option>
                        <option value="Masculino" @if(old('genero') == 'Masculino') selected @endif>Masculino</option>
                        <option value="Femenino" @if(old('genero') == 'Femenino') selected @endif>Femenino</option>
                        <option value="Otro" @if(old('genero') == 'Otro') selected @endif>Otro</option>
                    </select>
                </div>
                
                <div class="col-span-2">
                    <x-label for="telefono" value="{{ __('Teléfono / Celular') }}" />
                    <x-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono')" required />
                </div>
            </div>

            <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4 border-t pt-4">Datos de la Cuenta</h3>

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Contraseña') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('¿Ya estás registrado?') }}
                </a>

                <x-button class="ms-4" style="background-color: #4FBDBA; color: white;">
                    {{ __('Registrarse') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>