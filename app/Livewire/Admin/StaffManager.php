<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Personal;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; 

class StaffManager extends Component
{
    use WithPagination;

    public $name, $email, $password, $role;
    public $carnet_identidad, $telefono, $direccion, $estado;
    
    public $user_id, $personal_id; 

    public $showInactive = false;

    public $isOpen = false;
    public $search = '';

    protected $listeners = ['render' => 'render']; 

    public function render()
    {
        $rolesExcluidos = ['Admin', 'Medico', 'Paciente'];
        $rolesParaGestionar = Role::whereNotIn('name', $rolesExcluidos)->get();
        $nombresRoles = $rolesParaGestionar->pluck('name')->toArray();

        $personalQuery = Personal::with('user', 'user.roles')
            ->whereHas('user.roles', function ($query) use ($nombresRoles) { 
                $query->whereIn('name', $nombresRoles);
            })
            ->where(function ($query) {
                $query->where('carnet_identidad', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->search . '%')
                                 ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });

        $personalQuery->when(!$this->showInactive, function ($query) {
            return $query->where('estado', 'Activo');
        });

        $personal = $personalQuery->latest()->paginate(10);
            
        return view('livewire.admin.staff-manager', [
            'personal' => $personal, 
            'roles' => $rolesParaGestionar 
        ])->layout('layouts.app');
    }

    public function toggleEstado($id)
    {
        $personal = Personal::find($id);
        if ($personal) {
            $personal->estado = ($personal->estado == 'Activo') ? 'Inactivo' : 'Activo';
            $personal->save();
            session()->flash('message', 'Estado del personal actualizado.');
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
        $personal = Personal::with('user')->findOrFail($id);
        
        $this->personal_id = $id;
        $this->user_id = $personal->user_id;

        $this->name = $personal->user->name;
        $this->email = $personal->user->email;
        $this->password = null;
        $this->role = $personal->user->getRoleNames()->first();

        $this->carnet_identidad = $personal->carnet_identidad;
        $this->telefono = $personal->telefono;
        $this->direccion = $personal->direccion;
        $this->estado = $personal->estado;

        $this->openModal();
    }

    public function store()
    {
        $rolesExcluidos = ['Admin', 'Medico', 'Paciente'];
        $rolesPermitidos = Role::whereNotIn('name', $rolesExcluidos)->pluck('name')->toArray();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user_id,
            'password' => $this->user_id ? 'nullable|min:8' : 'required|min:8',
            'role' => ['required', Rule::in($rolesPermitidos)],
            'carnet_identidad' => 'required|string|max:20|unique:personal,carnet_identidad,' . $this->personal_id,
            'telefono' => 'nullable|string|max:25',
            'direccion' => 'nullable|string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ]);
        
        DB::transaction(function () {
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];
            if (!empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            }

            $user = User::updateOrCreate(['id' => $this->user_id], $userData);
            
            $user->syncRoles([$this->role]);

            Personal::updateOrCreate(['id' => $this->personal_id], [
                'user_id' => $user->id,
                'carnet_identidad' => $this->carnet_identidad,
                'telefono' => $this->telefono,
                'direccion' => $this->direccion,
                'estado' => $this->estado,
            ]);
        });
        
        session()->flash('message', 
            $this->personal_id ? '¡Personal actualizado!' : '¡Personal registrado exitosamente!');

        $this->closeModal();
        $this->resetInputFields();
    }
    
    public function delete($id)
    {
        $personal = Personal::find($id);
        if ($personal) {
             session()->flash('error', 'La función eliminar está inhabilitada. Usa el botón Estado.');
        }
    }

    public function openModal() { $this->isOpen = true; }
    public function closeModal() { $this->isOpen = false; }
    
    private function resetInputFields()
    {
        $this->reset([
            'name', 'email', 'password', 'role', 'user_id', 
            'personal_id', 'carnet_identidad', 'telefono', 'direccion', 'estado'
        ]);
        $this->estado = null;
    }
}