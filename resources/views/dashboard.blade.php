<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- INICIO DE LA MODIFICACIÓN --}}
            @role('Laboratorista')
                
                <!-- ============================================= -->
                <!-- ¡SECCIÓN DE ESTADÍSTICAS MOVIDA AQUÍ! -->
                <!-- ============================================= -->
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
                                        {{-- Esto dará error hasta la Etapa 2 --}}
                                        {{ $stats['pendientes'] }}
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
                                        {{-- Esto dará error hasta la Etapa 2 --}}
                                        {{ $stats['completadasHoy'] }}
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
                                        {{-- Esto dará error hasta la Etapa 2 --}}
                                        {{ $stats['totalHoy'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================= -->
                <!-- FIN DE LA SECCIÓN DE ESTADÍSTICAS -->
                <!-- ============================================= -->

            @else
                
                {{-- Esto es lo que ven los otros roles --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <x-welcome />
                </div>

            @endrole
            {{-- FIN DE LA MODIFICACIÓN --}}

        </div>
    </div>
</x-app-layout>