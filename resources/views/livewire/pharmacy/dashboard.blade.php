<div>
    {{-- Cabecera blanca --}}
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <h1 class="text-2xl font-medium text-gray-900">
            Dashboard de Farmacia
        </h1>
        <p class="mt-2 text-gray-600">Bienvenido/a, {{ auth()->user()->name }}. Revisa el inventario y las ventas.</p>
    </div>

    {{-- Contenido gris --}}
    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">

        {{-- Botones de Acceso Rápido --}}
        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Accesos Rápidos</h2>
            <div class="space-y-4">
                {{-- Botón Inventario --}}
                <a href="{{ route('pharmacy.inventory') }}" class="block w-full text-center bg-teal-500 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded transition duration-150 ease-in-out">
                    Gestionar Inventario
                </a>
                {{-- Botón Punto de Venta --}}
                <a href="{{ route('pharmacy.pos') }}" class="block w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded transition duration-150 ease-in-out">
                    Ir al Punto de Venta (POS)
                </a>
                {{-- Puedes añadir más botones aquí si Farmacia necesita acceso a otras áreas --}}
            </div>
        </div>

        {{-- Alertas de Stock Bajo --}}
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-red-800 mb-4 border-b border-red-200 pb-2">
                <svg class="inline-block w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Alertas de Stock Bajo
            </h2>
            <div class="overflow-x-auto max-h-96"> {{-- Limita altura y permite scroll --}}
                @if($productosBajoStock->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-red-50 sticky top-0"> {{-- Encabezado fijo y rojo claro --}}
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">Medicamento</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">Stock Actual</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase">Stock Mínimo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($productosBajoStock as $medicamento)
                                @php
                                    $stockTotal = $medicamento->lotes->sum('cantidad');
                                    $stockMinimo = $medicamento->lotes->min('stock_minimo') ?? 0;
                                @endphp
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $medicamento->nombre_comercial }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-red-600">{{ $stockTotal }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">{{ $stockMinimo }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center text-green-600 font-semibold py-4">¡Todo bien! No hay productos con stock bajo.</p>
                @endif
            </div>
             <p class="text-xs text-gray-500 mt-3">* Se muestra el stock total sumando todos los lotes del medicamento.</p>
        </div>

    </div>
</div>
