<?php

namespace App\Livewire\Vendor;

use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Login extends Component
{

    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:8',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function login()
    {
        $this->validate();

        try {

            if (!Auth::attempt([
                'email' => $this->email,
                'password' => $this->password
            ])) {
                $this->dispatch('show-toast', type: 'error', message: 'Invalid credentials.');
                return;
            }

            $user = Auth::user();

            // not vendor
            if (!$user->hasRole('Vendor')) {
                Auth::logout();
                $this->dispatch('show-toast', type: 'error', message: 'You are not authorized as vendor.');
                return;
            }

            // vendor blocked
            $vendor = $user->vendor;

            // vendor missing OR blocked
            if ($vendor->is_block == 1) {
                Auth::logout();
                $this->dispatch(
                    'show-toast',
                    type: 'error',
                    message: 'Your vendor account is blocked. Please contact support.'
                );
                return;
            }


            //  not approved
            if (!$vendor || $vendor->status != 1) {
                Auth::logout();
                $this->dispatch(
                    'show-toast',
                    type: 'error',
                    message: 'Your account is not approved yet.'
                );
                return;
            }

            // ✅ success
            session()->regenerate();

            $this->dispatch(
                'show-toast',
                type: 'success',
                message: 'Login successful! Redirecting...'
            );

            return redirect()->route('admin.dashboard');
        } catch (\Throwable $e) {
            Log::error('Vendor login failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            Auth::logout();

            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Something went wrong. Please try again later.'
            );
        }
    }

    public function render()
    {
        return view('livewire.vendor.login');
    }
}
