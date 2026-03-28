<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="flex h-screen bg-[#ecfafaff]">
            
            <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'sm:w-64': !sidebarCollapsed, 'sm:w-20': sidebarCollapsed}" 
                   class="fixed inset-y-0 left-0 z-30 bg-[#0A2342] text-[#ffffff] transform transition-all duration-300 ease-in-out 
                          sm:sticky sm:top-0 sm:translate-x-0 h-screen flex flex-col">
                
                {{-- [NOTA] 'bg-sidebar-darker' no estaba en tu config, así que no se pudo convertir --}}
                <div class="flex items-center h-16 border-b border-[#374151] bg-sidebar-darker" :class="sidebarCollapsed ? 'justify-center' : 'justify-between px-4'">
                    {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                    <a href="{{ route('dashboard') }}" class="hidden sm:block flex items-center overflow-hidden" x-show="!sidebarCollapsed" x-cloak>
                        <div class="flex items-center min-w-max">
                            {{-- ====== NUEVO LOGO (SOLO TEXTO) ====== --}}
                            <div class="ml-3 flex flex-col leading-tight">
                                <span class="font-bold text-base text-[#ffffff] whitespace-nowrap">Clínica Más Cerca</span>
                                <span class="font-bold text-base text-[#ffffff] whitespace-nowrap">del Cielo</span>
                            </div>
                        </div>
                    </a>
                    
                    {{-- Logo colapsado (solo una "C") --}}
                    {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                    <a href="{{ route('dashboard') }}" class="hidden sm:block" x-show="sidebarCollapsed" x-cloak>
                         <div class="bg-[#4f46e5] w-8 h-8 flex items-center justify-center rounded-lg shadow-lg">
                             <span class="font-bold text-[#ffffff] text-lg">C</span>
                        </div>
                    </a>

                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden sm:block text-[#9ca3af] hover:text-[#ffffff] p-1 rounded-md transition-colors duration-200 focus:outline-none">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                
                <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                    
                    {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center p-2 rounded-md {{ request()->routeIs('dashboard') || request()->routeIs('paciente.dashboard') || request()->routeIs('doctor.dashboard') || request()->routeIs('reception.dashboard') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}"
                       :class="sidebarCollapsed ? 'justify-center' : ''"
                       title="Inicio">
                         <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4a1 1 0 001-1v-1a1 1 0 10-2 0v1a1 1 0 001 1z"/></svg>
                         <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Inicio</span>
                    </a>

                    @role('Admin')
                        <div class="pt-4 mt-4 border-t border-[#374151]">
                            <h3 class="px-2 text-xs font-semibold text-[#9ca3af] uppercase tracking-wider" x-show="!sidebarCollapsed" x-cloak>Administración</h3>
                            <div class="mt-2 space-y-1">
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('admin.specialties') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('admin.specialties') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Especialidades">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.523 5.754 18 7.5 18s3.332.523 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.523 18.247 18 16.5 18c-1.746 0-3.332.523-4.5 1.253" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Especialidades</span>
                                </a>
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('admin.doctors') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('admin.doctors') || request()->routeIs('admin.doctors.profile') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Médicos">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.08-1.282-.23-1.857m-7.77-1.857a3 3 0 00-5.356 1.857v2h5m-5 0H3v-2a3 3 0 015.356-1.857M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a3 3 0 11-6 0 3 3 0 016 0zM9 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Médicos</span>
                                </a>
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('admin.patients') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('admin.patients') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Pacientes">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Pacientes</span>
                                </a>
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('admin.staff') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('admin.staff') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Personal">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11a3 3 0 11-6 0 3 3 0 016 0zm-3 4a2 2 0 100-4 2 2 0 000 4z" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Personal</span>
                                </a>
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('admin.lab-tests') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('admin.lab-tests') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Tipos de Examen">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 7.5l1.6 1.6M18.4 4.6l2.1 2.1m-2.1-2.1l-1.1 1.1m1.1-1.1l2.1 2.1m-2.1-2.1l-1.1 1.1m0 0l-1.6-1.6M6.2 16l-1.6-1.6M12 21a9 9 0 110-18 9 9 0 010 18z" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Tipos de Examen</span>
                                </a>
                            </div>
                        </div>
                    @endrole
                    
                    @role('Admin|Recepcion')
                        <div class="pt-4 mt-4 border-t border-[#374151]">
                            <h3 class="px-2 text-xs font-semibold text-[#9ca3af] uppercase tracking-wider" x-show="!sidebarCollapsed" x-cloak>Recepción</h3>
                            <div class="mt-2 space-y-1">
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('appointments') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('appointments') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Citas">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Citas</span>
                                </a>
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('patients.manage') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('patients.manage') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Pacientes">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Pacientes</span>
                                </a>
                            </div>
                        </div>
                    @endrole

                    @role('Admin|Farmaceutico')
                         <div class="pt-4 mt-4 border-t border-[#374151]">
                            <h3 class="px-2 text-xs font-semibold text-[#9ca3af] uppercase tracking-wider" x-show="!sidebarCollapsed" x-cloak>Farmacia</h3>
                            <div class="mt-2 space-y-1">
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('pharmacy.inventory') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('pharmacy.inventory') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Inventario">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Inventario</span>
                                </a>
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('pharmacy.pos') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('pharmacy.pos') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Punto de Venta">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Punto de Venta</span>
                                </a>
                            </div>
                        </div>
                    @endrole
                    
                    @role('Medico')
                        <div class="pt-4 mt-4 border-t border-[#374151]">
                            <h3 class="px-2 text-xs font-semibold text-[#9ca3af] uppercase tracking-wider" x-show="!sidebarCollapsed" x-cloak>Médico</h3>
                            <div class="mt-2 space-y-1">
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('doctor.mis-citas') }}" 
                                   class="flex items-center p-2 rounded-md {{ request()->routeIs('doctor.mis-citas') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" 
                                   :class="sidebarCollapsed ? 'justify-center' : ''" 
                                   title="Mis Citas">
                                     <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                   <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Mis Citas</span>
                                </a>
                                
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('doctor.mis-pacientes') }}" 
                                   class="flex items-center p-2 rounded-md {{ request()->routeIs('doctor.mis-pacientes') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" 
                                   :class="sidebarCollapsed ? 'justify-center' : ''" 
                                   title="Mis Pacientes">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                   <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Mis Pacientes</span>
                                </a>

                            </div>
                        </div>
                    @endrole

                    @role('Laboratorista')
                        <div class="pt-4 mt-4 border-t border-[#374151]">
                            <h3 class="px-2 text-xs font-semibold text-[#9ca3af] uppercase tracking-wider" x-show="!sidebarCollapsed" x-cloak>Laboratorio</h3>
                            <div class="mt-2 space-y-1">
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('laboratorio.dashboard') }}" 
                                   class="flex items-center p-2 rounded-md {{ request()->routeIs('laboratorio.dashboard') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" 
                                   :class="sidebarCollapsed ? 'justify-center' : ''" 
                                   title="Cola de Trabajo">
                                     <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.5 1.586l-1.02.982A.75.75 0 008.25 12h7.5a.75.75 0 00.52-.214l-1.02-.982a2.25 2.25 0 01-.5-1.586V3.104A2.25 2.25 0 0012 2.25c-1.148 0-2.14.82-2.25 1.854zM9 16.5v2.25m6-2.25v2.25m3.75-5.25v5.25a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-5.25h12z" />
                                    </svg>
                                   <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Cola de Trabajo</span>
                                </a>
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('laboratorio.historial') }}" 
                                   class="flex items-center p-2 rounded-md {{ request()->routeIs('laboratorio.historial') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" 
                                   :class="sidebarCollapsed ? 'justify-center' : ''" 
                                   title="Historial de Órdenes">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                   <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Historial</span>
                                </a>
                            </div>
                        </div>
                    @endrole
                    @role('Paciente')
                        <div class="pt-4 mt-4 border-t border-[#374151]">
                            <h3 class="px-2 text-xs font-semibold text-[#9ca3af] uppercase tracking-wider" x-show="!sidebarCollapsed" x-cloak>Paciente</h3>
                            <div class="mt-2 space-y-1">
                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('paciente.agendar') }}" class="flex items-center p-2 rounded-md {{ request()->routeIs('paciente.agendar') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Reservar Cita">
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Reservar Cita</span>
                                </a>

                                {{-- ====== CAMBIO: Quitado wire:navigate ====== --}}
                                <a href="{{ route('paciente.historial') }}" 
                                   class="flex items-center p-2 rounded-md {{ request()->routeIs('paciente.historial') ? 'bg-[#0d9488] text-[#ffffff]' : 'text-[#d1d5db] hover:bg-[#0f766e] hover:text-[#ffffff]' }}" 
                                   :class="sidebarCollapsed ? 'justify-center' : ''" 
                                   title="Mi Historial Clínico">
                                    
                                    <svg class="h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="ml-3" x-show="!sidebarCollapsed" x-cloak>Mi Historial Clínico</span>
                                </a>
                                </div>
                        </div>
                    @endrole
                </nav>
            </aside>
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-[#000000] opacity-50 sm:hidden"></div>

            <div class="flex-1 flex flex-col w-full transition-all duration-300 ease-in-out" 
                 :class="sidebarCollapsed ? 'sm:pl-20' : 'sm:pl-64'">
                
                @include('navigation-menu')

                @if (isset($header))
                    <header class="bg-[#ffffff] shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1 p-6 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html> 