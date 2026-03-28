<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use Carbon\Carbon;

class MisCitas extends Component
{
    use WithPagination;

    // Quitamos la propiedad protegida $medico
    public $search = '';
    public $filtro_estado = 'Programada'; // Mostrar 'Programada' por defecto
    public $filtro_fecha = 'proximas'; // Mostrar 'proximas' por defecto

    // Quitamos el método mount()
    // public function mount() ...

    public function render()
    {
        // ¡SOLUCIÓN! Obtenemos el médico aquí, en cada render
        $medico = Auth::user()->medico; 
        
        if (!$medico) {
            // Si por alguna razón no encontramos al médico, no continuamos.
             return view('livewire.doctor.mis-citas', [
                'citas' => collect()->paginate(15),
            ])->layout('layouts.app');
        }

        // Usamos $medico->id (de la variable local) en lugar de $this->medico->id
        $query = Cita::where('medico_id', $medico->id)
                     ->with('paciente'); // Cargar la relación con el paciente

        // 1. Filtro por búsqueda (nombre del paciente)
        if ($this->search) {
            $query->whereHas('paciente', function ($q) {
                $q->where('nombre_completo', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Filtro por estado
        if ($this->filtro_estado != 'todas') {
            $query->where('estado', $this->filtro_estado);
        }

        // 3. Filtro por fecha
        $now = Carbon::now();
        if ($this->filtro_fecha == 'proximas') {
            $query->where('fecha_hora_inicio', '>=', $now)
                  ->orderBy('fecha_hora_inicio', 'asc'); // Próximas, de la más cercana a la más lejana
        } elseif ($this->filtro_fecha == 'pasadas') {
            $query->where('fecha_hora_inicio', '<', $now)
                  ->orderBy('fecha_hora_inicio', 'desc'); // Pasadas, de la más reciente a la más antigua
        } else {
            // 'todas'
            $query->orderBy('fecha_hora_inicio', 'desc');
        }

        $citas = $query->paginate(15); // 15 citas por página

        return view('livewire.doctor.mis-citas', [
            'citas' => $citas,
        ])->layout('layouts.app');
    }
}