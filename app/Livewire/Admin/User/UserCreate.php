<?php

namespace App\Livewire\Admin\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class UserCreate extends Component
{
    use WithFileUploads;

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

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'dob' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'image' => 'nullable|image|max:2048',
        'user_type' => 'required|string|max:50',
        'user_status' => 'boolean',
        'role_id' => 'required|exists:roles,id',
    ];

    public function render()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('livewire.admin.user.user-create', compact('roles'));
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {

            $user = User::create([
                'name'        => $this->name,
                'email'       => $this->email,
                'password'    => Hash::make($this->password),
                'dob'         => $this->dob,
                'address'     => $this->address,
                'user_type'   => $this->user_type,
                'user_status' => $this->user_status,
            ]);

            // Image upload
            if ($this->image) {
                $path = $this->image->store('users', 'public');

                $user->update([
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
                $user->assignRole($role);
            }

            DB::commit();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: 'User created successfully!'
            );

            return redirect()->route('admin.user');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Something went wrong while creating user. Please try again.'
            );
        }
    }
}
