<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historial Clínico de: {{ $paciente->nombre_completo ?? 'Paciente Desconocido' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- MENSAJES DE ERROR PARA LA DESCARGA --}}
            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Datos del Paciente</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <p><strong>Nombre:</strong> {{ $paciente->nombre_completo }}</p>
                    <p><strong>C.I.:</strong> {{ $paciente->carnet_identidad }}</p>
                    <p><strong>Fecha Nac.:</strong> {{ $paciente->fecha_nacimiento ? $paciente->fecha_nacimiento->format('d/m/Y') : 'N/A' }} ({{ $paciente->fecha_nacimiento ? $paciente->fecha_nacimiento->age : '' }} años)</p>
                    <p><strong>Género:</strong> {{ $paciente->genero }}</p>
                    <p><strong>Teléfono:</strong> {{ $paciente->telefono }}</p>
                    <p><strong>Email:</strong> {{ $paciente->email ?? 'N/A' }}</p>
                    <div class="md:col-span-2"><p><strong>Dirección:</strong> {{ $paciente->direccion ?? 'N/A' }}</p></div>
                    <div class="md:col-span-2 pt-4 border-t mt-4"><p class="font-semibold text-red-600">Alergias:</p><p class="text-red-600">{{ $paciente->alergias ?: 'Ninguna registrada' }}</p></div>
                    <div class="md:col-span-2 pt-4 border-t mt-4"><p class="font-semibold">Antecedentes Médicos:</p><p>{{ $paciente->antecedentes_medicos ?: 'Ninguno registrado' }}</p></div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Historial de Consultas</h3>

                <div class="space-y-6">
                    @forelse ($historialPaginado as $entrada)
                    <div class="border rounded-lg p-4 shadow-sm {{ $loop->odd ? 'bg-gray-50' : '' }}">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-gray-800">
                                @php
                                    $fecha = $entrada->cita?->fecha_hora_inicio ?? $entrada->created_at;
                                    $fechaFormateada = $fecha ? ucfirst($fecha->locale('es')->isoFormat('dddd, DD [de] MMMM [de] YYYY - hh:mm A')) : 'Fecha desconocida';
                                @endphp
                                {{ $fechaFormateada }}
                            </span>
                            <span class="text-sm text-gray-600">Atendido por: Dr(a). {{ $entrada->medico->user->name ?? 'Desconocido' }}</span>
                        </div>
                        
                        <div>
                            <p class="text-sm mt-1"><strong class="font-medium text-gray-700">Síntomas:</strong> {{ $entrada->sintomas ?? 'N/A' }}</p>
                            <p class="text-sm mt-1"><strong class="font-medium text-gray-700">Diagnóstico:</strong> {{ $entrada->diagnostico ?? 'N/A' }}</p>
                            <p class="text-sm mt-1"><strong class="font-medium text-gray-700">Tratamiento:</strong> {{ $entrada->tratamiento ?? 'N/A' }}</p>
                            @if($entrada->receta_medica)
                            <p class="text-sm mt-1"><strong class="font-medium text-gray-700">Receta:</strong> {{ $entrada->receta_medica }}</p>
                            @endif
                            @if($entrada->observaciones)
                            <p class="text-sm mt-1"><strong class="font-medium text-gray-700">Observaciones:</strong> {{ $entrada->observaciones }}</p>
                            @endif
                            <div class="text-xs text-gray-500 mt-2 flex space-x-4">
                                @if($entrada->peso_kg)<span>Peso: {{ $entrada->peso_kg }} kg</span>@endif
                                @if($entrada->altura_cm)<span>Altura: {{ $entrada->altura_cm }} cm</span>@endif
                                @if($entrada->presion_arterial)<span>Presión: {{ $entrada->presion_arterial }}</span>@endif
                                @if($entrada->temperatura_c)<span>Temp: {{ $entrada->temperatura_c }}°C</span>@endif
                            </div>
                        </div>

                        {{-- =================================== --}}
                        {{-- == INICIO: FASE N (Ver PDF)      == --}}
                        {{-- =================================== --}}
                        {{-- 1. Verificamos si existe una cita --}}
                        {{-- 2. Verificamos si esa cita tiene una orden de laboratorio --}}
                        {{-- 3. Verificamos si esa orden tiene un PDF generado --}}
                        @if($entrada->cita && $entrada->cita->ordenLaboratorio && $entrada->cita->ordenLaboratorio->resultado_pdf_path)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <h5 class="text-sm font-semibold text-gray-800">Archivos Adjuntos</h5>
                            <div class="mt-2">
                                <button wire:click="descargarResultado({{ $entrada->cita->ordenLaboratorio->id }})" 
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                    <!-- Icono de PDF -->
                                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625a1.875 1.875 0 00-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V11.25a1.875 1.875 0 00-1.875-1.875H9.75M12 9.75v-4.5" />
                                    </svg>
                                    Ver Resultados de Laboratorio (PDF)
                                </button>
                            </div>
                        </div>
                        @endif
                        {{-- =================================== --}}
                        {{-- == FIN: FASE N (Ver PDF)         == --}}
                        {{-- =================================== --}}


                        {{-- ============ INICIO DE MÓDULOS MODULARES ============ --}}

                        @if($entrada->historiaPediatrica)
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <h5 class="text-sm font-semibold text-blue-700">Datos Pediátricos</h5>
                            <div class="text-sm text-gray-600 mt-1 space-y-1">
                                <p><strong>Perímetro Cefálico:</strong> {{ $entrada->historiaPediatrica->perimetro_cefalico_cm ?? 'N/A' }} cm</p>
                                <p><strong>Alimentación:</strong> {{ $entrada->historiaPediatrica->alimentacion ?? 'N/A' }}</p>
                                <p><strong>Desarrollo Psicomotor:</strong> {{ $entrada->historiaPediatrica->desarrollo_psicomotor ?? 'N/A' }}</p>
                                <p><strong>Vacunación:</strong> {{ $entrada->historiaPediatrica->esquema_vacunacion ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endif

                        @if($entrada->historiaGinecologica)
                        <div class="mt-3 pt-3 border-t border-pink-200">
                            <h5 class="text-sm font-semibold text-pink-700">Datos Ginecológicos</h5>
                            <div class="text-sm text-gray-600 mt-1 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
                                <p><strong>FUM:</strong> {{ $entrada->historiaGinecologica->fum ? \Carbon\Carbon::parse($entrada->historiaGinecologica->fum)->format('d/m/Y') : 'N/A' }}</p>
                                <p><strong>Gesta:</strong> {{ $entrada->historiaGinecologica->gesta ?? 'N/A' }}</p>
                                <p><strong>Para:</strong> {{ $entrada->historiaGinecologica->para ?? 'N/A' }}</p>
                                <p><strong>Ciclo Menstrual:</strong> {{ $entrada->historiaGinecologica->ciclo_menstrual ?? 'N/A' }}</p>
                                <p><strong>Último PAP:</strong> {{ $entrada->historiaGinecologica->ultimo_pap ? \Carbon\Carbon::parse($entrada->historiaGinecologica->ultimo_pap)->format('d/m/Y') : 'N/A' }}</p>
                                <p class="sm:col-span-2"><strong>Eco. Mamaria:</strong> {{ $entrada->historiaGinecologica->notas_eco_mamaria ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($entrada->historiaCardiologica)
                        <div class="mt-3 pt-3 border-t border-red-200">
                            <h5 class="text-sm font-semibold text-red-700">Datos Cardiológicos</h5>
                            <div class="text-sm text-gray-600 mt-1 space-y-1">
                                <p><strong>Riesgo Cardiovascular:</strong> {{ $entrada->historiaCardiologica->riesgo_cardiovascular ?? 'N/A' }}</p>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-4">
                                    <p><strong>Colesterol Total:</strong> {{ $entrada->historiaCardiologica->colesterol_total ?? 'N/A' }}</p>
                                    <p><strong>HDL:</strong> {{ $entrada->historiaCardiologica->hdl ?? 'N/A' }}</p>
                                    <p><strong>LDL:</strong> {{ $entrada->historiaCardiologica->ldl ?? 'N/A' }}</p>
                                    <p><strong>Triglicéridos:</strong> {{ $entrada->historiaCardiologica->trigliceridos ?? 'N/A' }}</p>
                                </div>
                                <p><strong>Resumen ECG:</strong> {{ $entrada->historiaCardiologica->resumen_ecg ?? 'N/A' }}</p>
                                <p><strong>Resumen Ecocardiograma:</strong> {{ $entrada->historiaCardiologica->resumen_ecocardiograma ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($entrada->historiaTraumatologica)
                        <div class="mt-3 pt-3 border-t border-green-200">
                            <h5 class="text-sm font-semibold text-green-700">Datos Traumatológicos</h5>
                            <div class="text-sm text-gray-600 mt-1 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
                                <p><strong>Tipo de Lesión:</strong> {{ $entrada->historiaTraumatologica->tipo_lesion ?? 'N/A' }}</p>
                                <p><strong>Zona Afectada:</strong> {{ $entrada->historiaTraumatologica->zona_afectada ?? 'N/A' }}</p>
                                <p><strong>Rango de Movilidad:</strong> {{ $entrada->historiaTraumatologica->rango_movilidad ?? 'N/A' }}</p>
                                <p><strong>Reflejos:</strong> {{ $entrada->historiaTraumatologica->reflejos ?? 'N/A' }}</p>
                                <p class="sm:col-span-2"><strong>Pruebas de Imagen:</strong> {{ $entrada->historiaTraumatologica->resumen_pruebas_imagen ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($entrada->historiaGastro)
                        <div class="mt-3 pt-3 border-t border-yellow-200">
                            <h5 class="text-sm font-semibold text-yellow-700">Datos Gastroenterológicos</h5>
                            <div class="text-sm text-gray-600 mt-1 space-y-1">
                                <p><strong>Prueba H. Pylori:</strong> {{ $entrada->historiaGastro->prueba_h_pylori ?? 'N/A' }}</p>
                                <p><strong>Dieta/Intolerancias:</strong> {{ $entrada->historiaGastro->dieta_intolerancias ?? 'N/A' }}</p>
                                <p><strong>Resumen Endoscopia:</strong> {{ $entrada->historiaGastro->resumen_endoscopia ?? 'N/A' }}</p>
                                <p><strong>Resumen Colonoscopia:</strong> {{ $entrada->historiaGastro->resumen_colonoscopia ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- ============ FIN DE MÓDULOS MODULARES ============ --}}

                    </div>
                    @empty
                    <p class="text-center text-gray-500">Este paciente aún no tiene registros en su historial clínico.</p>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $historialPaginado->links() }}
                </div>
            </div>
        </div>
    </div>
</div>