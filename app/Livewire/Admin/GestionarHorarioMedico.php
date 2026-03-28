<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Medico;
use App\Models\MedicoHorario;
use Illuminate\Validation\Rule;

class GestionarHorarioMedico extends Component
{
    public Medico $medico;

    // form
    public $dia_semana;
    public $hora_inicio_hr;
    public $hora_inicio_min; 
    public $hora_inicio_ampm = 'AM';
    public $hora_fin_hr;
    public $hora_fin_min; 
    public $hora_fin_ampm = 'AM';

    public $editingHorarioId = null;
    public $formTitle = 'Añadir Nuevo Horario';

    protected function rules()
    {
        return [
            'dia_semana' => ['required', 'integer', 'between:1,7'],
            
            'hora_inicio_hr' => ['required', 'integer', 'min:1', 'max:12'],
            'hora_inicio_ampm' => ['required', 'in:AM,PM'],
            
            'hora_fin_hr' => ['required', 'integer', 'min:1', 'max:12'],
            'hora_fin_ampm' => ['required', 'in:AM,PM'],
            
            'hora_inicio_min' => ['nullable', 'integer', 'min:0', 'max:59'],
            'hora_fin_min' => ['nullable', 'integer', 'min:0', 'max:59'],
        ];
    }

    protected $messages = [
        'dia_semana.required' => 'El día es obligatorio.',
        
        'hora_inicio_hr.required' => 'La hora es obligatoria.',
        'hora_inicio_hr.min' => 'La hora debe ser entre 1 y 12.',
        'hora_inicio_hr.max' => 'La hora debe ser entre 1 y 12.',
        
        'hora_inicio_min.integer' => 'Minutos inválidos.',
        'hora_inicio_min.min' => 'Minutos inválidos.',
        'hora_inicio_min.max' => 'Minutos inválidos.',

        'hora_fin_min.integer' => 'Minutos inválidos.',
        'hora_fin_min.min' => 'Minutos inválidos.',
        'hora_fin_min.max' => 'Minutos inválidos.',
        
        'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.', 
    ];
    

    public function mount(Medico $medico)
    {
        $this->medico = $medico;
    }

    // edit
    public function editHorario($horarioId)
    {
        $horario = MedicoHorario::findOrFail($horarioId);
        
        $this->editingHorarioId = $horario->id;
        $this->dia_semana = $horario->dia_semana;
        
        $inicio = \Carbon\Carbon::parse($horario->hora_inicio);
        $this->hora_inicio_hr = $inicio->format('h');
        $this->hora_inicio_min = $inicio->format('i');
        $this->hora_inicio_ampm = $inicio->format('A');
        
        $fin = \Carbon\Carbon::parse($horario->hora_fin);
        $this->hora_fin_hr = $fin->format('h');
        $this->hora_fin_min = $fin->format('i');
        $this->hora_fin_ampm = $fin->format('A');
        
        $this->formTitle = 'Editar Horario';
        $this->resetErrorBag();
    }

    public function cancelEdit()
    {
        $this->reset([
            'dia_semana', 
            'hora_inicio_hr', 'hora_inicio_min',
            'hora_fin_hr', 'hora_fin_min',
            'editingHorarioId'
        ]);
        $this->hora_inicio_ampm = 'AM'; 
        $this->hora_fin_ampm = 'AM';
        
        $this->formTitle = 'Añadir Nuevo Horario';
        $this->resetErrorBag();
    }

    private function buildTimeFromParts($h, $m, $ampm)
    {
        $m = $m ?? 0;
        
        $m = str_pad($m, 2, '0', STR_PAD_LEFT);
        $timeString = "{$h}:{$m} {$ampm}";
        
        return \Carbon\Carbon::parse($timeString)->format('H:i');
    }

    public function save()
    {
        if ($this->hora_inicio_min === '' || $this->hora_inicio_min === null) {
            $this->hora_inicio_min = null;
        } else {
            $this->hora_inicio_min = (int) $this->hora_inicio_min;
        }
        
        if ($this->hora_fin_min === '' || $this->hora_fin_min === null) {
            $this->hora_fin_min = null;
        } else {
            $this->hora_fin_min = (int) $this->hora_fin_min;
        }

        $this->validate();
        
        try {
            $hora_inicio_24h = $this->buildTimeFromParts($this->hora_inicio_hr, $this->hora_inicio_min, $this->hora_inicio_ampm);
            $hora_fin_24h = $this->buildTimeFromParts($this->hora_fin_hr, $this->hora_fin_min, $this->hora_fin_ampm);
        } catch (\Exception $e) {
            $this->addError('hora_inicio_hr', 'La fecha o hora es inválida.');
            return;
        }

        if ($hora_inicio_24h >= $hora_fin_24h) {
            $this->addError('hora_fin_hr', 'La hora de fin debe ser posterior a la hora de inicio.');
            return;
        }

        $overlapQuery = $this->medico->horarios()
            ->where('dia_semana', $this->dia_semana)
            ->where('hora_inicio', '<', $hora_fin_24h) 
            ->where('hora_fin', '>', $hora_inicio_24h);

        if ($this->editingHorarioId) {
            $overlapQuery->where('id', '!=', $this->editingHorarioId);
        }

        if ($overlapQuery->exists()) {
            $this->addError('hora_inicio_hr', 'Este horario se superpone con un horario ya existente.');
            return;
        }

        $data = [
            'dia_semana' => $this->dia_semana,
            'hora_inicio' => $hora_inicio_24h,
            'hora_fin' => $hora_fin_24h,
        ];
        
        if ($this->editingHorarioId) {
            $horario = MedicoHorario::findOrFail($this->editingHorarioId);
            $horario->update($data);
            session()->flash('horario_message', 'Horario actualizado con éxito.');
        } else {
            $this->medico->horarios()->create($data);
            session()->flash('horario_message', 'Horario añadido con éxito.');
        }

        $this->cancelEdit();
    }

    public function deleteHorario($horarioId)
    {
        $horario = $this->medico->horarios()->find($horarioId);
        
        if ($horario) {
            $horario->delete();
            session()->flash('horario_message', 'Horario eliminado.');
        }

        if ($horarioId == $this->editingHorarioId) {
            $this->cancelEdit();
        }
    }

    public function getHorariosProperty()
    {
        return $this->medico->horarios()
                    ->orderBy('dia_semana')
                    ->orderBy('hora_inicio')
                    ->get();
    }

    public function render()
    {
        $dias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        return view('livewire.admin.gestionar-horario-medico', [
            'horarios' => $this->horarios,
            'dias' => $dias,
        ]);
    }
}