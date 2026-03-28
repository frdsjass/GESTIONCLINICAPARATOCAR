<div>
    {{-- Mantenemos el diseño de "tarjeta" blanca --}}
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200 shadow-xl sm:rounded-lg">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-medium text-gray-900">
                Gestión de Especialidades
            </h1>
            {{-- MODIFICADO: Color de botón REVERTIDO a azul --}}
            <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nueva Especialidad
            </button>
        </div>
    </div>

    <div class="bg-white shadow-xl sm:rounded-lg p-6 lg:p-8 mt-6">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        
        <div class="mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($especialidades as $especialidad)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $especialidad->nombre }}</td>
                            <td class="px-6 py-4">{{ $especialidad->descripcion }}</td>
                            
                            <!-- CELDA DE ESTADO -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($especialidad->estado == 'Activo')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            
                            <!-- CELDA DE ACCIONES -->
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                {{-- MODIFICADO: Color de enlace REVERTIDO a índigo --}}
                                <button wire:click="edit({{ $especialidad->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                
                                @if($especialidad->estado == 'Activo')
                                    <button wire:click="toggleEstado({{ $especialidad->id }})" 
                                            wire:confirm="¿Estás seguro de inhabilitar esta especialidad?"
                                            class="text-red-600 hover:text-red-900 ml-4">
                                        Inhabilitar
                                    </button>
                                @else
                                    <button wire:click="toggleEstado({{ $especialidad->id }})" 
                                            class="text-green-600 hover:text-green-900 ml-4">
                                        Habilitar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                {{ $showInactive ? 'No se encontraron especialidades inactivas.' : 'No se encontraron especialidades activas.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $especialidades->links() }}
        </div>

        <!-- Botón para mostrar/ocultar inactivos -->
        <div class="mt-6 border-t pt-4">
            <button wire:click="$toggle('showInactive')" class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                @if ($showInactive)
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"></path></svg>
                    Ocultar especialidades inactivas
                @else
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .91-2.905 3.018-5.26 5.642-6.49M15.125 5.175A10.04 10.04 0 0117.65 6.5C19.732 7.943 21.523 10 21.542 12c-.02 2.057-1.81 4.057-3.894 5.504M9.875 9.175a3 3 0 00-4.242 4.242M14.125 14.825a3 3 0 004.242-4.242" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l18 12M21 6L3 18" /></svg>
                    Mostrar especialidades inactivas
                @endif
            </button>
        </div>

    </div>

    <!-- MODAL (FORMULARIO) -->
    @if ($isOpen)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $specialty_id ? 'Editar Especialidad' : 'Nueva Especialidad' }}</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input type="text" wire:model.defer="nombre" id="nombre" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea wire:model.defer="descripcion" id="descripcion" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- CAMPO DE ESTADO AÑADIDO AL FORMULARIO -->
                            <div>
                                <label for="estado_especialidad" class="block text-sm font-medium text-gray-700">Estado</label>
                                <select id="estado_especialidad" wire:model.defer="estado" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Seleccione un estado</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        {{-- MODIFICADO: Color de botón REVERTIDO a azul --}}
                        <button wire:click.prevent="store()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700">Guardar</button>
                        <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>