<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Órdenes de Laboratorio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <h1 class="text-2xl font-bold text-gray-800 mb-4">Historial de Órdenes</h1>

                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Filtros y Búsqueda -->
                <div class="flex justify-between items-center mb-4 space-x-4">
                    <div class="flex-1">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por paciente o médico..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="w-1/4">
                        <label for="filtroEstado" class="block text-sm font-medium text-gray-700">Filtrar por Estado</label>
                        <select id="filtroEstado" wire:model.live="filtroEstado" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Todas</option> {{-- Por defecto--}}
                            <option value="Muestra Tomada">Muestra Tomada</option>
                            <option value="Resultados Listos">Resultados Listos</option>
                            <option value="Cancelada">Canceladas</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla de Órdenes -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orden #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($ordenes as $orden)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $orden->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $orden->paciente->nombre_completo ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $orden->created_at->format('d/m/Y H:i A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($orden->estado == 'Pendiente') bg-yellow-100 text-yellow-800 @endif
                                            @if($orden->estado == 'Muestra Tomada') bg-blue-100 text-blue-800 @endif
                                            @if($orden->estado == 'Resultados Listos') bg-green-100 text-green-800 @endif
                                            @if($orden->estado == 'Cancelada') bg-red-100 text-red-800 @endif
                                        ">
                                            {{ $orden->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        {{-- El único botón es Descargar --}}
                                        @if($orden->estado == 'Resultados Listos' && $orden->resultado_pdf_path)
                                            {{-- ¡BOTÓN CONECTADO! --}}
                                            <button wire:click="descargarResultado({{ $orden->id }})" 
                                                    wire:loading.attr="disabled"
                                                    class="bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                                <span wire:loading.remove wire:target="descargarResultado({{ $orden->id }})">Descargar</span>
                                                <span wire:loading wire:target="descargarResultado({{ $orden->id }})">...</span>
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Sin PDF</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron órdenes con los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $ordenes->links() }}
                </div>

            </div>
        </div>
    </div>
</div>