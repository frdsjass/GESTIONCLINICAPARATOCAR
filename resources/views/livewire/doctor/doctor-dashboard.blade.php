<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-medium text-gray-900">
                Mis Citas para Hoy ({{ \Carbon\Carbon::now()->format('d/m/Y') }})
            </h1>
            {{-- ¡NUEVO! Botón para refrescar la lista --}}
            <button wire:click="cargarCitas" class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-900">
                <svg wire:loading wire:target="cargarCitas" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg wire:loading.remove wire:target="cargarCitas" class="h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2.086A11.952 11.952 0 0012 4.044m0-4.044v5h-.582m15.356 2.086a11.952 11.952 0 01-14.133 14.133m14.133-14.133L4.044 19.956" />
                </svg>
                Refrescar
            </button>
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">

        <!-- ======================= -->
        <!-- LISTA 1: CITAS PENDIENTES -->
        <!-- ======================= -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Citas Pendientes ({{ $citasPendientes->count() }})
        </h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow mb-10">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="relative px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($citasPendientes as $cita)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">{{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('H:i A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $cita->paciente->nombre_completo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $cita->motivo_consulta }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($cita->estado == 'Programada') bg-yellow-100 text-yellow-800 @endif
                                    @if($cita->estado == 'Confirmada') bg-blue-100 text-blue-800 @endif
                                ">
                                    {{ $cita->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                {{-- ¡BOTÓN MODIFICADO! --}}
                                <a href="{{ route('doctor.record.manage', $cita) }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700">
                                    Atender
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No tiene citas pendientes por hoy.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ====================== -->
        <!-- LISTA 2: CITAS FINALIZADAS -->
        <!-- ====================== -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Citas Finalizadas ({{ $citasCompletadas->count() }})
        </h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="relative px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($citasCompletadas as $cita)
                        <tr class="hover:bg-gray-50 opacity-80">
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-700">{{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('H:i A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $cita->paciente->nombre_completo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $cita->motivo_consulta }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($cita->estado == 'Completada') bg-green-100 text-green-800 @endif
                                    @if($cita->estado == 'Cancelada' || $cita->estado == 'No Asistió') bg-red-100 text-red-800 @endif
                                ">
                                    {{ $cita->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                {{-- ¡BOTÓN MODIFICADO! --}}
                                <a href="{{ route('doctor.record.manage', $cita) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Ver Ficha
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No ha finalizado ninguna cita hoy.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>