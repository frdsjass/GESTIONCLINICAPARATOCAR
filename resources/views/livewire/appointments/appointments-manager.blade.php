<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-medium text-gray-900">
                Gestión de Citas
            </h1>
            <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Agendar Nueva Cita
            </button>
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        {{-- Este mensaje de ÉXITO (verde) es para la tabla principal --}}
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        
        <div class="mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre del paciente o médico..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha y Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="relative px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($citas as $cita)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $cita->paciente?->nombre_completo ?? 'Paciente no asignado' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $cita->medico?->user?->name ?? 'Médico no asignado' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('d/m/Y H:i A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($cita->estado == 'Programada') bg-yellow-100 text-yellow-800 @endif
                                    @if($cita->estado == 'Confirmada') bg-blue-100 text-blue-800 @endif
                                    @if($cita->estado == 'Completada') bg-green-100 text-green-800 @endif
                                    @if($cita->estado == 'Cancelada' || $cita->estado == 'No Asistió') bg-red-100 text-red-800 @endif
                                ">
                                    {{ $cita->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $cita->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron citas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $citas->links() }}
        </div>
    </div>

    {{-- =================================== --}}
    {{-- == MODAL DE CREAR/EDITAR CITA (FASE G)== --}}
    {{-- =================================== --}}
    @if ($isOpen)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $cita_id ? 'Editar Cita' : 'Agendar Nueva Cita' }}</h3>

                        {{-- Mensaje de Error (Límite de Citas) --}}
                        @if (session()->has('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-3" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="paciente_id" class="block text-sm font-medium text-gray-700">Paciente</label>
                                <select wire:model="paciente_id" id="paciente_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Seleccione un paciente</option>
                                    @foreach ($pacientes as $paciente)
                                        <option value="{{ $paciente->id }}">{{ $paciente->nombre_completo }}</option>
                                    @endforeach
                                </select>
                                @error('paciente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="medico_id" class="block text-sm font-medium text-gray-700">Médico</label>
                                {{-- ¡MODIFICADO! wire:model.live para reaccionar al cambio --}}
                                <select wire:model.live="medico_id" id="medico_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Seleccione un médico</option>
                                    @foreach ($medicos as $medico)
                                        <option value="{{ $medico->id }}">
                                            {{ $medico->user?->name ?? 'Médico sin nombre' }} 
                                            @if($medico->especialidad)
                                                ({{ $medico->especialidad?->nombre }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('medico_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- --- ¡NUEVO FORMULARIO DE FECHA Y HORA (FASE G)! --- --}}
                            
                            {{-- 1. Selector de Fecha --}}
                            <div>
                                <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                                {{-- ¡MODIFICADO! wire:model.live para reaccionar --}}
                                <input type="date" wire:model.live="fecha" id="fecha" 
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       @if(!$medico_id) disabled @endif> {{-- Deshabilitado si no hay médico --}}
                                @error('fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- 2. Selector de Hora (Dinámico) --}}
                            <div>
                                <label for="hora" class="block text-sm font-medium text-gray-700">Hora</label>
                                
                                {{-- Mensaje de Carga --}}
                                <div wire:loading wire:target="fecha, medico_id" class="text-sm text-gray-500">
                                    Buscando horarios disponibles...
                                </div>
                                
                                {{-- El Select de Horas --}}
                                <select wire:model="hora" id="hora" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                        wire:loading.remove wire:target="fecha, medico_id" {{-- Ocultar mientras carga --}}
                                        @if(!$medico_id || !$fecha || empty($intervalos)) disabled @endif> {{-- Deshabilitado si no hay data --}}
                                    
                                    <option value="">
                                        @if(!$medico_id) Seleccione un médico primero
                                        @elseif(!$fecha) Seleccione una fecha
                                        @elseif(empty($intervalos) && $fecha) No hay horas disponibles este día
                                        @else Seleccione una hora
                                        @endif
                                    </option>

                                    @foreach ($intervalos as $intervalo)
                                        <option value="{{ $intervalo }}">{{ $intervalo }}</option>
                                    @endforeach
                                </select>
                                @error('hora') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- --- FIN NUEVO FORMULARIO --- --}}


                            <div>
                                <label for="motivo_consulta" class="block text-sm font-medium text-gray-700">Motivo de la Consulta</label>
                                <input type="text" wire:model="motivo_consulta" id="motivo_consulta" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('motivo_consulta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                             <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                <select wire:model="estado" id="estado" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Seleccione un estado</option>
                                    <option value="Programada">Programada</option>
                                    <option value="Confirmada">Confirmada</option>
                                    <option value="Completada">Completada</option>
                                    <option value="Cancelada">Cancelada</option>
                                    <option value="No Asistió">No Asistió</option>
                                </select>
                                @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click.prevent="store()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700">Guardar</button>
                        <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>