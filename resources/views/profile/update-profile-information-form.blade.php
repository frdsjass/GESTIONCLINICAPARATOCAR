<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Información de Perfil') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Actualiza la información de perfil y la dirección de correo electrónico de tu cuenta.') }}
    </x-slot>

    <x-slot name="form">
        {{-- SE ELIMINÓ EL <div> WRAPPER QUE ESTABA AQUÍ --}}
    
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-6">
                
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Foto') }}" />

                
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full size-20 object-cover">
                </div>

                
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Seleccionar una nueva foto') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Quitar Foto') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        
        {{-- SE MOVIÓ EL X-DATA Y EL X-INIT AQUÍ PARA NO ROMPER EL GRID --}}
        <div class="col-span-6 sm:col-span-6" 
             x-data
             @role('Paciente')
             x-init="
                $wire.state.carnet_identidad = '{{ Auth::user()->paciente->carnet_identidad ?? '' }}';
                $wire.state.fecha_nacimiento = '{{ Auth::user()->paciente->fecha_nacimiento ? Auth::user()->paciente->fecha_nacimiento->format('Y-m-d') : '' }}';
                $wire.state.genero = '{{ Auth::user()->paciente->genero ?? '' }}';
                $wire.state.telefono = '{{ Auth::user()->paciente->telefono ?? '' }}';
                $wire.state.direccion = '{{ Auth::user()->paciente->direccion ?? '' }}';
                $wire.state.antecedentes_medicos = '{{ Auth::user()->paciente->antecedentes_medicos ?? '' }}';
                $wire.state.alergias = '{{ Auth::user()->paciente->alergias ?? '' }}';
             "
             @endrole
        >
            <x-label for="name" value="{{ __('Nombre Completo') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        
        <div class="col-span-6 sm:col-span-6">
            <x-label for="email" value="{{ __('Correo Electrónico') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Tu dirección de correo electrónico no está verificada.') }}

                    <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                        {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.') }}
                    </p>
                @endif
            @endif
        </div>

        @role('Paciente')
        <div class="col-span-6 sm:col-span-6">
            <x-label for="carnet_identidad" value="{{ __('Carnet de Identidad (C.I.)') }}" />
            <x-input id="carnet_identidad" type="text" class="mt-1 block w-full" wire:model="state.carnet_identidad" required />
            <x-input-error for="carnet_identidad" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-6">
            <x-label for="fecha_nacimiento" value="{{ __('Fecha de Nacimiento') }}" />
            <x-input id="fecha_nacimiento" type="date" class="mt-1 block w-full" wire:model="state.fecha_nacimiento" required />
            <x-input-error for="fecha_nacimiento" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-6">
            <x-label for="genero" value="{{ __('Género') }}" />
            <select id="genero" wire:model="state.genero" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">Seleccione...</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
            </select>
            <x-input-error for="genero" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-6">
            <x-label for="telefono" value="{{ __('Teléfono / Celular') }}" />
            <x-input id="telefono" type="text" class="mt-1 block w-full" wire:model="state.telefono" required />
            <x-input-error for="telefono" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-6">
            <x-label for="direccion" value="{{ __('Dirección (Opcional)') }}" />
            <x-input id="direccion" type="text" class="mt-1 block w-full" wire:model="state.direccion" />
            <x-input-error for="direccion" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-6">
            <x-label for="antecedentes_medicos" value="{{ __('Antecedentes Médicos (Opcional)') }}" />
            <textarea id="antecedentes_medicos" wire:model="state.antecedentes_medicos" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
            <x-input-error for="antecedentes_medicos" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-6">
            <x-label for="alergias" value="{{ __('Alergias (Opcional)') }}" />
            <textarea id="alergias" wire:model="state.alergias" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
            <x-input-error for="alergias" class="mt-2" />
        </div>
        @endrole
        {{-- SE ELIMINÓ EL </div> DE CIERRE DEL WRAPPER --}}
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>