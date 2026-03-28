<div> {{-- ¡ELEMENTO RAÍZ ÚNICO! --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($cita->estado == 'Completada')
                Viendo Consulta - {{ $paciente->nombre_completo ?? 'Paciente Desconocido' }}
            @else
                Atendiendo Cita - {{ $paciente->nombre_completo ?? 'Paciente Desconocido' }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Columna Izquierda: Datos del Paciente --}}
            <div class="md:col-span-1 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Paciente</h3>
                @if($paciente)
                    <div class="space-y-3 text-sm">
                        <p><strong>Nombre:</strong> {{ $paciente->nombre_completo }}</p>
                        <p><strong>C.I.:</strong> {{ $paciente->carnet_identidad }}</p>
                        <p><strong>Fecha Nac.:</strong> {{ $paciente->fecha_nacimiento ? $paciente->fecha_nacimiento->format('d/m/Y') : 'N/A' }} ({{ $paciente->fecha_nacimiento ? $paciente->fecha_nacimiento->age : '' }} años)</p>
                        <p><strong>Género:</strong> {{ $paciente->genero }}</p>
                        <p><strong>Teléfono:</strong> {{ $paciente->telefono }}</p>
                        <p><strong>Email:</strong> {{ $paciente->email ?? 'N/A' }}</p>
                        <div class="pt-4 border-t mt-4">
                            <p class="font-semibold text-red-600">Alergias:</p>
                            <p class="text-red-600">{{ $paciente->alergias ?: 'Ninguna registrada' }}</p>
                        </div>
                         <div class="pt-4 border-t mt-4">
                            <p class="font-semibold">Antecedentes Médicos:</p>
                            <p>{{ $paciente->antecedentes_medicos ?: 'Ninguno registrado' }}</p>
                        </div>
                        <div class="pt-4">
                             <a href="{{ route('doctor.pacientes.historial', $paciente->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                 Ver Historial Clínico Completo &rarr;
                             </a>
                        </div>
                    </div>
                @else
                    <p class="text-red-500">No se pudo cargar la información del paciente.</p>
                @endif
            </div>

            {{-- Columna Derecha: Registro de Consulta --}}
            <div class="md:col-span-2 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Consulta</h3>
                <p class="text-sm text-gray-600 mb-2"><strong>Motivo de la Consulta:</strong> {{ $cita->motivo_consulta ?? 'No especificado' }}</p>

                @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                @php
                    $isCompleted = ($cita->estado == 'Completada');
                @endphp
                
                <form wire:submit="finalizarConsulta">
                    @csrf
                    
                    <fieldset @if($isCompleted) disabled @endif> 
                    
                    {{-- ============ FORMULARIO NÚCLEO COMÚN ============ --}}
                    <h4 class="text-md font-semibold text-gray-700 mt-6 border-b pb-2">Datos Generales</h4>
                    
                    <div class="mt-4">
                        <label for="sintomas" class="block text-sm font-medium text-gray-700">Síntomas *</label>
                        <textarea wire:model.defer="sintomas" id="sintomas" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required></textarea>
                        @error('sintomas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label for="peso_kg" class="block text-sm font-medium text-gray-700">Peso (kg)</label>
                            <input type="number" step="0.1" wire:model.defer="peso_kg" id="peso_kg" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('peso_kg') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="altura_cm" class="block text-sm font-medium text-gray-700">Altura (cm)</label>
                            <input type="number" step="0.1" wire:model.defer="altura_cm" id="altura_cm" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('altura_cm') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="presion_arterial" class="block text-sm font-medium text-gray-700">Presión Art.</label>
                            <input type="text" wire:model.defer="presion_arterial" id="presion_arterial" placeholder="Ej: 120/80" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('presion_arterial') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="temperatura_c" class="block text-sm font-medium text-gray-700">Temp (°C)</label>
                            <input type="number" step="0.1" wire:model.defer="temperatura_c" id="temperatura_c" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('temperatura_c') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="diagnostico" class="block text-sm font-medium text-gray-700">Diagnóstico</label>
                        <textarea wire:model.defer="diagnostico" id="diagnostico" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                        @error('diagnostico') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                     <div class="mt-4">
                        <label for="tratamiento" class="block text-sm font-medium text-gray-700">Tratamiento Indicado</label>
                        <textarea wire:model.defer="tratamiento" id="tratamiento" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                        @error('tratamiento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- ============ INICIO DE MÓDULOS MODULARES ============ --}}
                    
                    {{-- ============ MÓDULO PEDIATRÍA ============ --}}
                    @if($medico->especialidad && $medico->especialidad->nombre == 'Pediatría')
                        <div class="mt-6 pt-4 border-t border-gray-300">
                            <h4 class="text-md font-semibold text-blue-700">Módulo de Pediatría</h4>
                            <div class="mt-4">
                                <label for="perimetro_cefalico_cm" class="block text-sm font-medium text-gray-700">Perímetro Cefálico (cm)</label>
                                <input type="number" step="0.1" wire:model.defer="perimetro_cefalico_cm" id="perimetro_cefalico_cm" class="mt-1 block w-full sm:w-1/2 shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mt-4">
                                <label for="alimentacion" class="block text-sm font-medium text-gray-700">Alimentación</label>
                                <textarea wire:model.defer="alimentacion" id="alimentacion" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                            <div class="mt-4">
                                <label for="desarrollo_psicomotor" class="block text-sm font-medium text-gray-700">Desarrollo Psicomotor</label>
                                <textarea wire:model.defer="desarrollo_psicomotor" id="desarrollo_psicomotor" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                             <div class="mt-4">
                                <label for="esquema_vacunacion" class="block text-sm font-medium text-gray-700">Esquema de Vacunación</label>
                                <textarea wire:model.defer="esquema_vacunacion" id="esquema_vacunacion" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                             </div>
                        </div>
                    
                    {{-- ============ MÓDULO GINECOLOGÍA ============ --}}
                    @elseif($medico->especialidad && $medico->especialidad->nombre == 'Ginecología y Obstetricia')
                        <div class="mt-6 pt-4 border-t border-gray-300">
                            <h4 class="text-md font-semibold text-pink-700">Módulo de Ginecología y Obstetricia</h4>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="fum" class="block text-sm font-medium text-gray-700">F.U.M.</label>
                                    <input type="date" wire:model.defer="fum" id="fum" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label for="gesta" class="block text-sm font-medium text-gray-700">Gesta (N° emb.)</label>
                                    <input type="number" wire:model.defer="gesta" id="gesta" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label for="para" class="block text-sm font-medium text-gray-700">Para (N° partos)</label>
                                    <input type="number" wire:model.defer="para" id="para" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="ciclo_menstrual" class="block text-sm font-medium text-gray-700">Ciclo Menstrual</label>
                                <input type="text" wire:model.defer="ciclo_menstrual" id="ciclo_menstrual" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Regular, 28 días">
                            </div>
                            <div class="mt-4">
                                <label for="ultimo_pap" class="block text-sm font-medium text-gray-700">Último Papanicolau</label>
                                <input type="date" wire:model.defer="ultimo_pap" id="ultimo_pap" class="mt-1 block w-full sm:w-1/2 shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div class="mt-4">
                                <label for="notas_eco_mamaria" class="block text-sm font-medium text-gray-700">Notas Eco. Mamaria</label>
                                <textarea wire:model.defer="notas_eco_mamaria" id="notas_eco_mamaria" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>

                    {{-- ============ MÓDULO CARDIOLOGÍA ============ --}}
                    @elseif($medico->especialidad && $medico->especialidad->nombre == 'Cardiología')
                        <div class="mt-6 pt-4 border-t border-gray-300">
                            <h4 class="text-md font-semibold text-red-700">Módulo de Cardiología</h4>
                            <div class="mt-4">
                                <label for="riesgo_cardiovascular" class="block text-sm font-medium text-gray-700">Riesgo Cardiovascular</label>
                                <input type="text" wire:model.defer="riesgo_cardiovascular" id="riesgo_cardiovascular" class="mt-1 block w-full sm:w-1/2 shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Alto, Medio, Bajo">
                            </div>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="resumen_ecg" class="block text-sm font-medium text-gray-700">Resumen ECG</label>
                                    <textarea wire:model.defer="resumen_ecg" id="resumen_ecg" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                                <div>
                                    <label for="resumen_ecocardiograma" class="block text-sm font-medium text-gray-700">Resumen Ecocardiograma</label>
                                    <textarea wire:model.defer="resumen_ecocardiograma" id="resumen_ecocardiograma" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="colesterol_total" class="block text-sm font-medium text-gray-700">Colesterol Total</label>
                                    <input type="text" wire:model.defer="colesterol_total" id="colesterol_total" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="mg/dL">
                                </div>
                                <div>
                                    <label for="hdl" class="block text-sm font-medium text-gray-700">HDL</label>
                                    <input type="text" wire:model.defer="hdl" id="hdl" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="mg/dL">
                                </div>
                                <div>
                                    <label for="ldl" class="block text-sm font-medium text-gray-700">LDL</label>
                                    <input type="text" wire:model.defer="ldl" id="ldl" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="mg/dL">
                                </div>
                                <div>
                                    <label for="trigliceridos" class="block text-sm font-medium text-gray-700">Triglicéridos</label>
                                    <input type="text" wire:model.defer="trigliceridos" id="trigliceridos" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="mg/dL">
                                </div>
                            </div>
                        </div>
                    
                    {{-- ============ MÓDULO TRAUMATOLOGÍA ============ --}}
                    @elseif($medico->especialidad && $medico->especialidad->nombre == 'Traumatología y Ortopedia')
                        <div class="mt-6 pt-4 border-t border-gray-300">
                            <h4 class="text-md font-semibold text-green-700">Módulo de Traumatología y Ortopedia</h4>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="tipo_lesion" class="block text-sm font-medium text-gray-700">Tipo de Lesión</label>
                                    <input type="text" wire:model.defer="tipo_lesion" id="tipo_lesion" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Fractura, Esguince...">
                                </div>
                                <div>
                                    <label for="zona_afectada" class="block text-sm font-medium text-gray-700">Zona Afectada</label>
                                    <input type="text" wire:model.defer="zona_afectada" id="zona_afectada" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Rodilla derecha">
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="rango_movilidad" class="block text-sm font-medium text-gray-700">Rango de Movilidad</label>
                                    <input type="text" wire:model.defer="rango_movilidad" id="rango_movilidad" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Completo, Limitado">
                                </div>
                                <div>
                                    <label for="reflejos" class="block text-sm font-medium text-gray-700">Reflejos</label>
                                    <input type="text" wire:model.defer="reflejos" id="reflejos" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Conservados">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="resumen_pruebas_imagen" class="block text-sm font-medium text-gray-700">Resumen Pruebas de Imagen (Rayos X, Tomografía)</label>
                                <textarea wire:model.defer="resumen_pruebas_imagen" id="resumen_pruebas_imagen" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>

                    {{-- ============ MÓDULO GASTROENTEROLOGÍA ============ --}}
                    @elseif($medico->especialidad && $medico->especialidad->nombre == 'Gastroenterología')
                        <div class="mt-6 pt-4 border-t border-gray-300">
                            <h4 class="text-md font-semibold text-yellow-700">Módulo de Gastroenterología</h4>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="resumen_endoscopia" class="block text-sm font-medium text-gray-700">Resumen Endoscopia</label>
                                    <textarea wire:model.defer="resumen_endoscopia" id="resumen_endoscopia" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                                <div>
                                    <label for="resumen_colonoscopia" class="block text-sm font-medium text-gray-700">Resumen Colonoscopia</label>
                                    <textarea wire:model.defer="resumen_colonoscopia" id="resumen_colonoscopia" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="prueba_h_pylori" class="block text-sm font-medium text-gray-700">Prueba H. Pylori</label>
                                <input type="text" wire:model.defer="prueba_h_pylori" id="prueba_h_pylori" class="mt-1 block w-full sm:w-1/2 shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej: Positivo, Negativo">
                            </div>
                            <div class="mt-4">
                                <label for="dieta_intolerancias" class="block text-sm font-medium text-gray-700">Dieta e Intolerancias</label>
                                <textarea wire:model.defer="dieta_intolerancias" id="dieta_intolerancias" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                    @endif 
                    {{-- ============ FIN DE MÓDULOS MODULARES ============ --}}


                    {{-- ============ MÓDULOS DE ACCIÓN ============ --}}
                    <div class="mt-6 pt-4 border-t border-gray-300">
                        <h4 class="text-md font-semibold text-gray-700">Acciones Adicionales</h4>
                        
                        {{-- Botón para abrir el modal de Laboratorio --}}
                        <div class="mt-4">
                            <button type="button" wire:click="openLabModal()" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900" @if($isCompleted) disabled @endif>
                                + Solicitar Exámenes de Laboratorio
                            </button>
                        </div>

                        {{-- LISTA DE ÓRDENES CREADAS --}}
                        <div class="mt-4 space-y-2">
                            @if($ordenesDeEstaCita->isNotEmpty())
                                <h5 class="text-sm font-semibold text-gray-600">Órdenes ya solicitadas:</h5>
                                @foreach($ordenesDeEstaCita as $orden)
                                <div class="border rounded-md p-3 bg-gray-50 text-sm flex justify-between items-center">
                                    <div>
                                        <span class="font-medium text-gray-800">Orden #{{ $orden->id }} ({{ $orden->estado }})</span>
                                        <ul class="list-disc pl-5 text-gray-600 mt-1">
                                            @foreach($orden->detalles as $detalle)
                                                <li>{{ $detalle->tipoExamen->nombre }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @if($orden->estado == 'Pendiente')
                                    <button type="button" 
                                            wire:click="eliminarOrdenLaboratorio({{ $orden->id }})" 
                                            wire:confirm="¿Estás seguro de eliminar esta orden de laboratorio?"
                                            class="text-red-500 hover:text-red-700 text-xs font-medium">
                                        Eliminar
                                    </button>
                                    @endif
                                </div>
                                @endforeach
                            @endif
                        </div>
                        
                        {{-- Campo de Receta --}}
                        <div class="mt-4">
                            <label for="receta_medica" class="block text-sm font-medium text-gray-700">Receta Médica (Indicaciones)</label>
                            <textarea wire:model.defer="receta_medica" id="receta_medica" rows="4" placeholder="Ej: Paracetamol 500mg..." class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            @error('receta_medica') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones Finales</label>
                            <textarea wire:model.defer="observaciones" id="observaciones" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            @error('observaciones') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    </fieldset> {{-- Cierre del fieldset --}}


                    {{-- Botón Finalizar --}}
                    <div class="flex items-center justify-end mt-6 pt-6 border-t">
                         <a href="{{ route('doctor.dashboard') }}" class="text-gray-600 hover:text-gray-800 mr-4">Cancelar</a>
                        
                        @if(!$isCompleted)
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                Finalizar Consulta y Guardar
                            </button>
                        @else
                            {{-- Si ya está completada, el botón se ve diferente y permite "Actualizar" --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                Actualizar Datos de Consulta
                            </button>
                        @endif
                    </div>
                </form>
                
                
                {{-- ============ MODAL DE LABORATORIO ============ --}}
                <x-dialog-modal wire:model.live="showLabModal">
                    <x-slot name="title">
                        Solicitar Exámenes de Laboratorio
                    </x-slot>

                    <x-slot name="content">
                        <div class="mb-4">
                            @if ($modalMessage)
                                <div class="px-4 py-3 rounded relative 
                                    {{ $modalMessageType == 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700' }}" role="alert">
                                    {{ $modalMessage }}
                                </div>
                            @endif
                        </div>

                        @if ($ordenRecienCreada)
                            <div class="p-4 border rounded-md bg-gray-50">
                                <h4 class="font-semibold text-lg text-gray-800 mb-2">Orden Creada: #{{ $ordenRecienCreada->id }}</h4>
                                <p class="text-sm text-gray-700 mb-2">Paciente: {{ $ordenRecienCreada->paciente->nombre_completo }}</p>
                                @if($ordenRecienCreada->notas_medico)
                                    <p class="text-sm text-gray-700 mb-2">Notas: {{ $ordenRecienCreada->notas_medico }}</p>
                                @endif
                                <h5 class="font-medium text-gray-800 mt-3">Exámenes Solicitados:</h5>
                                <ul class="list-disc pl-5 text-sm text-gray-600">
                                    @foreach ($ordenRecienCreada->detalles as $detalle)
                                        <li>{{ $detalle->tipoExamen->nombre }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            {{-- Formulario de Búsqueda y Selección --}}
                            <div>
                                <label for="labSearch" class="block text-sm font-medium text-gray-700">Buscar Examen:</label>
                                <input type="text" id="labSearch" wire:model.live.debounce.300ms="labSearch" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Buscar por nombre...">
                            </div>

                            <div class="max-h-60 overflow-y-auto border rounded-md p-4 space-y-2 mt-4">
                                @forelse ($tiposExamenes as $examen)
                                    <label class="flex items-center p-2 rounded-md hover:bg-gray-100">
                                        <input type="checkbox" wire:model="selectedExams.{{ $examen->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-3 text-sm text-gray-700">{{ $examen->nombre }}</span>
                                        <span class="ml-auto text-sm text-gray-500">{{ $examen->precio ? 'Bs. ' . number_format($examen->precio, 2) : '' }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500">No se encontraron exámenes.</p>
                                @endforelse
                            </div>

                            <div class="mt-4">
                                <label for="notas_laboratorio" class="block text-sm font-medium text-gray-700">Notas Adicionales (para el Laboratorio)</label>
                                <textarea wire:model.defer="notas_laboratorio" id="notas_laboratorio" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        @endif
                    </x-slot>

                    <x-slot name="footer">
                        @if ($ordenRecienCreada)
                            <x-button wire:click="closeLabModal()">
                                Cerrar
                            </x-button>
                        @else
                            <x-secondary-button wire:click="closeLabModal()">
                                Cancelar
                            </x-secondary-button>

                            <x-button class="ml-3" wire:click="guardarOrdenLaboratorio()" wire:loading.attr="disabled">
                                Guardar Orden de Laboratorio
                            </x-button>
                        @endif
                    </x-slot>
                </x-dialog-modal>
                {{-- ============ FIN MODAL DE LABORATORIO ============ --}}

            </div>

        </div>
    </div>
</div>