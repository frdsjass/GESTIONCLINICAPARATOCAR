<?php

namespace App\Livewire\Paciente;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\MedicoHorario;
use App\Models\Cita;
use Carbon\Carbon;

class AgendarCita extends Component
{
    // --- PROPIEDADES ---
    public $especialidad_id = '';
    public $medico_id = '';
    public $fecha = '';
    public $slots_disponibles = [];
    public $hora_seleccionada = null;
    public $selectedMedico = null;

    // --- LISTENERS (HOOKS) ---

    public function updatedEspecialidadId()
    {
        $this->reset(['medico_id', 'fecha', 'slots_disponibles', 'hora_seleccionada', 'selectedMedico']);
    }

    public function updatedMedicoId($medicoId)
    {
        $this->reset(['fecha', 'slots_disponibles', 'hora_seleccionada']);

        if ($medicoId) {
             $this->selectedMedico = Medico::with(['horarios', 'user'])->find($medicoId);
        } else {
             $this->selectedMedico = null;
        }
    }

    public function updatedFecha()
    {
        $this->generarSlots();
    }

    // --- MÉTODOS DE LÓGICA ---

    public function generarSlots()
    {
        $this->reset(['slots_disponibles', 'hora_seleccionada']);

        if (!$this->medico_id || !$this->fecha) {
            return;
        }

        // 1. Parsear la fecha
        try {
            $date = Carbon::parse($this->fecha);
        } catch (\Exception $e) {
            return; 
        }

        // 2. Obtener el NÚMERO del día (1 = Lunes, ..., 7 = Domingo)
        // Esto coincide con tu base de datos (tinyint)
        $diaSemanaIso = $date->dayOfWeekIso; 

        // 3. Buscar Horarios usando el NÚMERO, no el nombre
        // ¡AQUÍ ESTABA EL ERROR! Usábamos texto ('Lunes') vs número (1)
        $horariosLaborales = MedicoHorario::where('medico_id', $this->medico_id)
             ->where('dia_semana', $diaSemanaIso) // <-- Corrección clave
             ->orderBy('hora_inicio')
             ->get();

        if ($horariosLaborales->isEmpty()) {
            return; // El médico no trabaja este día
        }

        // 4. Obtener Citas Ocupadas
        $citasExistentes = Cita::where('medico_id', $this->medico_id)
            ->whereDate('fecha_hora_inicio', $date->format('Y-m-d'))
            ->whereIn('estado', ['Programada', 'Confirmada'])
            ->get()
            ->map(function($cita) {
                return $cita->fecha_hora_inicio->format('H:i');
            })->toArray();

        // 5. Generar Slots
        $ahora = Carbon::now(); 

        foreach ($horariosLaborales as $horario) {
            $horaInicio = Carbon::parse($horario->hora_inicio);
            $horaFin = Carbon::parse($horario->hora_fin);
            
            $slot = Carbon::parse($date->format('Y-m-d') . ' ' . $horaInicio->format('H:i:s'));
            $finTurno = Carbon::parse($date->format('Y-m-d') . ' ' . $horaFin->format('H:i:s'));

            while ($slot < $finTurno) {
                $horaString = $slot->format('H:i'); 

                // REGLA 1: ¿Está ocupado?
                if (!in_array($horaString, $citasExistentes)) {
                    
                    // REGLA 2: Filtrado por tiempo
                    if ($date->isSameDay($ahora)) {
                         // Si es HOY, debe ser futuro (+10 min margen)
                         if ($slot->gt($ahora->copy()->addMinutes(10))) {
                             $this->slots_disponibles[] = $slot->format('h:i A');
                         }
                    } elseif ($date->gt($ahora)) {
                         // Si es FUTURO, todo libre
                         $this->slots_disponibles[] = $slot->format('h:i A');
                    }
                }

                $slot->addMinutes(30);
            }
        }
    }

    public function seleccionarHora($hora)
    {
        $this->hora_seleccionada = $hora;
    }

    public function confirmarCita()
    {
        $this->validate([
            'medico_id' => 'required|exists:medicos,id',
            'fecha' => 'required|date',
            'hora_seleccionada' => 'required',
        ], [
            'hora_seleccionada.required' => 'Debes seleccionar un horario.'
        ]);

        $user = Auth::user();
        if (!$user->paciente) {
            session()->flash('error', 'Error: Perfil de paciente no encontrado.');
            return;
        }
        $paciente = $user->paciente;

        try {
            $date = Carbon::parse($this->fecha);
        } catch (\Exception $e) {
            session()->flash('error', 'Formato de fecha inválido.');
            return;
        }

        $horaCarbon = Carbon::parse($this->hora_seleccionada);
        $fechaHoraInicio = Carbon::parse($date->format('Y-m-d') . ' ' . $horaCarbon->format('H:i:s'));
        $fechaHoraFin = $fechaHoraInicio->copy()->addMinutes(30);

        Cita::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $this->medico_id,
            'fecha_hora_inicio' => $fechaHoraInicio,
            'fecha_hora_fin' => $fechaHoraFin,
            'motivo_consulta' => 'Cita agendada desde portal web',
            'estado' => 'Programada',
        ]);

        session()->flash('message', '¡Cita reservada con éxito!');
        return redirect()->route('paciente.dashboard');
    }

    public function render()
    {
        $especialidades = Especialidad::where('estado', 'Activo')->orderBy('nombre')->get();
        $medicos = [];

        if (!empty($this->especialidad_id)) {
             $medicos = Medico::with('user')
                ->where('especialidad_id', $this->especialidad_id)
                ->where('estado', 'Activo')
                ->get();
        }

        return view('livewire.paciente.agendar-cita', [
            'especialidades' => $especialidades,
            'medicos' => $medicos,
        ])->layout('layouts.app');
    }
}