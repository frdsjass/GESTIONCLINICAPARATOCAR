<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-medium text-gray-900">
                Catálogo de Farmacia y Gestión de Lotes
            </h1>
            <button wire:click="createMedicamento()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Agregar Nuevo Producto al Catálogo
            </button>
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o principio activo..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Comercial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Principio Activo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lotes</th>
                        <th class="relative px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($medicamentos as $medicamento)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $medicamento->nombre_comercial }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $medicamento->principio_activo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg font-semibold">{{ $medicamento->stock_total }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @forelse($medicamento->lotes as $lote)
                                    <div class="flex items-center justify-between hover:bg-gray-100 p-1 rounded">
                                        <span class="@if(\Carbon\Carbon::parse($lote->fecha_vencimiento)->isPast()) text-red-600 font-bold @endif">
                                            @if(\Carbon\Carbon::parse($lote->fecha_vencimiento)->isPast()) ⚠️ @endif
                                            Lote {{ $lote->numero_lote }}: {{ $lote->cantidad }} uds. (Vence: {{ \Carbon\Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y') }})
                                        </span>
                                        <button wire:click="deleteLote({{ $lote->id }})" wire:confirm="¿Estás seguro de que quieres eliminar este lote?" class="ml-2 text-red-500 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                @empty
                                    <span class="text-gray-400">Sin stock</span>
                                @endforelse
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="createLote({{ $medicamento->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-xs">Agregar Lote/Stock</button>
                                <button wire:click="editMedicamento({{ $medicamento->id }})" class="text-indigo-600 hover:text-indigo-900 ml-4">Editar Producto</button>
                                <button wire:click="deleteMedicamento({{ $medicamento->id }})" wire:confirm="¿Estás seguro de que quieres eliminar este producto y TODO su stock asociado?" class="text-red-600 hover:text-red-900 ml-4">Eliminar Producto</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay productos en el catálogo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $medicamentos->links() }}
        </div>
    </div>

    @if ($isMedicamentoModalOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form>
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $medicamento_id ? 'Editar Producto del Catálogo' : 'Agregar Nuevo Producto al Catálogo' }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre Comercial</label>
                                    <input type="text" wire:model="nombre_comercial" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('nombre_comercial') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Principio Activo</label>
                                    <input type="text" wire:model="principio_activo" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('principio_activo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                 <div>
                                    <label class="block text-sm font-medium text-gray-700">Laboratorio</label>
                                    <input type="text" wire:model="laboratorio" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Presentación</label>
                                    <input type="text" wire:model="presentacion" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Caja 20 comp.">
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="requiere_receta" id="requiere_receta" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <label for="requiere_receta" class="ml-2 block text-sm text-gray-900">Requiere Receta Médica</label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button wire:click.prevent="storeMedicamento()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600">Guardar Producto</button>
                            <button wire:click="closeMedicamentoModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white sm:mt-0 sm:ml-3">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($isLoteModalOpen)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form>
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Agregar Lote de Inventario</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Número de Lote</label>
                                    <input type="text" wire:model="numero_lote" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('numero_lote') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                                    <input type="date" wire:model="fecha_vencimiento" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('fecha_vencimiento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cantidad (Stock)</label>
                                    <input type="number" wire:model="cantidad" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('cantidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Precio de Venta (Bs.)</label>
                                    <input type="number" step="0.01" wire:model="precio_venta" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('precio_venta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Stock Mínimo (Alerta)</label>
                                    <input type="number" wire:model="stock_minimo" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('stock_minimo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button wire:click.prevent="storeLote()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600">Agregar al Inventario</button>
                            <button wire:click="closeLoteModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white sm:mt-0 sm:ml-3">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>