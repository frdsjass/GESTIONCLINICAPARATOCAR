<?php

namespace App\Livewire\Doctor;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DoctorDashboard extends Component
{
    // --- ¡MODIFICADO! Propiedades separadas
    public $citasPendientes;
    public $citasCompletadas;

    public function mount()
    {
        // mount() se ejecuta una vez, cuando el componente se carga por primera vez.
        $this->cargarCitas();
    }

    // --- ¡NUEVA FUNCIÓN! ---
    /**
     * Carga/Recarga todas las citas del día en sus respectivas colecciones.
     */
    public function cargarCitas()
    {
        $medico = Auth::user()->medico; 

        if (!$medico) {
            $this->citasPendientes = collect();
            $this->citasCompletadas = collect();
            return;
        }

        // ¡OPTIMIZADO! Usamos with('paciente') para evitar N+1 queries
        $citasQuery = $medico->citas()
                            ->with('paciente') 
                            ->whereDate('fecha_hora_inicio', today());

        // 1. Citas PENDIENTES (La "Lista de Tareas")
        $this->citasPendientes = (clone $citasQuery)
            ->whereIn('estado', ['Programada', 'Confirmada'])
            ->orderBy('fecha_hora_inicio', 'asc')
            ->get();

        // 2. Citas FINALIZADAS (El "Historial")
        $this->citasCompletadas = (clone $citasQuery)
            ->whereIn('estado', ['Completada', 'Cancelada', 'No Asistió'])
            ->orderBy('fecha_hora_inicio', 'desc') // Las más recientes primero
            ->get();
    }

    public function render()
    {
        return view('livewire.doctor.doctor-dashboard')
            ->layout('layouts.app');
    }
}