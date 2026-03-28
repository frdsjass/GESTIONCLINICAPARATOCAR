<div>
    <!-- Encabezado del POS -->
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <h1 class="text-2xl font-medium text-gray-900">
            Punto de Venta (POS)
        </h1>
    </div>

    <!-- Contenido Principal (Panel Izquierdo: Búsqueda, Panel Derecho: Carrito) -->
    <div class="bg-gray-50 p-6 lg:p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- ======================================== -->
        <!--      PANEL IZQUIERDO (BÚSQUEDA)          -->
        <!-- ======================================== -->
        <div class="md:col-span-2">
            <div class="bg-white p-4 rounded-lg shadow min-h-[75vh]">
                
                <!-- Input de Búsqueda -->
                <input type
                    ="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Buscar por nombre comercial o principio activo..." 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                <!-- ***** INICIO DE LA LÓGICA CONDICIONAL ***** -->
                <!-- Revisa si el usuario está buscando algo (más de 2 letras) -->
                @if (strlen($search) > 2)
                    
                    <!-- A. MUESTRA LOS RESULTADOS DE BÚSQUEDA -->
                    <div class="mt-4 max-h-[60vh] overflow-y-auto">
                        <h3 class="text-sm font-semibold text-gray-500 mb-2 uppercase">Resultados de Búsqueda</h3>
                        
                        @forelse($searchResults as $medicamento)
                            <div class="p-3 border-b border-gray-100">
                                <!-- Nombre del Medicamento -->
                                <h4 class="font-bold text-gray-800">{{ $medicamento->nombre_comercial }} 
                                    <span class="text-sm text-gray-500 font-normal">({{ $medicamento->principio_activo }})</span>
                                </h4>
                                
                                <!-- Lista de Lotes para este Medicamento -->
                                <ul class="mt-2 space-y-1">
                                    @foreach($medicamento->lotes as $lote)
                                        <li class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg">
                                            <!-- Detalles del Lote -->
                                            <div class="text-sm">
                                                Lote: <span class="font-semibold text-gray-700">{{ $lote->numero_lote }}</span> | 
                                                Stock: <span class="font-semibold text-green-600">{{ $lote->cantidad }}</span> | 
                                                Vence: <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y') }}</span> |
                                                Precio: <span class="font-semibold text-blue-600">{{ number_format($lote->precio_venta, 2) }} Bs.</span>
                                            </div>
                                            <!-- Botón Agregar -->
                                            <button wire:click="addToCart({{ $lote->id }})" 
                                                    wire:loading.attr="disabled"
                                                    class="bg-blue-600 text-white px-3 py-1 text-xs font-bold rounded-full hover:bg-blue-700 transition duration-150">
                                                Agregar
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <!-- Mensaje si no hay resultados -->
                            <p class="text-sm text-gray-500 p-3">No se encontraron productos para "{{ $search }}".</p>
                        @endforelse
                    </div>

                @else

                    <!-- B. MUESTRA LOS MÁS VENDIDOS (cuando no se busca) -->
                    <div class="mt-4 max-h-[60vh] overflow-y-auto">
                        <h3 class="text-sm font-semibold text-gray-500 mb-2 uppercase">Productos Más Vendidos (con stock)</h3>
                        
                        @forelse($topSelling as $medicamento)
                            <div class="p-3 border-b border-gray-100">
                                <!-- Nombre del Medicamento -->
                                <h4 class="font-bold text-gray-800">{{ $medicamento->nombre_comercial }} 
                                    <span class="text-sm text-gray-500 font-normal">({{ $medicamento->principio_activo }})</span>
                                </h4>
                                
                                <!-- Lista de Lotes para este Medicamento -->
                                <ul class="mt-2 space-y-1">
                                    @foreach($medicamento->lotes as $lote)
                                        <li class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg">
                                            <!-- Detalles del Lote -->
                                            <div class="text-sm">
                                                Lote: <span class="font-semibold text-gray-700">{{ $lote->numero_lote }}</span> | 
                                                Stock: <span class="font-semibold text-green-600">{{ $lote->cantidad }}</span> | 
                                                Vence: <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y') }}</span> |
                                                Precio: <span class="font-semibold text-blue-600">{{ number_format($lote->precio_venta, 2) }} Bs.</span>
                                            </div>
                                            <!-- Botón Agregar -->
                                            <button wire:click="addToCart({{ $lote->id }})" 
                                                    wire:loading.attr="disabled"
                                                    class="bg-blue-600 text-white px-3 py-1 text-xs font-bold rounded-full hover:bg-blue-700 transition duration-150">
                                                Agregar
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <!-- Mensaje si no hay más vendidos o están sin stock -->
                            <p class="text-sm text-gray-500 p-3">No hay productos más vendidos disponibles en este momento.</p>
                        @endforelse
                    </div>
                
                @endif
                <!-- ***** FIN DE LA LÓGICA CONDICIONAL ***** -->

            </div>
        </div>

        <!-- ======================================== -->
        <!--        PANEL DERECHO (CARRITO)           -->
        <!-- ======================================== -->
        <div class="md:col-span-1">
            <div class="bg-white p-4 rounded-lg shadow sticky top-8">
                <h3 class="text-lg font-bold border-b pb-2 text-gray-900">Carrito de Venta</h3>
                
                <!-- Mensaje de Éxito (cuando la venta se completa) -->
                @if (session()->has('message'))
                    <div class="p-3 my-3 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                        <span class="font-medium">¡Éxito!</span> {{ session('message') }}
                    </div>
                @endif
                
                <!-- Errores de "Stock" o "Cantidad 0" -->
                @error('cart.*.cantidad')
                    <div class="p-3 my-3 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                        <span class="font-medium">¡Error!</span> {{ $message }}
                    </div>
                @enderror
                @error('cart.*.stock')
                    <div class="p-3 my-3 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                        <span class="font-medium">¡Error!</span> {{ $message }}
                    </div>
                @enderror


                <!-- Selector de Paciente -->
                <div class="my-4">
                    <label for="paciente_id" class="block text-sm font-medium text-gray-700">Asociar a Paciente (Opcional)</label>
                    <select wire:model="paciente_id" id="paciente_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Venta General</option>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}">{{ $paciente->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Lista de Items en el Carrito -->
                <div class="mt-4 space-y-3 max-h-60 overflow-y-auto">
                    @forelse($cart as $loteId => $item)
                        <div class="flex justify-between items-center text-sm border-b pb-2">
                            <!-- Info del Producto -->
                            <div>
                                <p class="font-bold text-gray-800">{{ $item['nombre'] }}</p>
                                <p class="text-xs text-gray-500">Lote: {{ $item['lote'] }} | {{ number_format($item['precio'], 2) }} Bs.</p>
                                <!-- Mensaje de error DEBAJO del item (para stock) -->
                                @error('cart.'.$loteId.'.stock') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <!-- Controles de Cantidad -->
                            <div class="flex items-center">
                                <input type="number" 
                                       wire:model.live="cart.{{ $loteId }}.cantidad"
                                       wire:change="updateCartQuantity({{ $loteId }}, $event.target.value)"
                                       min="0"
                                       class="w-16 text-center border-gray-300 rounded-md shadow-sm text-sm">
                                <!-- Botón Eliminar -->
                                <button wire:click="removeFromCart({{ $loteId }})" class="ml-2 text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center text-sm">El carrito está vacío.</p>
                    @endforelse
                </div>

                <!-- Sección de Total y Pago -->
                @if(!empty($cart))
                <div class="mt-6 border-t pt-4">
                    <!-- Total -->
                    <div class="flex justify-between font-bold text-xl text-gray-900">
                        <span>Total:</span>
                        <span>{{ number_format($total, 2) }} Bs.</span>
                    </div>

                    <!-- Método de Pago -->
                    <div class="mt-4">
                        <label for="metodo_pago" class="block text-sm font-medium text-gray-700">Método de Pago</label>
                        <select wire:model="metodo_pago" id="metodo_pago" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="QR">QR</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="mt-6 space-y-2">
                        <!-- Botón Finalizar Venta -->
                        <button wire:click="processSale" 
                                wire:loading.attr="disabled"
                                wire:target="processSale"
                                class="w-full flex justify-center items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 disabled:opacity-50">
                            <span wire:loading.remove wire:target="processSale">
                                Finalizar Venta
                            </span>
                            <span wire:loading wire:target="processSale">
                                Procesando...
                            </span>
                        </button>
                        <!-- Botón Cancelar Venta -->
                        <button wire:click="resetAll" 
                                wire:loading.attr="disabled"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 disabled:opacity-50">
                            Cancelar Venta
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

