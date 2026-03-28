<div>
    {{-- Cabecera blanca --}}
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <h1 class="text-2xl font-medium text-gray-900">
            Dashboard de Recepción
        </h1>
        <p class="mt-2 text-gray-600">Bienvenido/a, {{ auth()->user()->name }}. Aquí tienes un resumen del día.</p>
    </div>

    {{-- Contenido gris --}}
    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 mb-6">
            
            <div class="flex items-center p-4 bg-white rounded-lg shadow-lg">
                <div class="p-3 mr-4 text-yellow-500 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Citas Totales (Hoy)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $citasTotalesHoy }}</p>
                </div>
            </div>
            
            <div class="flex items-center p-4 bg-white rounded-lg shadow-lg">
                <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pacientes en Sala (Check-in)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pacientesEnEsperaCount }}</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow-lg">
                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Citas Completadas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $citasCompletadasHoy }}</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow-lg">
                <div class="p-3 mr-4 text-indigo-500 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pacientes Nuevos (Hoy)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pacientesNuevosHoy }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">

            {{-- Panel Izquierdo: Próximas Citas (MODIFICADO) --}}
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Citas Programadas (Pendientes de Check-in)</h2>
                <div class="overflow-x-auto max-h-96">
                    @if($citasHoy->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($citasHoy as $cita)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('H:i A') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">{{ $cita->paciente?->nombre_completo ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">{{ $cita->medico?->user?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">
                                            <button wire:click="hacerCheckIn({{ $cita->id }})" 
                                                    wire:loading.attr="disabled"
                                                    class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full hover:bg-blue-600 transition duration-150 ease-in-out">
                                                Check-in
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-gray-500 py-4">No hay más citas programadas por hoy.</p>
                    @endif
                </div>
            </div>

            {{-- Panel Derecho: Pacientes en Sala (CORREGIDO) --}}
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Pacientes en Sala de Espera</h2>
                <div class="overflow-x-auto max-h-96">
                    @if($pacientesEnEspera->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Llegada (Hora Cita)</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pacientesEnEspera as $cita)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('H:i A') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">{{ $cita->paciente?->nombre_completo ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">{{ $cita->medico?->user?->name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-gray-500 py-4">No hay pacientes en sala de espera.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>