<div>
    {{-- Encabezado de la Página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Título neutral, ya que es para todos los roles --}}
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ============================================= --}}
            {{-- == VISTA DEL DASHBOARD PARA ADMIN/RECEPCIÓN == --}}
            {{-- ============================================= --}}
            @role('Admin|Recepcion')
                <!-- Fila de Tarjetas de Resumen (KPIs) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    
                    <!-- Card: Pacientes Activos -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">Pacientes Activos</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $totalPacientes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Médicos Activos -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.08-1.282-.23-1.857m-7.77-1.857a3 3 0 00-5.356 1.857v2h5m-5 0H3v-2a3 3 0 015.356-1.857M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a3 3 0 11-6 0 3 3 0 016 0zM9 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">Médicos Activos</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $totalMedicos }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Citas del Día -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">Citas (Hoy)</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $citasHoy }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Ingresos del Día -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0c-1.657 0-3-.895-3-2s1.343-2 3-2 3-.895 3-2-1.343-2-3-2m0 8c1.11 0 2.08-.402 2.599-1M12 16v1m0-1v-8m0 0c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08-.402 2.599-1M12 8V7m0 1v8m0 0c-1.657 0-3-.895-3-2s1.343-2 3-2 3-.895 3-2-1.343-2-3-2m0 8c1.11 0 2.08-.402 2.599-1M12 16v1m0-1v-8" /></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">Ingresos Farmacia (Hoy)</p>
                                <p class="text-3xl font-bold text-gray-900">Bs. {{ number_format($ingresosHoy, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin Fila de Tarjetas -->

                <!-- Fila de Tablas de Acción Rápida -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Tabla: Próximas Citas -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Próximas Citas del Día</h3>
                        </div>
                        <div class="p-0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($citasPendientes as $cita)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('h:i A') }}</div>
                                                <div class="text-sm text-gray-500">{{ $cita->paciente?->nombre_completo }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Dr. {{ $cita->medico?->user?->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($cita->estado == 'Programada') bg-yellow-100 text-yellow-800 @else bg-blue-100 text-blue-800 @endif">
                                                    {{ $cita->estado }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay más citas pendientes por hoy.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabla: Alertas de Stock Bajo -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 border-b">
                            <h3 class="text-lg font-medium text-gray-900">Alertas de Stock Bajo</h3>
                        </div>
                        <div class="p-0">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($stockBajo as $lote)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $lote->medicamento?->nombre_comercial }}</div>
                                                <div class="text-sm text-gray-500">Lote: {{ $lote->numero_lote }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Quedan {{ $lote->cantidad }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-4 text-center text-gray-500">¡Todo bien! No hay alertas de stock bajo.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- Fin Fila de Tablas -->
            @endrole

            {{-- ============================================= --}}
            {{-- == VISTA DEL DASHBOARD PARA LABORATORISTA   == --}}
            {{-- ============================================= --}}
            @role('Laboratorista')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        
                    <!-- Stat 1: Pendientes -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                    <!-- Icono (Heroicon: clock) -->
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 truncate">Órdenes Pendientes</p>
                                    <p class="text-3xl font-semibold text-gray-900">
                                        {{-- Esta variable $stats ahora viene de DashboardHome.php --}}
                                        {{ $stats['pendientes'] ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 2: Completadas Hoy -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <!-- Icono (Heroicon: check-circle) -->
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 truncate">Completadas Hoy</p>
                                    <p class="text-3xl font-semibold text-gray-900">
                                        {{ $stats['completadasHoy'] ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3: Total Órdenes Hoy -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <!-- Icono (Heroicon: document-text) -->
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 truncate">Total Órdenes Hoy</p>
                                    <p class="text-3xl font-semibold text-gray-900">
                                        {{ $stats['totalHoy'] ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endrole

            {{-- ============================================= --}}
            {{-- == VISTA PARA OTROS ROLES (Medico, Paciente) == --}}
            {{-- ============================================= --}}
            @role('Medico|Paciente')
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <x-welcome />
                </div>
            @endrole

        </div>
    </div>
</div>