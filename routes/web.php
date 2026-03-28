<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Medico\DashboardController;

// livewire
use App\Livewire\Admin\SpecialtiesManager;
use App\Livewire\Admin\DoctorsManager;
use App\Livewire\Admin\PatientsManager;
use App\Livewire\Admin\StaffManager;
use App\Livewire\Admin\LabTestManager;
use App\Livewire\Admin\DoctorProfile;
use App\Livewire\Admin\DashboardHome;

use App\Livewire\Appointments\AppointmentsManager;

use App\Livewire\Pharmacy\Inventory;
use App\Livewire\Pharmacy\PointOfSale;
use App\Livewire\Pharmacy\Dashboard as PharmacyDashboard;

use App\Livewire\Doctor\ManageClinicalRecord;
use App\Livewire\Doctor\AttendAppointment;
use App\Livewire\Doctor\MisPacientes;
use App\Livewire\Doctor\MisCitas;

use App\Livewire\PacienteHistorialClinico;
use App\Livewire\Paciente\MiHistorialClinico;
use App\Livewire\Paciente\Dashboard as PacienteDashboard;
use App\Livewire\Paciente\AgendarCita;

use App\Livewire\Reception\Dashboard as ReceptionDashboard;
use App\Livewire\Laboratorista\Dashboard as LaboratoristaDashboard;
use App\Livewire\Laboratorista\HistorialOrdenes; // ¡AÑADIDO! Para el historial

// publicas
Route::get('/', function () {
    return view('welcome');
});

// protegidas
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', DashboardHome::class)->name('dashboard');

    // del admin
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/specialties', SpecialtiesManager::class)->name('specialties');
        Route::get('/doctors', DoctorsManager::class)->name('doctors');
        Route::get('/medicos/perfil/{medico}', DoctorProfile::class)->name('doctors.profile');
        Route::get('/patients', PatientsManager::class)->name('patients');
        Route::get('/staff', StaffManager::class)->name('staff');
        Route::get('/lab-tests', LabTestManager::class)->name('lab-tests');
        Route::get('/pacientes/{paciente}/historial', PacienteHistorialClinico::class)->name('pacientes.historial');
    });

    // admin y recep
    Route::middleware(['role:Admin|Recepcion'])->group(function () {
        Route::get('/appointments', AppointmentsManager::class)->name('appointments');
    });

    // paciente man
    Route::middleware(['role:Admin|Recepcion'])->group(function () {
        Route::get('/gestionar-pacientes', PatientsManager::class)->name('patients.manage');
    });

    // farmacia
    Route::middleware(['role:Admin|Farmaceutico'])->prefix('pharmacy')->name('pharmacy.')->group(function () {
        Route::get('/inventory', Inventory::class)->name('inventory');
        Route::get('/pos', PointOfSale::class)->name('pos');
        Route::get('/dashboard', PharmacyDashboard::class)->name('dashboard');
    });

    // recepcion
    Route::middleware(['role:Recepcion'])->prefix('reception')->name('reception.')->group(function () {
        Route::get('/dashboard', ReceptionDashboard::class)->name('dashboard');
        // ============ ¡AQUÍ ESTÁ LA CORRECCIÓN! ============
        Route::get('/pacientes/{paciente}/historial', PacienteHistorialClinico::class)->name('pacientes.historial');
        // ============ ANTES DECÍA 'pacientes.histORIAL' ============
    });

    // doctor
    Route::middleware(['role:Medico'])->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pacientes/{paciente}/historial', PacienteHistorialClinico::class)->name('pacientes.historial');
        Route::get('/appointments/{cita}/attend', AttendAppointment::class)->name('appointments.attend');
        Route::get('/mis-pacientes', MisPacientes::class)->name('mis-pacientes');
        Route::get('/mis-citas', MisCitas::class)->name('mis-citas');
    });

    // labo
    Route::middleware(['role:Laboratorista'])->prefix('laboratorio')->name('laboratorio.')->group(function () {
        Route::get('/dashboard', LaboratoristaDashboard::class)->name('dashboard');
        // ¡AÑADIDA LA NUEVA RUTA!
        Route::get('/historial', HistorialOrdenes::class)->name('historial');
    });

    // paciente
    Route::middleware(['role:Paciente'])->prefix('paciente')->name('paciente.')->group(function () {
        Route::get('/dashboard', PacienteDashboard::class)->name('dashboard');
        Route::get('/agendar', AgendarCita::class)->name('agendar');
        Route::get('/historial', MiHistorialClinico::class)->name('historial');
    });
    //
});