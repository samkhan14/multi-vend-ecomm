<?php

namespace App\Livewire\Admin\Permission;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionCreate extends Component
{

    public $roleId = '';
    public $selectedPermissions = [];

    public function mount(): void
    {
        $this->roleId = optional(Role::orderBy('name')->first())->id ?: '';
        $this->loadAssignedPermissions();
    }

    public function updatedRoleId(): void
    {
        $this->loadAssignedPermissions();
    }

    public function loadAssignedPermissions(): void
    {

        $this->selectedPermissions = [];

        if (empty($this->roleId)) {
            return;
        }
        

        $role = Role::find($this->roleId);
        if (!$role) {
            return;
        }

        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        Log::info("Selected Permissions", ["Selected Permissions" => $this->selectedPermissions]);
    }

    public function save(): void
    {
        $this->validate([
            'roleId' => ['required'],
            'selectedPermissions' => ['array'],
        ]);

        $role = Role::findOrFail($this->roleId);

        $permissions = Permission::whereIn('id', $this->selectedPermissions)
            ->when($role->guard_name, function ($query) use ($role) {
                return $query->where('guard_name', $role->guard_name);
            })
            ->get();

        $role->syncPermissions($permissions);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->dispatch('show-toast', type: 'success', message: 'Permissions assigned successfully!');
        $this->loadAssignedPermissions();
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();
        $role = $this->roleId ? Role::find($this->roleId) : null;

        $permissions = Permission::when($role?->guard_name, function ($query) use ($role) {
                return $query->where('guard_name', $role->guard_name);
            })
            ->orderBy('name')
            ->get();

        // Group permissions by module
        $groupedPermissions = $this->groupPermissionsByModule($permissions);

        return view('livewire.admin.permission.permission-create', [
            'roles' => $roles,
            'groupedPermissions' => $groupedPermissions,
            'selectedRole' => $role,
        ]);
    }

    private function groupPermissionsByModule($permissions)
    {
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $module = $this->extractModuleFromPermission($permission->name);
            if (!isset($grouped[$module])) {
                $grouped[$module] = [
                    'name' => $module,
                    'permissions' => []
                ];
            }
            $grouped[$module]['permissions'][] = $permission;
        }

        // Sort modules alphabetically
        ksort($grouped);

        // Sort permissions within each module
        foreach ($grouped as $module => &$data) {
            usort($data['permissions'], function ($a, $b) {
                return strcmp($a->name, $b->name);
            });
        }
        return $grouped;
    }

    private function extractModuleFromPermission($permissionName)
    {
        // Extract module name from permission (e.g., "users.create" -> "Users")
        $parts = explode('.', $permissionName);
        $module = ucfirst($parts[0]);
        
        // Map some common modules to more readable names
        $moduleMapping = [  
            'Users' => 'User Management',
            'Roles' => 'Role Management',
            'Permissions' => 'Permission Management',
            'Products' => 'Product Management',
            'Categories' => 'Category Management',
            'Orders' => 'Order Management',
            'Settings' => 'System Settings',
            'Announcements' => 'Announcements',
            'Attributes' => 'Product Attributes',
            'Banners' => 'Banner Management',
            'Brands' => 'Brand Management',
            'Coupons' => 'Coupon Management',
            'Inquiries' => 'Customer Inquiries',
            'Pages' => 'Page Management',
            'Ratings' => 'Rating Management',
            'Seo' => 'SEO Management',
            'Shipping' => 'Shipping Management',
            'Subscribers' => 'Newsletter Subscribers',
            'Variants' => 'Product Variants',
            'Dashboard' => 'Dashboard',
        ];

        return $moduleMapping[$module] ?? ucfirst($module);
    }
}
