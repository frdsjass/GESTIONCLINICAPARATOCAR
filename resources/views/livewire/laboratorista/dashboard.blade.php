<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Laboratorio') }}
        </h2>
    </x-slot>

    <!-- 
      Contenedor principal. 
      Se eliminó el bloque de estadísticas.
    -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- INICIO TARJETA "COLA DE TRABAJO" (Tu código original) -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            <h1 class="text-2xl font-bold text-gray-800 mb-4">Cola de Trabajo del Laboratorio</h1>

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ session('message') }}
                </div>
            @endif
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
                        <option value="Pendiente">Pendientes</option>
                        <option value="Muestra Tomada">Muestra Tomada</option>
                        <option value="Resultados Listos">Resultados Listos</option>
                        <option value="Cancelada">Canceladas</option>
                        <option value="">Todas</option>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exámenes Solicitados</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico Solicitante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($ordenes as $orden)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $orden->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $orden->paciente->nombre_completo ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <ul class="list-disc list-inside text-sm">
                                        @forelse($orden->detalles as $detalle)
                                            <li>{{ $detalle->tipoExamen->nombre ?? 'Examen no encontrado' }}</li>
                                        @empty
                                            <li class="text-gray-500">Sin detalles</li>
                                        @endforelse
                                    </ul>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $orden->medico->user->name ?? 'N/A' }}</td>
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
                                    <button wire:click="procesarOrden({{ $orden->id }})" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded">
                                        Procesar / Ver
                                    </button>
                                    
                                    @if($orden->estado == 'Resultados Listos' && $orden->resultado_pdf_path)
                                        <button wire:click="descargarResultado({{ $orden->id }})" class="bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded">
                                            Descargar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron órdenes con los filtros seleccionados.</td>
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

    {{-- =================================== --}}
    {{-- == MODAL PARA PROCESAR ORDEN      == --}}
    {{-- == (Sin cambios)                  == --}}
    {{-- =================================== --}}
    @if ($isOpen && $ordenSeleccionada)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-2xl sm:w-full my-8">
                <form wire:submit="guardarResultados">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Procesar Orden de Laboratorio #{{ $ordenSeleccionada->id }}
                        </h3>
                        <p class="text-sm text-gray-600">Paciente: {{ $ordenSeleccionada->paciente->nombre_completo ?? 'N/A' }}</p>
                        
                        <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">

                            <!-- Resultados por Examen -->
                            <fieldset class="border rounded-md p-4">
                                <legend class="text-md font-semibold text-gray-800 px-2">Resultados Individuales</legend>
                                <p class="text-sm text-gray-600 mb-4">Ingrese los resultados para cada examen solicitado.</p>
                                
                                {{-- ¡¡INICIO DE LA MODIFICACIÓN (Fase M)!! --}}
                                @foreach($ordenSeleccionada->detalles as $detalle)
                                <div class="border-b border-gray-200 pb-4 mb-4">
                                    <label for="detalle_resultados_{{ $detalle->id }}" class="block text-sm font-medium text-gray-900">{{ $detalle->tipoExamen->nombre ?? 'N/A' }}</label>
                                    
                                    <!-- Campo de Resultado -->
                                    <input type="text" 
                                           wire:model="detallesForm.{{ $detalle->id }}.resultados" 
                                           id="detalle_resultados_{{ $detalle->id }}" 
                                           class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                           placeholder="Ej: 110 mg/dL">
                                    @error('detallesForm.'.$detalle->id.'.resultados') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                                    <!-- NUEVOS CAMPOS (Fase M) -->
                                    <div class="grid grid-cols-2 gap-4 mt-2">
                                        <div>
                                            <label for="detalle_referencia_{{ $detalle->id }}" class="block text-xs font-medium text-gray-600">Valores de Referencia</label>
                                            <input type="text" 
                                                   wire:model="detallesForm.{{ $detalle->id }}.valores_referencia" 
                                                   id="detalle_referencia_{{ $detalle->id }}" 
                                                   class="mt-1 block w-full shadow-sm sm:text-xs border-gray-300 rounded-md"
                                                   placeholder="Ej: 70 - 120 mg/dL">
                                            @error('detallesForm.'.$detalle->id.'.valores_referencia') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="detalle_metodo_{{ $detalle->id }}" class="block text-xs font-medium text-gray-600">Metodología</label>
                                            <input type="text" 
                                                   wire:model="detallesForm.{{ $detalle->id }}.metodologia" 
                                                   id="detalle_metodo_{{ $detalle->id }}" 
                                                   class="mt-1 block w-full shadow-sm sm:text-xs border-gray-300 rounded-md"
                                                   placeholder="Ej: Método Enzimático">
                                            @error('detallesForm.'.$detalle->id.'.metodologia') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <!-- FIN NUEVOS CAMPOS -->
                                </div>
                                @endforeach
                                {{-- ¡¡FIN DE LA MODIFICACIÓN!! --}}

                            </fieldset>
                            
                            <!-- Resultados Generales -->
                            <fieldset class="border rounded-md p-4">
                                <legend class="text-md font-semibold text-gray-800 px-2">Resultados Generales</legend>
                                
                                <div>
                                    <label for="resultados_texto" class="block text-sm font-medium text-gray-700">Conclusión General / Resumen</label>
                                    <textarea wire:model="resultados_texto" id="resultados_texto" rows="4" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    @error('resultados_texto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="mt-4">
                                    <label for="estadoOrden" class="block text-sm font-medium text-gray-700">Actualizar Estado de la Orden</label>
                                    <select wire:model="estadoOrden" id="estadoOrden" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Muestra Tomada">Muestra Tomada</option>
                                        <option value="Resultados Listos">Resultados Listos</option>
                                        <option value="Cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm" wire:loading.attr="disabled">
                            Guardar Resultados y Generar PDF
                        </button>
                        <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>