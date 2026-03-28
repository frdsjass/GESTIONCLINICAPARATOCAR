<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mis Citas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Filtros -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Buscar por nombre de paciente..." 
                        class="form-input rounded-md shadow-sm mt-1 block w-full"
                    />
                    
                    <select wire:model.live="filtro_fecha" class="form-select rounded-md shadow-sm mt-1 block w-full">
                        <option value="proximas">Citas Próximas</option>
                        <option value="pasadas">Citas Pasadas</option>
                        <option value="todas">Todas las Citas</option>
                    </select>

                    <select wire:model.live="filtro_estado" class="form-select rounded-md shadow-sm mt-1 block w-full">
                        <option value="Programada">Programadas</option>
                        <option value="Completada">Completadas</option>
                        <option value="Cancelada">Canceladas</option>
                        <option value="todas">Todos los Estados</option>
                    </select>
                </div>

                <!-- Tabla de Citas -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha y Hora
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Paciente
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($citas as $cita)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $cita->fecha_hora_inicio->translatedFormat('l, d F Y - H:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $cita->paciente->nombre_completo ?? 'Paciente no encontrado' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($cita->estado == 'Programada')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Programada
                                            </span>
                                        @elseif($cita->estado == 'Completada')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Completada
                                            </span>
                                        @elseif($cita->estado == 'Cancelada')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Cancelada
                                            </span>
                                        @else
                                            {{ $cita->estado }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($cita->estado == 'Programada' && $cita->fecha_hora_inicio->isToday())
                                            <a href="{{ route('doctor.appointments.attend', $cita->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Atender</a>
                                        @endif
                                        <a href="{{ route('doctor.pacientes.historial', $cita->paciente_id) }}" class="text-gray-600 hover:text-gray-900">Historial</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No se encontraron citas que coincidan con los filtros.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-6">
                    {{ $citas->links() }}
                </div>

            </div>
        </div>
    </div>
</div>