<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reservar Nueva Cita
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session()->has('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">¡Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    
                    <h1 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="bg-blue-100 text-blue-800 text-sm font-semibold mr-3 px-2.5 py-0.5 rounded-full">Paso 1</span>
                        Encuentra a tu especialista
                    </h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div>
                            <label for="especialidad" class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                            <select wire:model.live="especialidad_id" id="especialidad" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione especialidad...</option>
                                @foreach ($especialidades as $especialidad)
                                    <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="medico" class="block text-sm font-medium text-gray-700 mb-1">Médico</label>
                            <select wire:model.live="medico_id" id="medico" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed" @if(empty($especialidad_id)) disabled @endif>
                                <option value="">Seleccione médico...</option>
                                @foreach ($medicos as $medico)
                                    <option value="{{ $medico->id }}">{{ $medico->user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha deseada</label>
                            <input type="date" id="fecha" wire:model.live="fecha" 
                                   min="{{ date('Y-m-d') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                   @if(empty($medico_id)) disabled @endif>
                        </div>
                    </div>

                    <div wire:loading wire:target="updatedMedicoId" class="w-full mb-4">
                        <div class="flex items-center text-sm text-gray-500 italic">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Buscando horario del médico...
                        </div>
                    </div>
                    
                    @if ($selectedMedico)
                    <div wire:loading.remove wire:target="updatedMedicoId" class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg animate-pulse-once">
                        <h4 class="font-semibold text-gray-800">Horario de atención del Dr. {{ $selectedMedico->user->name }}:</h4>
                        
                        @if ($selectedMedico->horarios && $selectedMedico->horarios->count() > 0)
                            <ul class="list-disc list-inside mt-2 text-gray-700 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-4">
                                
                                {{-- 
                                -----------------------------------------------------------------
                                --- ¡CAMBIO AQUÍ! ---
                                -----------------------------------------------------------------
                                --}}
                                @foreach ($selectedMedico->horarios as $horario)
                                    <li>
                                        {{-- Usamos un @switch para "traducir" el número --}}
                                        @switch($horario->dia_semana)
                                            @case(1) <strong>Lunes:</strong> @break
                                            @case(2) <strong>Martes:</strong> @break
                                            @case(3) <strong>Miércoles:</strong> @break
                                            @case(4) <strong>Jueves:</strong> @break
                                            @case(5) <strong>Viernes:</strong> @break
                                            @case(6) <strong>Sábado:</strong> @break
                                            @case(7) <strong>Domingo:</strong> @break
                                            @default <strong>Día ({{ $horario->dia_semana }}):</strong>
                                        @endswitch

                                        de {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }} 
                                        a {{ \Carbon\Carbon::parse($horario->hora_fin)->format('h:i A') }}
                                    </li>
                                @endforeach
                                {{-- --- FIN DEL CAMBIO --- --}}

                            </ul>
                        @else
                            <p class="text-red-500 mt-2">El médico seleccionado no tiene un horario de atención configurado.</p>
                        @endif
                    </div>
                    @endif
                    <div wire:loading wire:target="fecha, updatedMedicoId" class="w-full text-center py-4 text-gray-500">
                        Buscando horarios disponibles...
                    </div>

                    @if (!empty($fecha) && !empty($medico_id))
                        <div class="mt-10 border-t pt-8 animate-pulse-once">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <span class="bg-blue-100 text-blue-800 text-sm font-semibold mr-3 px-2.5 py-0.5 rounded-full">Paso 2</span>
                                Horarios disponibles para el <span class="text-blue-600 ml-2 capitalize">{{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM') }}</span>
                            </h2>

                            @if (count($slots_disponibles) > 0)
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 mt-6">
                                    @foreach ($slots_disponibles as $slot)
                                        <button 
                                            wire:click="seleccionarHora('{{ $slot }}')"
                                            class="py-3 px-2 text-sm font-semibold border rounded-lg transition-all duration-200 text-center
                                                {{ $hora_seleccionada === $slot 
                                                    ? 'bg-blue-600 text-white border-blue-600 shadow-md ring-2 ring-blue-300' 
                                                    : 'bg-white text-gray-700 border-gray-200 hover:border-blue-400 hover:text-blue-600 hover:shadow-sm' 
                                                }}">
                                            {{ $slot }}
                                        </button>
                                    @endforeach
                                </div>

                                @if ($hora_seleccionada)
                                    <div class="mt-10 p-6 bg-green-50 border border-green-200 rounded-xl flex flex-col sm:flex-row justify-between items-center">
                                        <div class="mb-4 sm:mb-0">
                                            <h3 class="text-lg font-bold text-green-900">Resumen de tu Cita</h3>
                                            <p class="text-green-800 mt-1">
                                                Médico: <strong>{{ $medicos->find($medico_id)->user->name }}</strong><br>
                                                Fecha: <strong>{{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM') }}</strong> a las <strong>{{ $hora_seleccionada }}</strong>
                                            </p>
                                        </div>
                                        
                                        <div class="flex flex-col items-end">
                                            @error('hora_seleccionada') <span class="text-red-600 text-sm mb-2">{{ $message }}</span> @enderror
                                            @error('medico_id') <span class="text-red-600 text-sm mb-2">{{ $message }}</span> @enderror

                                            <button wire:click="confirmarCita" 
                                                    wire:loading.attr="disabled"
                                                    class="inline-flex items-center px-8 py-4 bg-green-600 border border-transparent rounded-full font-bold text-base text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                                                <span wire:loading.remove wire:target="confirmarCita">¡Confirmar Reserva!</span>
                                                <span wire:loading wire:target="confirmarCita">Procesando...</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                            @else
                                <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mt-6">
                                    <div class="flex">
                                        <div class="ml-3">
                                            <p class="text-sm text-orange-700">
                                                El médico no tiene horarios disponibles para esta fecha. Por favor, intenta seleccionando otro día.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>