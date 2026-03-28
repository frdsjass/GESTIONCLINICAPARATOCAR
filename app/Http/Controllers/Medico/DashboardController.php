<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\HistoriaClinica;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $medico = Auth::user()->medico;
        $now = Carbon::now();
        $today = Carbon::today();
        $endOfWeek = Carbon::today()->endOfWeek();

        // --- 1. DATOS PARA LAS TARJETAS (NUEVO) ---
        
        // Citas programadas para hoy
        $citasHoyCount = Cita::where('medico_id', $medico->id)
                            ->where('estado', 'Programada')
                            ->whereDate('fecha_hora_inicio', $today)
                            ->count();
                            
        // Citas programadas para los próximos 7 días
        $citasSemanaCount = Cita::where('medico_id', $medico->id)
                                ->where('estado', 'Programada')
                                ->whereBetween('fecha_hora_inicio', [$now, $endOfWeek])
                                ->count();

        // Pacientes ya atendidos hoy (citas completadas)
        $pacientesAtendidosHoyCount = Cita::where('medico_id', $medico->id)
                                        ->where('estado', 'Completada')
                                        ->whereDate('fecha_hora_inicio', $today)
                                        ->count();
        
        // <-- AÑADIDO: El contador que te faltaba -->
        // Pacientes en sala de espera (Check-in realizado)
        $pacientesEnEsperaCount = Cita::where('medico_id', $medico->id)
                                      ->where('estado', 'Confirmada')
                                      ->whereDate('fecha_hora_inicio', $today)
                                      ->count();


        // --- 2. DATOS PARA LOS PANELES ---

        // Panel Izquierdo: Citas del día (Esto ya lo tenías)
        $citasDelDia = Cita::where('medico_id', $medico->id)
            ->whereDate('fecha_hora_inicio', $today)
            // <-- CORRECCIÓN: Añadido 'Confirmada' al array -->
            ->whereIn('estado', ['Programada', 'Confirmada', 'Completada']) 
            ->with('paciente')
            ->orderBy('fecha_hora_inicio', 'asc')
            ->get();
            
        // Panel Derecho: Pacientes atendidos recientemente (NUEVO)
        $pacientesRecientes = HistoriaClinica::where('medico_id', $medico->id)
                                ->with('paciente')
                                ->orderBy('created_at', 'desc')
                                ->distinct('paciente_id') // Obtener solo la última entrada por paciente
                                ->take(5) // Tomar los últimos 5
                                ->get();


        return view('medico.dashboard', [
            'citasDelDia' => $citasDelDia,
            'citasHoyCount' => $citasHoyCount,
            'citasSemanaCount' => $citasSemanaCount,
            'pacientesAtendidosHoyCount' => $pacientesAtendidosHoyCount,
            'pacientesEnEsperaCount' => $pacientesEnEsperaCount, // <-- AÑADIDO: Pasamos el nuevo contador a la vista
            'pacientesRecientes' => $pacientesRecientes,
        ]);
    }
}