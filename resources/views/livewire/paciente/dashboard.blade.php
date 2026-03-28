<div>
    {{-- 
     VERSIÓN 3.0 (¡Ahora sí!)
     1. Corregido el error 'EOD' que rompía el archivo.
     2. 'bg-content' (tu gray-100) aplicado al fondo de la página.
     3. 'primary-600' (tu teal) aplicado a los acentos.
    --}}
    
    <div class="bg-content min-h-screen py-8 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ALERTAS DE SISTEMA --}}
            @if (!Auth::user()->paciente->isProfileComplete())
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">¡Perfil Incompleto!</strong>
                    <span class="block sm:inline">Por favor, completa tu información de perfil para un mejor servicio (alergias, etc.).</span>
                    <a href="{{ route('profile.show') }}" class="ml-2 font-bold underline">Completar aquí</a>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">¡Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- ENCABEZADO --}}
            <div class="mb-6 px-4 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900"> 
                    ¡Hola, {{ $pacienteNombre }}!
                </h1>
                <p class="mt-1 text-lg text-gray-600">
                    Bienvenido a tu portal. Aquí puedes gestionar tus citas y tu historial.
                </p>
            </div>

            {{-- GRID PRINCIPAL --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">

                    {{-- TARJETA 1: Próximas Citas (Fondo blanco) --}}
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-primary-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                Próximas Citas
                            </h2>
                        </div>
                        
                        <div class="flow-root">
                            <ul role="list" class="divide-y divide-gray-200">
                                @forelse ($citasFuturas as $cita)
                                    <li class="flex flex-col sm:flex-row sm:items-center justify-between p-6 hover:bg-gray-50 transition-colors">
                                        <div class="flex-1">
                                            <p class="text-lg font-bold text-primary-600 capitalize">
                                                {{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                                            </p>
                                            <p class="text-md font-medium text-gray-900">
                                                a las {{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('h:i A') }}
                                            </p>
                                            <div class="mt-1 text-sm text-gray-600">
                                                <span class="font-medium">Dr/a. {{ $cita->medico->user->name ?? 'No asignado' }}</span>
                                                <span class="text-gray-400 mx-1">•</span>
                                                <span>{{ $cita->medico->especialidad->nombre ?? 'General' }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 sm:mt-0 flex flex-col items-end gap-2">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($cita->estado == 'Programada') bg-blue-100 text-blue-800 @endif
                                                @if($cita->estado == 'Confirmada') bg-green-100 text-green-800 @endif
                                                @if($cita->estado == 'Cancelada') bg-red-100 text-red-800 @endif">
                                                {{ $cita->estado }}
                                            </span>
                                            @if(in_array($cita->estado, ['Programada', 'Confirmada']))
                                                @php
                                                    $limite = \Carbon\Carbon::parse($cita->fecha_hora_inicio)->subHours(24);
                                                    $puedeCancelar = now()->lessThan($limite);
                                                @endphp
                                                @if($puedeCancelar)
                                                    <span class="text-xs text-gray-500 flex items-center text-right" title="Fecha límite: {{ $limite->locale('es')->isoFormat('ddd D, h:mm A') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 mr-1 text-orange-400">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" />
                                                        </svg>
                                                        Quedan {{ $limite->locale('es')->diffForHumans(now(), ['parts' => 2, 'join' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                                    </span>
                                                    <button 
                                                        wire:click="solicitarCancelacion({{ $cita->id }})"
                                                        wire:confirm="¿Estás seguro de que deseas cancelar esta cita?"
                                                        wire:loading.attr="disabled"
                                                        class="text-sm text-red-600 hover:text-red-800 font-medium underline focus:outline-none transition ease-in-out duration-150">
                                                        Cancelar Cita
                                                    </button>
                                                @else
                                                    <span class="text-xs text-orange-500 italic font-medium">
                                                        Plazo de cancelación finalizado
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-10 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                        <h4 class="mt-4 text-lg font-medium text-gray-800">No tienes próximas citas</h4>
                                        <p class="mt-1 text-sm text-gray-500">Cuando agendes una cita, aparecerá aquí.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    {{-- TARJETA 2: Historial (Fondo blanco) --}}
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>
                                Historial de Citas y Resultados
                            </h2>
                        </div>
                        <div class="flow-root">
                            <ul role="list" class="divide-y divide-gray-200">
                                @forelse ($citasPasadas as $cita)
                                    <li class="flex flex-col sm:flex-row sm:items-center justify-between p-6 opacity-80 hover:opacity-100 transition-opacity">
                                        <div class="flex-1">
                                            <p class="text-md font-medium text-gray-700 capitalize">
                                                {{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}
                                                - {{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('h:i A') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Dr/a. {{ $cita->medico->user->name ?? 'No asignado' }}</span>
                                                ({{ $cita->medico->especialidad->nombre ?? 'General' }})
                                            </p>

                                            {{-- =================================== --}}
                                            {{-- == INICIO: FASE O (Ver PDF)      == --}}
                                            {{-- =================================== --}}
                                            @if($cita->ordenLaboratorio && $cita->ordenLaboratorio->resultado_pdf_path)
                                            <div class="mt-3">
                                                <button wire:click="descargarResultado({{ $cita->ordenLaboratorio->id }})" 
                                                        wire:loading.attr="disabled"
                                                        wire:target="descargarResultado({{ $cita->ordenLaboratorio->id }})"
                                                        class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                                    <!-- Icono de PDF -->
                                                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625a1.875 1.875 0 00-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V11.25a1.875 1.875 0 00-1.875-1.875H9.75M12 9.75v-4.5" />
                                                    </svg>
                                                    Descargar Resultados (PDF)
                                                </button>
                                            </div>
                                            @endif
                                            {{-- =================================== --}}
                                            {{-- == FIN: FASE O (Ver PDF)         == --}}
                                            {{-- =================================== --}}

                                        </div>
                                        <div class="mt-4 sm:mt-0">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($cita->estado == 'Completada') bg-gray-200 text-gray-800 @endif
                                                @if($cita->estado == 'Cancelada') bg-red-100 text-red-800 @endif
                                                @if($cita->estado == 'No Asistió') bg-orange-100 text-orange-800 @endif">
                                                {{ $cita->estado }}
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-8 text-center">
                                        <p class="text-center text-gray-500 italic">No tienes citas antiguas en tu historial.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">

                    {{-- TARJETA 3: Horarios --}}
                    <div class="bg-white p-6 shadow-lg sm:rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Horarios de Atención
                        </h3>
                        <ul class="mt-4 space-y-2 text-sm text-gray-600">
                            <li class="flex justify-between">
                                <span>Lunes a Viernes</span>
                                <span class="font-medium text-gray-800">8:00 AM - 8:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Sábados</span>
                                <span class="font-medium text-gray-800">9:00 AM - 1:00 PM</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Domingos</span>
                                <span class="font-medium text-red-600">Cerrado</span>
                            </li>
                        </ul>
                        <p class="mt-4 text-xs text-gray-500">
                            Puedes agendar tu cita en línea 24/7 desde el menú de la izquierda.
                        </p>
                    </div>

                    {{-- TARJETA 4: Perfil --}}
                    <div class="bg-white p-6 shadow-lg sm:rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900">Mi Perfil</h3>
                        @if (!Auth::user()->paciente->isProfileComplete())
                            <p class="mt-2 text-sm text-yellow-700">
                                <strong class="font-bold">¡Atención!</strong> Tu perfil está incompleto.
                            </p>
                            <p class="mt-1 text-sm text-gray-600">Completa tus datos (alergias, etc.) para un mejor servicio.</p>
                            <a href="{{ route('profile.show') }}" class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 border border-yellow-500 rounded-md text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                                Completar Perfil
                            </a>
                        @else
                            <p class="mt-2 text-sm text-green-700">
                                <strong class="font-bold">¡Gracias!</strong> Tu perfil está completo.
                            </p>
                            <p class="mt-1 text-sm text-gray-600">Puedes revisar o actualizar tu información en cualquier momento.</p>
                            <a href="{{ route('profile.show') }}" class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Ver mi Perfil
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>