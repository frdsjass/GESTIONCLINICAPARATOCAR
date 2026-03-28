<?php

namespace App\Livewire\Admin;

use App\Models\Medico;
use App\Models\MedicoHorario; // <-- AÑADIDO
use Livewire\Component;
use Carbon\Carbon; // <-- AÑADIDO (Aunque no se usa directamente, es buena práctica)

class DoctorProfile extends Component
{
    public Medico $medico;

    // Propiedades para el formulario de nuevo horario
    public $dia_semana;
    public $hora_inicio;
    public $hora_fin;

    // Reglas de validación
    protected $rules = [
        'dia_semana' => 'required|integer|between:1,7',
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fin' => 'required|date_format:H:i',
    ];

    public function mount(Medico $medico)
    {
        $this->medico = $medico;
    }

    /**
     * Función privada para chequear si un nuevo horario se solapa (choca) con uno existente.
     * Esta es la lógica que soluciona el error de "día ocupado".
     */
    private function checkOverlap($dia, $inicio, $fin)
    {
        // Lógica de solapamiento: (StartA < EndB) AND (EndA > StartB)
        return MedicoHorario::where('medico_id', $this->medico->id)
            ->where('dia_semana', $dia)
            ->where('hora_inicio', '<', $fin) // El nuevo inicio es ANTES de que termine el existente
            ->where('hora_fin', '>', $inicio) // El nuevo fin es DESPUÉS de que empiece el existente
            ->exists();
    }

    /**
     * Guarda un nuevo horario, manejando turnos de madrugada y validando solapamientos.
     */
    public function addHorario()
    {
        $this->validate();

        // 1. Validar que la hora de fin no sea igual a la de inicio
        if ($this->hora_inicio == $this->hora_fin) {
            session()->flash('error-horario', 'La hora de inicio no puede ser igual a la hora de fin.');
            return;
        }

        // 2. Detectar si es un turno de madrugada (ej. 23:00 a 02:00)
        // Esta es la lógica que soluciona el bug de la madrugada
        $isOvernight = $this->hora_fin < $this->hora_inicio;

        // 3. Validar solapamiento para la primera parte (o el turno completo si no es de madrugada)
        $horario1_fin = $isOvernight ? '23:59:59' : $this->hora_fin;
        if ($this->checkOverlap($this->dia_semana, $this->hora_inicio, $horario1_fin)) {
            session()->flash('error-horario', 'El horario ingresado (o su primera parte) se solapa con un horario ya existente.');
            return;
        }

        // 4. Si es de madrugada, validar también el día siguiente
        if ($isOvernight) {
            $nextDay = ($this->dia_semana % 7) + 1; // 1->2, 6->7, 7(Domingo)->1(Lunes)
            
            if ($this->checkOverlap($nextDay, '00:00:00', $this->hora_fin)) {
                session()->flash('error-horario', 'La segunda parte del horario (madrugada) se solapa con un horario existente el día siguiente.');
                return;
            }

            // Guardar los dos tramos del turno
            MedicoHorario::create([
                'medico_id' => $this->medico->id,
                'dia_semana' => $this->dia_semana,
                'hora_inicio' => $this->hora_inicio,
                'hora_fin' => '23:59:59', // Fin del primer día
            ]);

            MedicoHorario::create([
                'medico_id' => $this->medico->id,
                'dia_semana' => $nextDay,
                'hora_inicio' => '00:00:00', // Inicio del segundo día
                'hora_fin' => $this->hora_fin,
            ]);

        } else {
            // No es de madrugada, guardar el turno normal
            MedicoHorario::create([
                'medico_id' => $this->medico->id,
                'dia_semana' => $this->dia_semana,
                'hora_inicio' => $this->hora_inicio,
                'hora_fin' => $this->hora_fin,
            ]);
        }

        session()->flash('message-horario', 'Horario guardado exitosamente.');
        $this->reset(['dia_semana', 'hora_inicio', 'hora_fin']);
    }

    /**
     * Elimina un horario existente.
     */
    public function deleteHorario($horarioId)
    {
        // Usamos try-catch por si el ID no existe
        try {
            MedicoHorario::where('id', $horarioId)
                ->where('medico_id', $this->medico->id) // Seguridad: solo borra horarios de este médico
                ->firstOrFail()
                ->delete();
                
            session()->flash('message-horario', 'Horario eliminado.');
        } catch (\Exception $e) {
            session()->flash('error-horario', 'No se pudo eliminar el horario.');
        }
    }

    // vista
    public function render()
    {
        // Cargar los horarios agrupados por día para la vista
        $horarios = MedicoHorario::where('medico_id', $this->medico->id)
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('dia_semana'); // Agrupa la colección por 'dia_semana'

        return view('livewire.admin.doctor-profile', [
            'horariosPorDia' => $horarios
        ])->layout('layouts.app');
    }
}