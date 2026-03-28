<?php

namespace App\Livewire\Paciente;

use Livewire\Component;
use Illuminate\Support\Facades\Auth; // <-- Importa Auth
use App\Models\HistoriaClinica;      // <-- Importa el modelo
use Livewire\WithPagination;         // <-- Importa paginación

// --- ¡AÑADIDOS! ---
use App\Models\OrdenLaboratorio;
use Illuminate\Support\Facades\Storage;

class MiHistorialClinico extends Component
{
    use WithPagination; // <-- Usa paginación

    public $paciente;

    public function mount()
    {
        // Obtiene el paciente asociado al usuario logueado
        // Asumiendo que tu modelo User tiene una relación llamada 'paciente'
        $this->paciente = Auth::user()->paciente; 

        if (!$this->paciente) {
            // Si por alguna razón el usuario no tiene un paciente vinculado
            // lo redirigimos con un error.
            return redirect()->route('paciente.dashboard')->with('error', 'No se encontró tu perfil de paciente.');
        }
    }

    public function render()
    {
        // Busca el historial para este paciente y lo pagina
        $historialPaginado = HistoriaClinica::where('paciente_id', $this->paciente->id)
                                ->with([
                                    'medico.user', 
                                    'cita', 
                                    'historiaPediatrica',
                                    // --- ¡AÑADIDO! ---
                                    // Cargamos la orden de laboratorio (si existe) a través de la cita
                                    'cita.ordenLaboratorio'
                                ]) 
                                ->orderBy('created_at', 'desc')
                                ->paginate(10); // O el número que prefieras

        // --- ¡ESTA ES LA LÍNEA DE LA SOLUCIÓN! ---
        // Le decimos a Livewire que use la plantilla de Jetstream 'layouts.app'
        return view('livewire.paciente.mi-historial-clinico', [
            'historialPaginado' => $historialPaginado,
            'paciente' => $this->paciente 
        ])->layout('layouts.app');
    }

    // --- ¡NUEVA FUNCIÓN AÑADIDA! ---
    /**
     * Permite al paciente descargar su propio resultado de laboratorio.
     */
    public function descargarResultado($ordenId)
    {
        $paciente = Auth::user()->paciente;
        if (!$paciente) {
            session()->flash('error', 'No se pudo encontrar tu perfil de paciente.');
            return;
        }

        $orden = OrdenLaboratorio::find($ordenId);

        // --- Verificación de Seguridad CRÍTICA ---
        // 1. ¿La orden existe?
        // 2. ¿La orden pertenece AL PACIENTE que está logueado?
        if (!$orden || $orden->paciente_id != $paciente->id) {
            session()->flash('error', 'No se encontró el resultado o no tiene permiso para verlo.');
            return;
        }

        // Verificación de Archivo: ¿El PDF existe?
        if ($orden->resultado_pdf_path && Storage::disk('public')->exists($orden->resultado_pdf_path)) {
            return Storage::disk('public')->download($orden->resultado_pdf_path);
        }

        session()->flash('error', 'El archivo PDF para esta orden no está disponible o aún no se ha generado.');
    }
}