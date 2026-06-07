<?php

namespace App\Livewire\Admin\Roles;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleIndex extends Component
{
    public $search = '';

    public $roleId = null;
    public $name = '';
    public $guard_name = 'web';

    public function createRole(): void
    {
        $this->resetForm();
        $this->dispatch('openRoleModal');
    }

    public function resetForm(): void
    {
        $this->reset(['roleId', 'name']);
        $this->guard_name = 'web';
        $this->resetValidation();
    }

    public function editRole($id): void
    {
        $role = Role::query()->findOrFail($id);
        
        // Prevent editing Super Admin role
        if ($role->name === 'Super Admin') {
            $this->dispatch('show-toast', type: 'error', message: 'Super Admin role cannot be edited!');
            return;
        }

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;

        $this->dispatch('openRoleModal');
    }

    public function saveRole(): void
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $this->guard_name)
                    ->ignore($this->roleId),
            ],
            'guard_name' => ['required', 'string', 'max:255'],
        ]);

        if ($this->roleId) {
            $role = Role::query()->findOrFail($this->roleId);
            
            // Prevent editing Super Admin role
            if ($role->name === 'Super Admin') {
                $this->dispatch('show-toast', type: 'error', message: 'Super Admin role cannot be edited!');
                return;
            }
            
            $role->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            $this->dispatch('show-toast', type: 'success', message: 'Role updated successfully!');
        } else {
            Role::query()->create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            $this->dispatch('show-toast', type: 'success', message: 'Role created successfully!');
        }

        $this->resetForm();
        $this->dispatch('closeRoleModal');
    }

    public function deleteRole($id): void
    {
        $role = Role::query()->findOrFail($id);
        
        // Prevent deleting Super Admin role
        if ($role->name === 'Super Admin') {
            $this->dispatch('show-toast', type: 'error', message: 'Super Admin role cannot be deleted!');
            return;
        }
        
        $role->delete();

        $this->dispatch('show-toast', type: 'success', message: 'Role deleted successfully!');
    }

    public function render()
    {
        $roles = Role::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderByDesc('id')
            ->get();

        return view('livewire.admin.roles.role-index', [
            'roles' => $roles,
        ]);
    }
}
