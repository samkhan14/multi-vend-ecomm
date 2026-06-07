<?php

namespace App\Livewire\Admin\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class UserEdit extends Component
{
    use WithFileUploads;

    public $user;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $dob;
    public $address;
    public $image;
    public $user_type = 'admin-panel';
    public $user_status = true;
    public $role_id;
    public $existing_image;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'nullable|string|min:8|confirmed',
        'dob' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'image' => 'nullable|image|max:2048',
        'user_type' => 'required|string|max:50',
        'user_status' => 'required|boolean',
        'role_id' => 'required|exists:roles,id',
    ];

    public function mount($id)
    {
        $this->user = User::findOrFail($id);
        
        // Prevent editing Super Admin
        if ($this->user->hasRole('Super Admin')) {
            session()->flash('error', 'Cannot edit Super Admin user!');
            return redirect()->route('admin.user');
        }

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->dob = $this->user->dob;
        $this->address = $this->user->address;
        $this->user_type = $this->user->user_type;
        $this->user_status = $this->user->user_status ? true : false;
        $this->existing_image = $this->user->image;
        
        // Get current role
        $userRole = $this->user->roles->first();
        if ($userRole) {
            $this->role_id = $userRole->id;
        }
    }

    public function render()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('livewire.admin.user.user-edit', compact('roles'));
    }

    public function update()
    {
        // Update email validation rule to ignore current user
        $this->rules['email'] = 'required|string|email|max:255|unique:users,email,' . $this->user->id;
        
        // Make password optional for update
        $this->rules['password'] = 'nullable|string|min:8|confirmed';

        $this->validate();

        DB::beginTransaction();

        try {

            $updateData = [
                'name'        => $this->name,
                'email'       => $this->email,
                'dob'         => $this->dob,
                'address'     => $this->address,
                'user_type'   => $this->user_type,
                'user_status' => $this->user_status,
            ];

            // Only update password if provided
            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $this->user->update($updateData);

            // Image upload
            if ($this->image) {
                $path = $this->image->store('users', 'public');
                $this->user->update([
                    'image' => $path
                ]);
            }

           if ($this->role_id) {
                $role = Role::findOrFail($this->role_id);
                // Check if role is "Super Admin"
                if ($role->name === 'Super Admin') {
                    // Show toast and stop execution
                    $this->dispatch('show-toast', 
                        type: 'error', 
                        message: 'Cannot assign Super Admin role via admin panel.'
                    );

                    return; // Stop further execution
                }
                
                // Remove all existing roles and assign new one
                $this->user->syncRoles([$role]);
            }

            DB::commit();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: 'User updated successfully!'
            );

            return redirect()->route('admin.user');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('User update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Something went wrong while updating user. Please try again.'
            );
        }
    }
}
