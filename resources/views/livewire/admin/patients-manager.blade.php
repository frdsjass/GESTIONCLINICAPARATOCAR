<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-medium text-gray-900">
                Gestión de Pacientes
            </h1>
            <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Registrar Nuevo Paciente
            </button>
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, carnet o email..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email (Usuario)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carnet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pacientes as $paciente)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $paciente->nombre_completo }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $paciente->user?->email ?? 'Sin Cuenta' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $paciente->carnet_identidad }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $paciente->telefono }}</td>

                            <!-- CELDA DE ESTADO -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($paciente->estado == 'Activo')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <!-- CELDA DE ACCIONES (FUSIONADA) -->
                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">

                                {{-- ¡AQUÍ ESTÁ LA CORRECCIÓN MEJORADA! --}}
                                {{-- Ahora comprueba el rol del usuario --}}
                                @if(Auth::user()->hasRole('Admin'))
                                    <a href="{{ route('admin.pacientes.historial', $paciente->id) }}" wire:navigate class="text-blue-600 hover:text-blue-900">
                                        Historial
                                    </a>
                                @elseif(Auth::user()->hasRole('Recepcion'))
                                    <a href="{{ route('reception.pacientes.historial', $paciente->id) }}" wire:navigate class="text-blue-600 hover:text-blue-900">
                                        Historial
                                    </a>
                                @endif
                                {{-- FIN DE LA CORRECCIÓN MEJORADA --}}

                                <button wire:click="edit({{ $paciente->id }})" class="text-indigo-600 hover:text-indigo-900 ml-4">
                                    Editar
                                </button>

                                {{-- Botón de Habilitar/Inhabilitar (Reemplaza a Eliminar) --}}
                                @if($paciente->estado == 'Activo')
                                    <button wire:click="toggleEstado({{ $paciente->id }})" 
                                            wire:confirm="¿Estás seguro de inhabilitar a este paciente? Su acceso al login quedará bloqueado."
                                            class="text-red-600 hover:text-red-900 ml-4">
                                        Inhabilitar
                                    </button>
                                @else
                                    <button wire:click="toggleEstado({{ $paciente->id }})" 
                                            class="text-green-600 hover:text-green-900 ml-4">
                                        Habilitar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                {{ $showInactive ? 'No se encontraron pacientes inactivos.' : 'No se encontraron pacientes activos.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pacientes->links() }}
        </div>
        
        <!-- Botón para mostrar/ocultar inactivos -->
        <div class="mt-6 border-t pt-4">
            <button wire:click="$toggle('showInactive')" class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                @if ($showInactive)
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"></path></svg>
                    Ocultar pacientes inactivos
                @else
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .91-2.905 3.018-5.26 5.642-6.49M15.125 5.175A10.04 10.04 0 0117.65 6.5C19.732 7.943 21.523 10 21.542 12c-.02 2.057-1.81 4.057-3.894 5.504M9.875 9.175a3 3 0 00-4.242 4.242M14.125 14.825a3 3 0 004.242-4.242" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l18 12M21 6L3 18" /></svg>
                    Mostrar pacientes inactivos
                @endif
            </button>
        </div>
    </div>

    <!-- MODAL (FORMULARIO ACTUALIZADO) -->
    @if ($isOpen)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="store">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $paciente_id ? 'Editar Paciente' : 'Nuevo Paciente' }}</h3>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Nombre Completo --}}
                            <div class="col-span-2">
                                <label for="nombre_completo" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                <input type="text" wire:model.defer="nombre_completo" id="nombre_completo" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('nombre_completo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email (Opcional) --}}
                            <div class="col-span-2">
                                <label for="email_paciente" class="block text-sm font-medium text-gray-700">Email (Opcional - para inicio de sesión)</label>
                                <input type="email" wire:model.defer="email" id="email_paciente" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Contraseña (Opcional, con ojito) --}}
                            <div class="col-span-2" x-data="{ showPassword: false }">
                                <label for="password_paciente" class="block text-sm font-medium text-gray-700">Contraseña (Opcional)</label>
                                <div class="relative mt-1">
                                    <input :type="showPassword ? 'text' : 'password'"
                                           wire:model.defer="password"
                                           id="password_paciente"
                                           class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                                           placeholder="{{ $paciente_id ? ($user_id ? 'Dejar en blanco para no cambiar' : 'Asignar si se dio email') : 'Asignar si se dio email' }}">

                                    <button type="button" @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                                        {{-- Iconos SVG del ojito --}}
                                        <svg x-show="!showPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" /></svg>
                                        <svg x-show="showPassword" x-cloak class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .91-2.905 3.018-5.26 5.642-6.49M15.125 5.175A10.04 10.04 0 0117.65 6.5C19.732 7.943 21.523 10 21.542 12c-.02 2.057-1.81 4.057-3.894 5.504M9.875 9.175a3 3 0 00-4.242 4.242M14.125 14.825a3 3 0 004.242-4.242" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l18 12M21 6L3 18" /></svg>
                                    </button>
                                </div>
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Datos del Paciente --}}
                            <div>
                                <label for="carnet_identidad" class="block text-sm font-medium text-gray-700">Carnet</label>
                                <input type="text" wire:model.defer="carnet_identidad" id="carnet_identidad" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('carnet_identidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha Nacimiento</label>
                                <input type="date" wire:model.defer="fecha_nacimiento" id="fecha_nacimiento" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('fecha_nacimiento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="genero" class="block text-sm font-medium text-gray-700">Género</label>
                                <select wire:model.defer="genero" id="genero" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                @error('genero') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                             <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" wire:model.defer="telefono" id="telefono" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2">
                                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección (Opcional)</label>
                                <input type="text" wire:model.defer="direccion" id="direccion" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="col-span-2">
                                <label for="antecedentes_medicos" class="block text-sm font-medium text-gray-700">Antecedentes Médicos (Opcional)</label>
                                <textarea wire:model.defer="antecedentes_medicos" id="antecedentes_medicos" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                             <div class="col-span-2">
                                <label for="alergias" class="block text-sm font-medium text-gray-700">Alergias (Opcional)</label>
                                <textarea wire:model.defer="alergias" id="alergias" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>

                            {{-- Estado --}}
                            <div class="col-span-2">
                                <label for="estado_paciente" class="block text-sm font-medium text-gray-700">Estado</label>
                                <select id="estado_paciente" wire:model.defer="estado" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione un estado</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                    {{-- Botones del Modal --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Guardar</button>
                        <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>