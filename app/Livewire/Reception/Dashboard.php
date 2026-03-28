<?php

namespace App\Livewire\Reception;

use Livewire\Component;
use App\Models\Cita;     
use App\Models\Paciente; 
use Carbon\Carbon;     

class Dashboard extends Component
{
    // --- FUNCIÓN DE CHECK-IN CORREGIDA ---
    public function hacerCheckIn($citaId)
    {
        $cita = Cita::find($citaId);

        if ($cita) {
            // Se cambia el estado en la BD
            $cita->estado = 'Confirmada'; 
            $cita->save();
        }
        
        // --- ¡ARREGLO DE RENDERING! ---
        // Forzamos la re-ejecución del método render() para refrescar las listas y K-Cards.
        return $this->render();
    }
    
    public function render()
    {
        $today = Carbon::today();

        // Las consultas son correctas y usan 'Programada' y 'Confirmada'
        $citasTotalesHoy = Cita::whereDate('fecha_hora_inicio', $today)->count();
        $pacientesEnEsperaCount = Cita::whereDate('fecha_hora_inicio', $today)
                                      ->where('estado', 'Confirmada') 
                                      ->count();
        $citasCompletadasHoy = Cita::whereDate('fecha_hora_inicio', $today)
                                    ->where('estado', 'Completada')
                                    ->count();
        $pacientesNuevosHoy = Paciente::whereDate('created_at', $today)->count();


        $citasHoy = Cita::with(['paciente', 'medico.user'])
            ->whereDate('fecha_hora_inicio', $today)
            ->whereIn('estado', ['Programada']) 
            ->orderBy('fecha_hora_inicio', 'asc')
            ->get();

        $pacientesEnEspera = Cita::with(['paciente', 'medico.user'])
            ->whereDate('fecha_hora_inicio', $today)
            ->where('estado', 'Confirmada') 
            ->orderBy('fecha_hora_inicio', 'asc')
            ->get();


        return view('livewire.reception.dashboard', [
            'citasHoy' => $citasHoy,
            'pacientesEnEspera' => $pacientesEnEspera,
            'citasTotalesHoy' => $citasTotalesHoy,
            'pacientesEnEsperaCount' => $pacientesEnEsperaCount,
            'citasCompletadasHoy' => $citasCompletadasHoy,
            'pacientesNuevosHoy' => $pacientesNuevosHoy,
        ])->layout('layouts.app');
    }
}