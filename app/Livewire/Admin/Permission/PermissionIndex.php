<?php

namespace App\Livewire\Admin\Permission;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionIndex extends Component
{
    public $search = '';
    public $selectedRole = null;

    public function render()
    {
        $roles = Role::whereHas('permissions') // only roles with at least one permission
                ->with('permissions')
                ->when($this->search, function($query) {
                    $search = "%{$this->search}%";
                    $query->where('name', 'LIKE', $search);
                })
                ->orderBy('name')
                ->get();
                
        return view('livewire.admin.permission.permission-index', [
            'roles' => $roles,
        ]);
    }

    public function showRolePermissions($roleId)
    {
        $this->selectedRole = Role::with('permissions')->find($roleId);
    }

    public function closeRoleDetails()
    {
        $this->selectedRole = null;
    }
}
