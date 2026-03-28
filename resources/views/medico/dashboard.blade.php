<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inicio (Dashboard Médico)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- ====== Tarjetas de Estadísticas (ACTUALIZADO A 4 COLUMNAS) ====== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                
                <!-- Pacientes en Sala de Espera (NUEVO) -->
                <div class="bg-white p-6 rounded-lg shadow-lg border border-yellow-300">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-500 rounded-full text-white">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm font-medium">Pacientes en Espera</h3>
                            <p class="text-2xl font-semibold text-gray-900">{{ $pacientesEnEsperaCount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Citas de Hoy -->
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-500 rounded-full text-white">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm font-medium">Citas Programadas (Hoy)</h3>
                            <p class="text-2xl font-semibold text-gray-900">{{ $citasHoyCount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Citas de la Semana -->
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-500 rounded-full text-white">
                             <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm font-medium">Citas (Próx. 7 días)</h3>
                            <p class="text-2xl font-semibold text-gray-900">{{ $citasSemanaCount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pacientes Atendidos Hoy -->
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-500 rounded-full text-white">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm font-medium">Pacientes Atendidos (Hoy)</h3>
                            <p class="text-2xl font-semibold text-gray-900">{{ $pacientesAtendidosHoyCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ====== Paneles (NUEVO) ====== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Panel Izquierdo: Citas del Día (El que ya tenías) -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Agenda del Día - {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($citasDelDia as $cita)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $cita->fecha_hora_inicio->format('H:i A') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $cita->paciente->nombre_completo ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            {{-- ============ INICIO DE LA CORRECCIÓN DE ESTADO ============ --}}
                                            @if($cita->estado == 'Programada')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Programada
                                                </span>
                                            @elseif($cita->estado == 'Confirmada')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 animate-pulse">
                                                    En Espera (Check-in)
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Completada
                                                </span>
                                            @endif
                                            {{-- ============ FIN DE LA CORRECCIÓN DE ESTADO ============ --}}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- ============ INICIO DE LA CORRECCIÓN DE ACCIÓN ============ --}}
                                            @if(in_array($cita->estado, ['Programada', 'Confirmada']))
                                                <a href="{{ route('doctor.appointments.attend', $cita->id) }}" 
                                                   class="mr-3 {{ $cita->estado == 'Confirmada' ? 'text-green-600 hover:text-green-900 font-bold' : 'text-indigo-600 hover:text-indigo-900' }}">
                                                    {{ $cita->estado == 'Confirmada' ? 'Llamar a Consulta' : 'Atender' }}
                                                </a>
                                            @endif
                                            {{-- ============ FIN DE LA CORRECCIÓN DE ACCIÓN ============ --}}
                                            
                                            <a href="{{ route('doctor.pacientes.historial', $cita->paciente_id) }}" class="text-gray-600 hover:text-gray-900">Historial</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">
                                            No hay citas programadas para hoy.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Panel Derecho: Pacientes Recientes (NUEVO) -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Pacientes Atendidos Recientemente
                    </h3>
                    <div class="space-y-4">
                        @forelse($pacientesRecientes as $historia)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $historia->paciente->nombre_completo ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">
                                        Última visita: {{ $historia->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <a href="{{ route('doctor.pacientes.historial', $historia->paciente_id) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    Historial
                                </a>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-500">
                                Aún no hay pacientes en el historial reciente.
                            </p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>