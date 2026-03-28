<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Paciente;
use App\Models\HistoriaClinica;

class MisPacientes extends Component
{
    use WithPagination;

    public $search = '';
    protected $medico;

    public function mount()
    {
        // Obtiene el modelo Medico basado en el User ID autenticado
        $this->medico = Auth::user()->medico; 
        
        if (!$this->medico) {
            // Manejar el caso de que el usuario no sea un médico
            // (aunque la ruta ya debería proteger esto)
            abort(403, 'Acceso no autorizado.');
        }
    }

    public function render()
    {
        // 1. Obtener los IDs de pacientes únicos que este médico ha atendido
        $pacienteIds = HistoriaClinica::where('medico_id', $this->medico->id)
                                      ->distinct()
                                      ->pluck('paciente_id');

        // 2. Obtener los modelos de esos pacientes
        $query = Paciente::whereIn('id', $pacienteIds);

        // 3. Aplicar la búsqueda si hay algo en $search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre_completo', 'like', '%' . $this->search . '%')
                  ->orWhere('carnet_identidad', 'like', '%' . $this->search . '%');
            });
        }

        // 4. Paginar los resultados
        $pacientes = $query->paginate(10); // 10 pacientes por página

        return view('livewire.doctor.mis-pacientes', [
            'pacientes' => $pacientes,
        ])->layout('layouts.app'); // Usa la plantilla de Jetstream
    }
}