<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth; 
use App\Models\HistoriaClinica;     
use Livewire\WithPagination;       

use App\Models\OrdenLaboratorio;
use Illuminate\Support\Facades\Storage;
use App\Models\Paciente; // <-- AÑADIDO IMPORT DEL MODELO PACIENTE

class PacienteHistorialClinico extends Component
{
    use WithPagination;

    public Paciente $paciente; // <-- Corregido a tipo 'Paciente'

    // ============ INICIO DE LA CORRECCIÓN ============
    /**
     * El método mount RECIBE al paciente desde la ruta (Route Model Binding).
     * NO debe buscar el paciente del usuario autenticado.
     */
    public function mount(Paciente $paciente)
    {
        // Esto asigna el paciente que viene de la URL (ej. /pacientes/15/historial)
        // a la propiedad $this->paciente
        $this->paciente = $paciente; 

        // Se elimina la redirección y la comprobación de Auth::user()->paciente
        // La seguridad de quién puede ver esto ya se maneja en las RUTAS (routes/web.php)
        // con el middleware 'role:Admin|Recepcion|Medico'.
    }
    // ============ FIN DE LA CORRECCIÓN ============

    public function render()
    {
        // Busca el historial para el paciente CARGADO EN MOUNT
        $historialPaginado = HistoriaClinica::where('paciente_id', $this->paciente->id)
                                ->with([
                                    'medico.user', 
                                    'cita', 
                                    'historiaPediatrica',
                                    'historiaGinecologica', // (Añadí las que faltaban por si acaso)
                                    'historiaCardiologica',
                                    'historiaTraumatologica',
                                    'historiaGastro',
                                    'cita.ordenLaboratorio'
                                ]) 
                                ->orderBy('created_at', 'desc')
                                ->paginate(10); 

        // ============ CORRECCIÓN DE LA RUTA DE LA VISTA ============
        // Debe renderizar su propia vista (la que está en livewire/paciente-historial-clinico.blade.php)
        return view('livewire.paciente-historial-clinico', [
            'historialPaginado' => $historialPaginado,
            'paciente' => $this->paciente 
        ])->layout('layouts.app');
    }

    /**
     * Permite al usuario (Admin, Medico, Paciente) descargar el resultado.
     */
    public function descargarResultado($ordenId)
    {
        $orden = OrdenLaboratorio::find($ordenId);

        // --- Verificación de Seguridad CRÍTICA ---
        // 1. ¿La orden existe?
        // 2. ¿La orden pertenece AL PACIENTE QUE ESTAMOS VIENDO?
        // (Esta lógica estaba bien, porque se basa en $this->paciente, que ahora es el correcto)
        if (!$orden || $orden->paciente_id != $this->paciente->id) {
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