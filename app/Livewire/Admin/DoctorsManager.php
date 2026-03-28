<?php

namespace App\Livewire\Admin;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class DoctorsManager extends Component
{
    use WithPagination;

    // form
    public $name, $email, $password;
    public $especialidad_id, $cedula_profesional, $telefono, $direccion;
    
    public $estado; 

    public $limite_citas_dia;

    // filtrar
    public $showInactive = false;

    public $medico_id, $user_id;
    public $isOpen = false;
    public $search = '';

    protected $listeners = ['render' => 'render'];

    public function render()
    {
        $medicosQuery = Medico::with('user', 'especialidad')
            ->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('cedula_profesional', 'like', '%' . $this->search . '%');
            });

        // ocultar archivs
        $medicosQuery->when(!$this->showInactive, function ($query) {
            return $query->where('estado', 'Activo');
        });

        $medicos = $medicosQuery->latest()->paginate(10);
            
        $especialidades = Especialidad::where('estado', 'Activo')->get();

        return view('livewire.admin.doctors-manager', compact('medicos', 'especialidades'))
            ->layout('layouts.app');
    }

    public function toggleEstado($id)
    {
        $medico = Medico::find($id);
        if ($medico) {
            $medico->estado = ($medico->estado == 'Activo') ? 'Inactivo' : 'Activo';
            $medico->save();
            session()->flash('message', 'Estado del médico actualizado.');
            $this->dispatch('render'); 
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit($id)
    {
        $medico = Medico::findOrFail($id);
        $this->medico_id = $id;
        $this->user_id = $medico->user_id;

        $this->name = $medico->user->name;
        $this->email = $medico->user->email;
        $this->especialidad_id = $medico->especialidad_id;
        $this->cedula_profesional = $medico->cedula_profesional;
        $this->telefono = $medico->telefono;
        $this->direccion = $medico->direccion;
        $this->estado = $medico->estado; 
        $this->limite_citas_dia = $medico->limite_citas_dia; 
        
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user_id,
            'password' => $this->user_id ? 'nullable|min:8' : 'required|min:8',
            'especialidad_id' => 'required|exists:especialidades,id',
            'cedula_profesional' => 'required|string|max:255|unique:medicos,cedula_profesional,' . $this->medico_id,
            'telefono' => 'nullable|string|max:25',
            'estado' => 'required|in:Activo,Inactivo', 
            'limite_citas_dia' => 'required|integer|min:1|max:100', 
        ]);
        
        DB::transaction(function () {
            $userData = [ 'name' => $this->name, 'email' => $this->email ];
            if (!empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            }
            $user = User::updateOrCreate(['id' => $this->user_id], $userData);
            
            if (!$this->user_id) { $user->assignRole('Medico'); }

            Medico::updateOrCreate(['id' => $this->medico_id], [
                'user_id' => $user->id,
                'especialidad_id' => $this->especialidad_id,
                'cedula_profesional' => $this->cedula_profesional,
                'telefono' => $this->telefono,
                'direccion' => $this->direccion,
                'estado' => $this->estado,
                'limite_citas_dia' => $this->limite_citas_dia, 
            ]);
        });
        
        session()->flash('message', $this->medico_id ? '¡Médico actualizado!' : '¡Médico creado exitosamente!');
        $this->closeModal();
        $this->resetInputFields();
    }
    
    public function openModal() { $this->isOpen = true; }
    public function closeModal() { $this->isOpen = false; }
    
    private function resetInputFields()
    {
        $this->reset([
            'name', 'email', 'password', 'especialidad_id', 'cedula_profesional',
            'telefono', 'direccion', 'medico_id', 'user_id',
            'estado',
            'limite_citas_dia' 
        ]);
        $this->estado = null;
        $this->limite_citas_dia = 20; 
    }
}