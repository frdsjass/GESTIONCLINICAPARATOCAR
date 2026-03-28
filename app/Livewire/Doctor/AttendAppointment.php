<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\HistoriaClinica;
use App\Models\HistoriaPediatrica;
// --- ¡NUEVOS MODELOS IMPORTADOS! ---
use App\Models\HistoriaGinecologica;
use App\Models\HistoriaCardiologica;
use App\Models\HistoriaTraumatologica;
use App\Models\HistoriaGastro;
// --- FIN DE NUEVOS MODELOS ---
use App\Models\TipoExamen;
use App\Models\OrdenLaboratorio;
use App\Models\OrdenExamenDetalle;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendAppointment extends Component
{
    public Cita $cita;
    public ?Paciente $paciente = null;
    public $medico;

    // --- CAMPOS NÚCLEO COMÚN ---
    public $sintomas = '';
    public $diagnostico = '';
    public $tratamiento = '';
    public $receta_medica = '';
    public $observaciones = '';
    public $peso_kg = null;
    public $altura_cm = null;
    public $presion_arterial = '';
    public $temperatura_c = null;

    // --- CAMPOS PEDIATRÍA ---
    public $perimetro_cefalico_cm = null;
    public $esquema_vacunacion = '';
    public $desarrollo_psicomotor = '';
    public $alimentacion = '';

    // --- ¡NUEVOS CAMPOS AÑADIDOS! ---

    // --- CAMPOS GINECOLOGÍA ---
    public $fum = null; // Fecha
    public $gesta = null; // Número
    public $para = null; // Número
    public $ciclo_menstrual = '';
    public $ultimo_pap = null; // Fecha
    public $notas_eco_mamaria = '';

    // --- CAMPOS CARDIOLOGÍA ---
    public $riesgo_cardiovascular = '';
    public $resumen_ecg = '';
    public $resumen_ecocardiograma = '';
    public $colesterol_total = '';
    public $hdl = '';
    public $ldl = '';
    public $trigliceridos = '';

    // --- CAMPOS TRAUMATOLOGÍA ---
    public $tipo_lesion = '';
    public $zona_afectada = '';
    public $rango_movilidad = '';
    public $reflejos = '';
    public $resumen_pruebas_imagen = '';

    // --- CAMPOS GASTROENTEROLOGÍA ---
    public $resumen_endoscopia = '';
    public $resumen_colonoscopia = '';
    public $prueba_h_pylori = '';
    public $dieta_intolerancias = '';

    // --- FIN DE NUEVOS CAMPOS ---

    // --- PROPIEDADES PARA EL MODAL DE LABORATORIO ---
    public $showLabModal = false;
    public $labSearch = '';
    public $selectedExams = [];
    public $notas_laboratorio = '';
    public $ordenRecienCreada = null;
    public $modalMessage = '';
    public $modalMessageType = 'success';
    
    // --- PROPIEDAD PARA ÓRDENES EXISTENTES ---
    public $ordenesDeEstaCita = [];

    protected $listeners = ['ordenEliminada' => 'cargarOrdenesExistentes'];

    public function mount(Cita $cita)
    {
        $this->cita = $cita->load('paciente');
        $this->paciente = $this->cita->paciente;
        $this->medico = Auth::user()->medico->load('especialidad'); 

        if (!$this->paciente) {
             session()->flash('error', 'Error crítico: No se pudo encontrar la información del paciente.');
             return redirect()->route('doctor.dashboard');
        }
        
        if (!$this->medico || $this->cita->medico_id !== $this->medico->id) {
            abort(403, 'No tiene permiso para atender esta cita.');
        }

        // Si la cita ya está completada, cargamos los datos existentes
        if ($this->cita->estado == 'Completada') {
            $this->cargarDatosExistentes();
        }
        
        $this->cargarOrdenesExistentes();
    }

    public function cargarDatosExistentes()
    {
        $historia = HistoriaClinica::where('cita_id', $this->cita->id)
            ->with([ // Cargar todas las relaciones de una vez
                'historiaPediatrica', 
                'historiaGinecologica', 
                'historiaCardiologica', 
                'historiaTraumatologica', 
                'historiaGastro'
            ])->first();
            
        if ($historia) {
            // Cargar Núcleo Común
            $this->sintomas = $historia->sintomas;
            $this->diagnostico = $historia->diagnostico;
            $this->tratamiento = $historia->tratamiento;
            $this->receta_medica = $historia->receta_medica;
            $this->observaciones = $historia->observaciones;
            $this->peso_kg = $historia->peso_kg;
            $this->altura_cm = $historia->altura_cm;
            $this->presion_arterial = $historia->presion_arterial;
            $this->temperatura_c = $historia->temperatura_c;

            // Cargar Pediatría
            if ($historia->historiaPediatrica) {
                $this->perimetro_cefalico_cm = $historia->historiaPediatrica->perimetro_cefalico_cm;
                $this->esquema_vacunacion = $historia->historiaPediatrica->esquema_vacunacion;
                $this->desarrollo_psicomotor = $historia->historiaPediatrica->desarrollo_psicomotor;
                $this->alimentacion = $historia->historiaPediatrica->alimentacion;
            }

            // --- ¡CARGAR DATOS DE NUEVOS MÓDULOS! ---

            // Cargar Ginecología
            if ($historia->historiaGinecologica) {
                $this->fum = $historia->historiaGinecologica->fum;
                $this->gesta = $historia->historiaGinecologica->gesta;
                $this->para = $historia->historiaGinecologica->para;
                $this->ciclo_menstrual = $historia->historiaGinecologica->ciclo_menstrual;
                $this->ultimo_pap = $historia->historiaGinecologica->ultimo_pap;
                $this->notas_eco_mamaria = $historia->historiaGinecologica->notas_eco_mamaria;
            }

            // Cargar Cardiología
            if ($historia->historiaCardiologica) {
                $this->riesgo_cardiovascular = $historia->historiaCardiologica->riesgo_cardiovascular;
                $this->resumen_ecg = $historia->historiaCardiologica->resumen_ecg;
                $this->resumen_ecocardiograma = $historia->historiaCardiologica->resumen_ecocardiograma;
                $this->colesterol_total = $historia->historiaCardiologica->colesterol_total;
                $this->hdl = $historia->historiaCardiologica->hdl;
                $this->ldl = $historia->historiaCardiologica->ldl;
                $this->trigliceridos = $historia->historiaCardiologica->trigliceridos;
            }

            // Cargar Traumatología
            if ($historia->historiaTraumatologica) {
                $this->tipo_lesion = $historia->historiaTraumatologica->tipo_lesion;
                $this->zona_afectada = $historia->historiaTraumatologica->zona_afectada;
                $this->rango_movilidad = $historia->historiaTraumatologica->rango_movilidad;
                $this->reflejos = $historia->historiaTraumatologica->reflejos;
                $this->resumen_pruebas_imagen = $historia->historiaTraumatologica->resumen_pruebas_imagen;
            }

            // Cargar Gastroenterología
            if ($historia->historiaGastro) {
                $this->resumen_endoscopia = $historia->historiaGastro->resumen_endoscopia;
                $this->resumen_colonoscopia = $historia->historiaGastro->resumen_colonoscopia;
                $this->prueba_h_pylori = $historia->historiaGastro->prueba_h_pylori;
                $this->dieta_intolerancias = $historia->historiaGastro->dieta_intolerancias;
            }

        }
    }
    
    public function cargarOrdenesExistentes()
    {
        $this->ordenesDeEstaCita = OrdenLaboratorio::where('cita_id', $this->cita->id)
                                    ->with('detalles.tipoExamen')
                                    ->latest()
                                    ->get();
    }

    public function finalizarConsulta()
    {
        $this->validate([
            // --- Núcleo Común ---
            'sintomas' => 'required|string|min:5',
            'diagnostico' => 'nullable|string',
            'tratamiento' => 'nullable|string',
            'receta_medica' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'peso_kg' => 'nullable|numeric|min:0|max:500',
            'altura_cm' => 'nullable|numeric|min:0|max:300',
            'presion_arterial' => 'nullable|string|max:20',
            'temperatura_c' => 'nullable|numeric|min:30|max:45',
            
            // --- Pediatría ---
            'perimetro_cefalico_cm' => 'nullable|numeric|min:0',
            'esquema_vacunacion' => 'nullable|string',
            'desarrollo_psicomotor' => 'nullable|string',
            'alimentacion' => 'nullable|string',

            // --- ¡NUEVAS REGLAS DE VALIDACIÓN! ---
            
            // --- Ginecología ---
            'fum' => 'nullable|date',
            'gesta' => 'nullable|integer|min:0',
            'para' => 'nullable|integer|min:0',
            'ciclo_menstrual' => 'nullable|string',
            'ultimo_pap' => 'nullable|date',
            'notas_eco_mamaria' => 'nullable|string',

            // --- Cardiología ---
            'riesgo_cardiovascular' => 'nullable|string',
            'resumen_ecg' => 'nullable|string',
            'resumen_ecocardiograma' => 'nullable|string',
            'colesterol_total' => 'nullable|string',
            'hdl' => 'nullable|string',
            'ldl' => 'nullable|string',
            'trigliceridos' => 'nullable|string',

            // --- Traumatología ---
            'tipo_lesion' => 'nullable|string',
            'zona_afectada' => 'nullable|string',
            'rango_movilidad' => 'nullable|string',
            'reflejos' => 'nullable|string',
            'resumen_pruebas_imagen' => 'nullable|string',

            // --- Gastroenterología ---
            'resumen_endoscopia' => 'nullable|string',
            'resumen_colonoscopia' => 'nullable|string',
            'prueba_h_pylori' => 'nullable|string',
            'dieta_intolerancias' => 'nullable|string',
        ]);

        // --- 1. Guardar el Núcleo Común (Esto no cambia) ---
        $historia = HistoriaClinica::updateOrCreate(
            ['cita_id' => $this->cita->id], 
            [ 
                'paciente_id' => $this->paciente->id,
                'medico_id' => $this->medico->id,
                'sintomas' => $this->sintomas,
                'diagnostico' => $this->diagnostico,
                'tratamiento' => $this->tratamiento,
                'receta_medica' => $this->receta_medica,
                'observaciones' => $this->observaciones,
                'peso_kg' => $this->peso_kg,
                'altura_cm' => $this->altura_cm,
                'presion_arterial' => $this->presion_arterial,
                'temperatura_c' => $this->temperatura_c,
            ]
        );

        // --- 2. Guardar el Módulo de Especialidad (¡LÓGICA MEJORADA CON SWITCH!) ---
        if ($this->medico->especialidad) {
            switch ($this->medico->especialidad->nombre) {
                
                case 'Pediatría':
                    HistoriaPediatrica::updateOrCreate(
                        ['historia_clinica_id' => $historia->id], 
                        [ 
                            'perimetro_cefalico_cm' => $this->perimetro_cefalico_cm,
                            'esquema_vacunacion' => $this->esquema_vacunacion,
                            'desarrollo_psicomotor' => $this->desarrollo_psicomotor,
                            'alimentacion' => $this->alimentacion,
                        ]
                    );
                    break;
                
                case 'Ginecología y Obstetricia':
                    HistoriaGinecologica::updateOrCreate(
                        ['historia_clinica_id' => $historia->id], 
                        [ 
                            'fum' => $this->fum,
                            'gesta' => $this->gesta,
                            'para' => $this->para,
                            'ciclo_menstrual' => $this->ciclo_menstrual,
                            'ultimo_pap' => $this->ultimo_pap,
                            'notas_eco_mamaria' => $this->notas_eco_mamaria,
                        ]
                    );
                    break;

                case 'Cardiología':
                    HistoriaCardiologica::updateOrCreate(
                        ['historia_clinica_id' => $historia->id], 
                        [ 
                            'riesgo_cardiovascular' => $this->riesgo_cardiovascular,
                            'resumen_ecg' => $this->resumen_ecg,
                            'resumen_ecocardiograma' => $this->resumen_ecocardiograma,
                            'colesterol_total' => $this->colesterol_total,
                            'hdl' => $this->hdl,
                            'ldl' => $this->ldl,
                            'trigliceridos' => $this->trigliceridos,
                        ]
                    );
                    break;

                case 'Traumatología y Ortopedia':
                    HistoriaTraumatologica::updateOrCreate(
                        ['historia_clinica_id' => $historia->id], 
                        [ 
                            'tipo_lesion' => $this->tipo_lesion,
                            'zona_afectada' => $this->zona_afectada,
                            'rango_movilidad' => $this->rango_movilidad,
                            'reflejos' => $this->reflejos,
                            'resumen_pruebas_imagen' => $this->resumen_pruebas_imagen,
                        ]
                    );
                    break;
                
                case 'Gastroenterología':
                    HistoriaGastro::updateOrCreate(
                        ['historia_clinica_id' => $historia->id], 
                        [ 
                            'resumen_endoscopia' => $this->resumen_endoscopia,
                            'resumen_colonoscopia' => $this->resumen_colonoscopia,
                            'prueba_h_pylori' => $this->prueba_h_pylori,
                            'dieta_intolerancias' => $this->dieta_intolerancias,
                        ]
                    );
                    break;
                
                // default: 
                //     // No hacer nada si es 'Medicina General' u otra.
                //     break;
            }
        }

        // --- 3. Marcar la cita como completada (Esto no cambia) ---
        $this->cita->update(['estado' => 'Completada']);

        session()->flash('message', 'Consulta guardada y registrada.');
        
        return redirect()->route('doctor.dashboard');
    }

    // --- FUNCIONES DEL MODAL DE LABORATORIO (No cambian) ---

    public function openLabModal()
    {
        $this->reset(['labSearch', 'selectedExams', 'notas_laboratorio', 'ordenRecienCreada', 'modalMessage']);
        $this->showLabModal = true;
    }

    public function closeLabModal()
    {
        $this->showLabModal = false;
        $this->reset(['labSearch', 'selectedExams', 'notas_laboratorio', 'ordenRecienCreada', 'modalMessage']);
    }

    public function guardarOrdenLaboratorio()
    {
        $this->modalMessage = ''; 

        if (empty(array_filter($this->selectedExams))) {
            $this->modalMessage = 'Debe seleccionar al menos un examen.';
            $this->modalMessageType = 'error';
            return;
        }

        $orden = OrdenLaboratorio::create([
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'cita_id' => $this->cita->id,
            'estado' => 'Pendiente',
            'notas_medico' => $this->notas_laboratorio,
        ]);

        foreach ($this->selectedExams as $examenId => $isSelected) {
            if ($isSelected) {
                OrdenExamenDetalle::create([
                    'orden_laboratorio_id' => $orden->id,
                    'tipo_examen_id' => $examenId,
                ]);
            }
        }

        $this->ordenRecienCreada = $orden->load('detalles.tipoExamen');
        $this->modalMessage = '¡Orden de Laboratorio #' . $orden->id . ' creada exitosamente!';
        $this->modalMessageType = 'success';
        $this->reset(['labSearch', 'selectedExams', 'notas_laboratorio']);
        
        $this->cargarOrdenesExistentes();
    }
    
    public function eliminarOrdenLaboratorio($ordenId)
    {
        $orden = OrdenLaboratorio::find($ordenId);
        
        if ($orden && $orden->medico_id == $this->medico->id && $orden->cita_id == $this->cita->id) {
            $orden->delete(); 
            $this->cargarOrdenesExistentes(); 
            session()->flash('message', 'Orden de laboratorio eliminada.'); 
        }
    }


    public function render()
    {
        $tiposExamenes = [];
        if ($this->showLabModal && !$this->ordenRecienCreada) { 
            $query = TipoExamen::where('estado', 'Activo');
            
            if ($this->labSearch) {
                $query->where('nombre', 'like', '%' . $this->labSearch . '%');
            }
            $tiposExamenes = $query->take(10)->get();
        }

        return view('livewire.doctor.attend-appointment', [
            'cita' => $this->cita,
            'paciente' => $this->paciente,
            'medico' => $this->medico,
            'tiposExamenes' => $tiposExamenes,
            'ordenesDeEstaCita' => $this->ordenesDeEstaCita,
        ])->layout('layouts.app');
    }
}