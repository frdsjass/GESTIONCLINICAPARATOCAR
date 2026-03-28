<?php

namespace App\Livewire\Paciente;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Paciente;
use App\Models\Cita;
use Carbon\Carbon; // ¡Importante para las fechas!

// --- ¡AÑADIDOS FASE O! ---
use App\Models\OrdenLaboratorio;
use Illuminate\Support\Facades\Storage;

class Dashboard extends Component
{
    // --- ACCIÓN PARA CANCELAR CITA (CON RESTRICCIÓN DE TIEMPO) ---
    public function solicitarCancelacion($citaId)
    {
        $user = Auth::user();
        // Aseguramos que el paciente exista
        if (!$user || !$user->paciente) {
             return;
        }
        $paciente = $user->paciente;

        // Buscamos la cita asegurándonos de que pertenece a ESTE paciente
        $cita = $paciente->citas()->find($citaId);

        // Validamos que la cita exista y sea futura
        if ($cita && $cita->fecha_hora_inicio > now()) {
            
            // --- RESTRICCIÓN DE 24 HORAS (ACTIVA) ---
            // Calculamos la diferencia en horas entre ahora y la hora de la cita
            $horasRestantes = now()->diffInHours($cita->fecha_hora_inicio, false);

            // Si faltan menos de 24 horas, NO dejamos cancelar
            if ($horasRestantes < 24) {
                session()->flash('error', 'Lo sentimos, solo se pueden cancelar citas con al menos 24 horas de anticipación.');
                return; // Detenemos la ejecución aquí
            }
            // ----------------------------------------

            $cita->estado = 'Cancelada';
            $cita->save();

            session()->flash('message', 'Cita cancelada exitosamente.');
        } else {
            session()->flash('error', 'No se pudo cancelar la cita. Verifica que sea una cita futura.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $paciente = $user->paciente; 

        $citasFuturas = collect();
        $citasPasadas = collect();
        $pacienteNombre = $user->name;

        if ($paciente) {
            $pacienteNombre = $paciente->nombre_completo;
            
            // 1. Citas futuras (de ahora en adelante)
            $citasFuturas = $paciente->citas()
                ->with(['medico.user', 'medico.especialidad'])
                // Usamos 'fecha_hora_inicio' y 'now()' para mayor precisión
                ->where('fecha_hora_inicio', '>=', now()) 
                ->orderBy('fecha_hora_inicio', 'asc')
                ->get();

            // 2. Citas pasadas (anteriores a ahora)
            // --- ¡MODIFICADO FASE O! ---
            $citasPasadas = $paciente->citas()
                ->with([
                    'medico.user', 
                    'medico.especialidad',
                    'ordenLaboratorio' // <-- ¡Añadido para cargar el PDF!
                ])
                ->where('fecha_hora_inicio', '<', now())
                ->orderBy('fecha_hora_inicio', 'desc')
                ->take(10) // Limitamos a las últimas 10
                ->get();
        }

        return view('livewire.paciente.dashboard', [
            'pacienteNombre' => $pacienteNombre,
            'citasFuturas' => $citasFuturas,
            'citasPasadas' => $citasPasadas,
        ])->layout('layouts.app');
    }

    // --- ¡NUEVA FUNCIÓN - FASE O! ---
    /**
     * Permite al paciente descargar su propio resultado de laboratorio.
     */
    public function descargarResultado($ordenId)
    {
        $paciente = Auth::user()->paciente;
        if (!$paciente) {
            session()->flash('error', 'No se pudo encontrar tu perfil de paciente.');
            return;
        }

        $orden = OrdenLaboratorio::find($ordenId);

        // --- Verificación de Seguridad CRÍTICA ---
        // 1. ¿La orden existe?
        // 2. ¿La orden pertenece AL PACIENTE que está logueado?
        if (!$orden || $orden->paciente_id != $paciente->id) {
            session()->flash('error', 'No se encontró el resultado o no tiene permiso para verlo.');
            return;
        }

        // Verificación de Archivo: ¿El PDF existe?
        if ($orden->resultado_pdf_path && Storage::disk('public')->exists($orden->resultado_pdf_path)) {
            return Storage::disk('public')->download($orden->resultado_pdf_path);
        }

        session()->flash('error', 'El archivo PDF para esta orden no está disponible o aún no se ha generado.');
    }
}