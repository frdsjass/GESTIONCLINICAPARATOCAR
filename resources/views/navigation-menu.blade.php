<div> <!-- ¡ESTA ES LA ETIQUETA RAÍZ QUE FALTABA! -->
<!-- MODIFICADO: Quitamos x-data="{ open: false }" porque el estado lo maneja app.blade.php -->
<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Botón Hamburger para MÓVIL (Ahora se muestra aquí) -->
                <div class="-ms-2 flex items-center sm:hidden">
                    <button @click="sidebarOpen = ! sidebarOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': sidebarOpen, 'inline-flex': ! sidebarOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Menú de la Derecha (Perfil y Logout) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Teams Dropdown (Se queda como estaba) -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam->name }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>
                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    {{-- ¡TRADUCIDO! --}}
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Gestionar Equipo') }}
                                    </div>
                                    
                                    {{-- ¡TRADUCIDO Y RÁPIDO! --}}
                                    <!-- SOLUCIÓN: Añadido wire:navigate -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" wire:navigate>
                                        {{ __('Ajustes del Equipo') }}
                                    </x-dropdown-link>
                                    
                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        {{-- ¡TRADUCIDO Y RÁPIDO! --}}
                                        <!-- SOLUCIÓN: Añadido wire:navigate -->
                                        <x-dropdown-link href="{{ route('teams.create') }}" wire:navigate>
                                            {{ __('Crear Nuevo Equipo') }}
                                        </x-dropdown-link>
                                    @endcan
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200"></div>
                                        {{-- ¡TRADUCIDO! --}}
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Cambiar de Equipo') }}
                                        </div>
                                        @foreach (Auth::user()->allTeams() as $team)
                                            <!-- ESTO SE DEJA ASÍ. El cambio de equipo es una acción, no una navegación -->
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown (Perfil y Logout) -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            {{-- ¡TRADUCIDO! --}}
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Administrar Cuenta') }}
                            </div>

                            {{-- ¡TRADUCIDO Y RÁPIDO! --}}
                            <!-- añadido wire:navigate -->
                            <x-dropdown-link href="{{ route('profile.show') }}" wire:navigate>
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                {{-- ¡RÁPIDO! --}}
                                <!-- añadido wire:navigate -->
                                <x-dropdown-link href="{{ route('api-tokens.index') }}" wire:navigate>
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif
                            
                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <!-- ESTO SE DEJA ASÍ. Logout es un POST, no una navegación -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                {{-- ¡TRADUCIDO! --}}
                                <x-dropdown-link href="{{ route('logout') }}"
                                                 @click.prevent="$root.submit();">
                                    {{ __('Salir') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

        </div>
    </div>

</nav>
</div> 