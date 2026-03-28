<?php

namespace App\Livewire\Appointments;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon; 

class AppointmentsManager extends Component
{
    use WithPagination;

    public $paciente_id, $medico_id, $motivo_consulta, $estado;
    public $fecha, $hora; 

    public $intervalos = []; 

    public $cita_id;
    public $isOpen = false;
    public $search = '';

    public function updatedMedicoId($value)
    {
        $this->reset('fecha', 'hora', 'intervalos');
    }

    public function updatedFecha($value)
    {
        $this->generarIntervalos();
    }

    public function render()
    {
        $citas = Cita::with('paciente', 'medico.user')
            ->when($this->search, function ($query) {
                $query->whereHas('paciente', function ($q) {
                    $q->where('nombre_completo', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('medico.user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);
            
        $medicos = Medico::where('estado', 'Activo')
                            ->with('user', 'especialidad')
                            ->get()
                            ->sortBy('user.name');
                            
        $pacientes = Paciente::where('estado', 'Activo')
                                ->orderBy('nombre_completo')
                                ->get();

        return view('livewire.appointments.appointments-manager', compact('citas', 'pacientes', 'medicos'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit($id)
    {
        $cita = Cita::findOrFail($id);
        $this->cita_id = $id;
        $this->paciente_id = $cita->paciente_id;
        $this->medico_id = $cita->medico_id;
        
        $fechaCarbon = Carbon::parse($cita->fecha_hora_inicio);
        $this->fecha = $fechaCarbon->format('Y-m-d');
        $this_hora_cita = $fechaCarbon->format('H:i');

        $this->generarIntervalos();

        if (!in_array($this_hora_cita, $this->intervalos)) {
            $this->intervalos[] = $this_hora_cita;
            sort($this->intervalos); 
        }

        $this->hora = $this_hora_cita; 
        
        $this->motivo_consulta = $cita->motivo_consulta;
        $this->estado = $cita->estado;

        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:medicos,id',
            'fecha' => 'required|date', 
            'hora' => 'required', 
            'motivo_consulta' => 'required|string|max:255',
            'estado' => 'required|in:Programada,Confirmada,Completada,Cancelada,No Asistió',
        ]);

        $fechaHoraInicio = Carbon::parse($this->fecha . ' ' . $this->hora);

        $medico = Medico::find($this->medico_id);
        $limite = $medico->limite_citas_dia;
        $fechaCita = Carbon::parse($this->fecha)->format('Y-m-d'); 

        $conteoCitas = Cita::where('medico_id', $this->medico_id)
                            ->whereDate('fecha_hora_inicio', $fechaCita)
                            ->where('estado', '!=', 'Cancelada')
                            ->count();
                            
        if (!$this->cita_id && $conteoCitas >= $limite) {
            session()->flash('error', 'El Dr. ' . $medico->user->name . ' ya ha alcanzado su límite de ' . $limite . ' citas para este día.');
            return; 
        }
        
        $fechaFin = $fechaHoraInicio->copy()->addMinutes(30);

        Cita::updateOrCreate(['id' => $this->cita_id], [
            'paciente_id' => $this->paciente_id,
            'medico_id' => $this->medico_id,
            'fecha_hora_inicio' => $fechaHoraInicio, 
            'fecha_hora_fin' => $fechaFin,
            'motivo_consulta' => $this->motivo_consulta,
            'estado' => $this->estado,
        ]);

        session()->flash('message', 
            $this->cita_id ? '¡Cita actualizada exitosamente!' : '¡Cita agendada exitosamente!');

        $this->closeModal();
        $this->resetInputFields();
    }
    
    private function generarIntervalos()
    {
        $this->reset('hora', 'intervalos');

        if (!$this->medico_id || !$this->fecha) {
            return;
        }

        $fechaCarbon = Carbon::parse($this->fecha);
        $diaSemana = $fechaCarbon->dayOfWeek; 
        $duracionCita = 30; 

        $medico = Medico::find($this->medico_id);

        $horarios = $medico->horarios()->where('dia_semana', $diaSemana)->get();

        $citasOcupadas = $medico->citas()
            ->whereDate('fecha_hora_inicio', $fechaCarbon)
            ->where('estado', '!=', 'Cancelada')
            ->get()
            ->map(function ($cita) {
                return Carbon::parse($cita->fecha_hora_inicio)->format('H:i');
            })
            ->flip(); 

        $intervalosDisponibles = [];

        foreach ($horarios as $horario) {
            $horaInicio = Carbon::parse($horario->hora_inicio);
            $horaFin = Carbon::parse($horario->hora_fin);

            while ($horaInicio < $horaFin) {
                $horaActualStr = $horaInicio->format('H:i');

                if (!$citasOcupadas->has($horaActualStr)) {
                    if (!$this->cita_id && $fechaCarbon->isToday() && $horaInicio->isPast()) {
                    } else {
                         $intervalosDisponibles[] = $horaActualStr;
                    }
                }
                
                $horaInicio->addMinutes($duracionCita);
            }
        }

        $this->intervalos = $intervalosDisponibles;
    }

    public function openModal() { $this->isOpen = true; }
    public function closeModal() { $this->isOpen = false; }
    
    private function resetInputFields()
    {
        $this->reset('paciente_id', 'medico_id', 'motivo_consulta', 'estado', 'cita_id', 'fecha', 'hora', 'intervalos');
    }
}