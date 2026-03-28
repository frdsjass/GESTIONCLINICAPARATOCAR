<div>
    {{-- Cabecera blanca --}}
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-medium text-gray-900">
                Gestión de Personal
            </h1>
            <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Registrar Nuevo Personal
            </button>
        </div>
    </div>

    {{-- Contenido gris --}}
    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        {{-- Mensaje de éxito --}}
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        
        {{-- Buscador --}}
        <div class="mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, email o carnet..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carnet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($personal as $item) 
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->user->name ?? 'N/A' }}</td> 
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->user->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->carnet_identidad }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($item->user->getRoleNames()->first() == 'Recepcion') 
                                        bg-blue-100 text-blue-800
                                    @elseif($item->user->getRoleNames()->first() == 'Farmaceutico') 
                                        bg-purple-100 text-purple-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $item->user->getRoleNames()->first() ?? 'Sin Rol' }}
                                </span>
                            </td>
                             {{-- Celda de Estado --}}
                             <td class="px-6 py-4 whitespace-nowrap">
                                 @if($item->estado == 'Activo')
                                     <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                         Activo
                                     </span>
                                 @else
                                     <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                         Inactivo
                                     </span>
                                 @endif
                             </td>
                            
                            {{-- Acciones (Estado fusionado con Eliminar) --}}
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                <button wire:click="edit({{ $item->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                
                                @if($item->estado == 'Activo')
                                    <button wire:click="toggleEstado({{ $item->id }})" 
                                            wire:confirm="¿Estás seguro de inhabilitar a {{ $item->user->name }}? Bloqueará su acceso al sistema."
                                            class="text-red-600 hover:text-red-900 ml-4">
                                        Inhabilitar
                                    </button>
                                @else
                                    <button wire:click="toggleEstado({{ $item->id }})" 
                                            class="text-green-600 hover:text-green-900 ml-4">
                                        Habilitar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                {{ $showInactive ? 'No se encontró personal inactivo.' : 'No se encontró personal activo.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Paginación --}}
        <div class="mt-4">
            {{ $personal->links() }}
        </div>

        <!-- Botón para mostrar/ocultar inactivos -->
        <div class="mt-6 border-t pt-4">
            <button wire:click="$toggle('showInactive')" class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                @if ($showInactive)
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"></path></svg>
                    Ocultar personal inactivo
                @else
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .91-2.905 3.018-5.26 5.642-6.49M15.125 5.175A10.04 10.04 0 0117.65 6.5C19.732 7.943 21.523 10 21.542 12c-.02 2.057-1.81 4.057-3.894 5.504M9.875 9.175a3 3 0 00-4.242 4.242M14.125 14.825a3 3 0 004.242-4.242" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l18 12M21 6L3 18" /></svg>
                    Mostrar personal inactivo
                @endif
            </button>
        </div>

    </div>

    <!-- MODAL (FORMULARIO CON OJITO) -->
    @if ($isOpen)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $personal_id ? 'Editar Personal' : 'Registrar Nuevo Personal' }}</h3>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                <input type="text" wire:model.defer="name" id="name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" wire:model.defer="email" id="email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                             <!-- Carnet de Identidad -->
                             <div class="md:col-span-2">
                                 <label for="carnet_identidad" class="block text-sm font-medium text-gray-700">Carnet de Identidad</label>
                                 <input type="text" wire:model.defer="carnet_identidad" id="carnet_identidad" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                 @error('carnet_identidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                             </div>
                            
                            <!-- Contraseña con Ojito -->
                            <div class="col-span-2" x-data="{ showPassword: false }"> 
                                <label for="password_personal" class="block text-sm font-medium text-gray-700">Contraseña</label>
                                <div class="relative mt-1"> 
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           wire:model.defer="password" 
                                           id="password_personal"
                                           class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 pr-10" 
                                           placeholder="{{ $personal_id ? 'Dejar en blanco para no cambiar' : '' }}">
                                    
                                    <button type="button" @click="showPassword = !showPassword" 
                                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                                        {{-- Icono Ojo Abierto --}}
                                        <svg x-show="!showPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{-- Icono Ojo Cerrado --}}
                                         <svg x-show="showPassword" x-cloak class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .91-2.905 3.018-5.26 5.642-6.49M15.125 5.175A10.04 10.04 0 0117.65 6.5C19.732 7.943 21.523 10 21.542 12c-.02 2.057-1.81 4.057-3.894 5.504M9.875 9.175a3 3 0 00-4.242 4.242M14.125 14.825a3 3 0 004.242-4.242" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l18 12M21 6L3 18" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Rol -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                                <select wire:model.defer="role" id="role" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione un rol</option> 
                                    @if(isset($roles))
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                             <!-- Teléfono -->
                             <div>
                                 <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono (Opcional)</label>
                                 <input type="text" wire:model.defer="telefono" id="telefono" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                 @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                             </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                <select wire:model.defer="estado" id="estado" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione un estado</option> 
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección (Opcional)</label>
                                <input type="text" wire:model.defer="direccion" id="direccion" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('direccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                    {{-- Botones del Modal --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click.prevent="store()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Guardar</button>
                        <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>