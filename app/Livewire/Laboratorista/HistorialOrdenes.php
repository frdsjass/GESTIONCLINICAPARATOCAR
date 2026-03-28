<?php

namespace App\Livewire\Laboratorista;

use Livewire\Component;
use App\Models\OrdenLaboratorio;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage; // <-- Importante para la descarga

class HistorialOrdenes extends Component
{
    use WithPagination;

    public $filtroEstado = 'Todas';
    public $search = '';

    protected $queryString = [
        'filtroEstado' => ['except' => 'Todas'],
        'search' => ['except' => ''],
    ];

    public function render()
    {
        $query = OrdenLaboratorio::query()
            ->where('estado', '!=', 'Pendiente'); 

        // Filtrar por estado (si no es 'Todas')
        $query->when($this->filtroEstado !== 'Todas', function ($q) {
            return $q->where('estado', $this->filtroEstado);
        });

        // Filtrar por búsqueda (paciente o médico)
        $query->when($this->search, function ($q) {
            $q->whereHas('paciente', function ($pac) {
                $pac->where('nombre_completo', 'like', '%' . $this->search . '%');
            })->orWhereHas('medico.user', function ($medUser) {
                $medUser->where('name', 'like', '%' . $this->search . '%');
            });
        });

        $ordenes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.laboratorista.historial-ordenes', [
            'ordenes' => $ordenes
        ])->layout('layouts.app');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    // --- ¡NUEVA FUNCIÓN PARA DESCARGAR EL PDF! ---
    public function descargarResultado($ordenId)
    {
        // Buscamos la orden
        $orden = OrdenLaboratorio::find($ordenId);

        // Verificamos si existe
        if (!$orden) {
            session()->flash('error', 'No se encontró la orden solicitada.');
            return;
        }

        // Verificamos si tiene un PDF generado y si el archivo existe físicamente
        if ($orden->resultado_pdf_path && Storage::disk('public')->exists($orden->resultado_pdf_path)) {
            // Descargamos el archivo
            return Storage::disk('public')->download($orden->resultado_pdf_path);
        }

        // Si no existe el archivo o la ruta, mostramos error
        session()->flash('error', 'El archivo PDF para la orden #' . $ordenId . ' no está disponible o no se ha generado.');
    }
}