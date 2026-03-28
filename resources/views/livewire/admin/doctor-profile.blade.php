<div>
    {{-- Esto pone el título en la cabecera de tu layout 'app.blade.php' --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil del Médico: {{ $medico->user?->name ?? 'N/A' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Contenido del Perfil --}}
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900">Información Personal</h3>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre Completo</p>
                            <p class="mt-1 text-lg text-gray-900">{{ $medico->user?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="mt-1 text-lg text-gray-900">{{ $medico->user?->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Especialidad</p>
                            <p class="mt-1 text-lg text-gray-900">{{ $medico->especialidad?->nombre ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">C.I. Profesional</p>
                            <p class="mt-1 text-lg text-gray-900">{{ $medico->cedula_profesional }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Límite de Citas/Día</p>
                            <p class="mt-1 text-lg text-gray-900">{{ $medico->limite_citas_dia }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Estado</p>
                            @if($medico->estado == 'Activo')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Teléfono</p>
                            <p class="mt-1 text-lg text-gray-900">{{ $medico->telefono ?? 'No especificado' }}</p>
                        </div>
                        
                        <div class="md:col-span-2"> <p class="text-sm font-medium text-gray-500">Dirección</p>
                            <p class="mt-1 text-lg text-gray-900">{{ $medico->direccion ?? 'No especificada' }}</p>
                        </div>
                        </div>

                    <div class="mt-8 border-t pt-6">
                        <a href="{{ route('admin.doctors') }}" wire:navigate 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            &larr; Volver a la lista
                        </a>
                    </div>

                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            Administrar Horario de Atención
                        </h3>
                        
                        @livewire('admin.gestionar-horario-medico', ['medico' => $medico])
                    
                    </div>
                    </div>

            </div>
        </div>
    </div>
</div>