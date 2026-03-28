<?php

namespace App\Livewire\Laboratorista;

use App\Models\OrdenExamenDetalle;
use Livewire\Component;
use App\Models\OrdenLaboratorio;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage; 
use Barryvdh\DomPDF\Facade\Pdf; 

class Dashboard extends Component
{
    use WithPagination;

    // --- Propiedades de la "Cola de Trabajo" ---
    public $search = '';
    public $filtroEstado = 'Pendiente';

    // --- Propiedades del Modal "Procesar Orden" ---
    public $isOpen = false;
    public ?OrdenLaboratorio $ordenSeleccionada = null;
    public $orden_id;

    // Campos del formulario del modal
    public $resultados_texto = '';
    public $estadoOrden = 'Resultados Listos';
    
    // --- ¡MODIFICADO! (Fase M) ---
    // Esta variable ahora guardará un array de arrays para todos los campos de detalle
    public $detallesForm = [];
    // Ejemplo: [ 1 => ['resultados' => '110', 'valores_referencia' => '70-120', 'metodologia' => '...'], 2 => [...] ]

    protected $listeners = ['ordenProcesada' => '$refresh'];

    public function render()
    {
        // =============================================
        // ¡INICIO DE LA MODIFICACIÓN! 
        // Se eliminó el cálculo de $stats de este método.
        // =============================================

        // --- LÓGICA DE LA TABLA (COLA DE TRABAJO) ---
        // (Este es tu código original)
        $ordenesQuery = OrdenLaboratorio::with(['paciente', 'medico.user', 'detalles.tipoExamen'])
            ->when($this->search, function ($query) {
                $query->whereHas('paciente', function ($q) {
                    $q->where('nombre_completo', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('medico.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });

        if (!empty($this->filtroEstado)) {
            $ordenesQuery->where('estado', $this->filtroEstado);
        }

        $ordenes = $ordenesQuery->orderBy('created_at', 'asc')->paginate(10);

        // --- PASAR DATOS A LA VISTA ---
        return view('livewire.laboratorista.dashboard', [
            'ordenes' => $ordenes,
            // 'stats' => $stats  <-- ¡Eliminado!
        ])->layout('layouts.app');

        // =============================================
        // ¡FIN DE LA MODIFICACIÓN!
        // =============================================
    }

    public function procesarOrden($ordenId)
    {
        $this->resetErrorBag();
        $this->orden_id = $ordenId;
        $this->ordenSeleccionada = OrdenLaboratorio::with('detalles.tipoExamen', 'paciente', 'medico.user')->find($ordenId); 
        
        if(!$this->ordenSeleccionada) {
            session()->flash('error', 'Error: No se pudo encontrar la orden.');
            return;
        }

        $this->resultados_texto = $this->ordenSeleccionada->resultados_texto;
        $this->estadoOrden = $this->ordenSeleccionada->estado;
        
        // --- ¡MODIFICADO! (Fase M) ---
        // Pre-carga los resultados de los detalles en la nueva estructura
        $this->detallesForm = [];
        foreach ($this->ordenSeleccionada->detalles as $detalle) {
            $this->detallesForm[$detalle->id] = [
                'resultados' => $detalle->resultados,
                'valores_referencia' => $detalle->valores_referencia,
                'metodologia' => $detalle->metodologia,
            ];
        }

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        // --- ¡MODIFICADO! (Fase M) ---
        $this->reset(['ordenSeleccionada', 'orden_id', 'resultados_texto', 'estadoOrden', 'detallesForm']);
    }

    /**
     * ¡¡MODIFICADO!! (Fase M) Guarda los resultados Y GENERA EL PDF.
     */
    public function guardarResultados()
    {
        $this->validate([
            'resultados_texto' => 'nullable|string',
            'estadoOrden' => 'required|in:Pendiente,Muestra Tomada,Resultados Listos,Cancelada',
            // --- ¡MODIFICADO! (Fase M) ---
            // Validación para la nueva estructura de array
            'detallesForm.*.resultados' => 'nullable|string',
            'detallesForm.*.valores_referencia' => 'nullable|string|max:255',
            'detallesForm.*.metodologia' => 'nullable|string|max:255',
        ]);

        if (!$this->ordenSeleccionada) {
            session()->flash('error', 'No se ha seleccionado ninguna orden.');
            return;
        }

        // --- ¡MODIFICADO! (Fase M) ---
        // 1. Actualizar los resultados de cada examen individual
        foreach ($this->detallesForm as $detalleId => $campos) {
            $detalle = $this->ordenSeleccionada->detalles->find($detalleId);
            if($detalle) {
                // Usamos array_filter para no guardar strings vacíos como 'null' si el campo es nullable
                $detalle->update(array_filter([
                    'resultados' => $campos['resultados'] ?? null,
                    'valores_referencia' => $campos['valores_referencia'] ?? null,
                    'metodologia' => $campos['metodologia'] ?? null,
                ]));
            }
        }
        // --- FIN DE MODIFICACIÓN ---

        // 2. Refrescar la orden con los datos recién guardados
        $this->ordenSeleccionada->refresh();
        $this->ordenSeleccionada->resultados_texto = $this->resultados_texto;
        $this->ordenSeleccionada->estado = $this->estadoOrden;

        // 3. ¡LA MAGIA! Generar y guardar el PDF
        $pdfNombre = 'orden-lab-' . $this->ordenSeleccionada->id . '-' . time() . '.pdf';
        $rutaPdf = 'resultados_lab' . DIRECTORY_SEPARATOR . $pdfNombre;

        // Pasamos los datos del formulario y la orden a la plantilla Blade
        $datosParaPdf = [
            'orden' => $this->ordenSeleccionada,
            // 'resultadosDetalleArray' => $this->detallesForm, // El PDF ya lee desde $orden->detalles
            'conclusion' => $this->resultados_texto,
        ];

        // Generamos el PDF
        $pdf = Pdf::loadView('pdf.orden-laboratorio', $datosParaPdf);
        
        Storage::disk('public')->put($rutaPdf, $pdf->output());

        // 4. Actualizar la Orden principal con el estado y la ruta del PDF
        $this->ordenSeleccionada->resultado_pdf_path = 'resultados_lab/' . $pdfNombre;
        $this->ordenSeleccionada->save();

        session()->flash('message', '¡Orden de laboratorio #' . $this->ordenSeleccionada->id . ' actualizada y PDF generado!');
        $this->closeModal();
        $this->dispatch('$refresh');
    }

    /**
     * Permite descargar el PDF generado.
     */
    public function descargarResultado($ordenId)
    {
        $orden = OrdenLaboratorio::findOrFail($ordenId);

        if ($orden->resultado_pdf_path && Storage::disk('public')->exists($orden->resultado_pdf_path)) {
            return Storage::disk('public')->download($orden->resultado_pdf_path);
        }

        session()->flash('error', 'No se encontró el archivo PDF para esta orden.');
    }
}