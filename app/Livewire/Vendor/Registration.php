<?php

namespace App\Livewire\Vendor;

use App\Events\Vendor\RegisterVendor;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorDocument;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class Registration extends Component
{
    use WithFileUploads;
    
    // Step management
    public $currentStep = 1;
    public $totalSteps = 3;
    
    // User Information
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    
    // Store Information
    public $store_name;
    public $store_slug;
    public $business_type;
    public $phone;
    public $address;
    public $city;
    public $country;
    
    // Document Fields
    public $cnic_front;
    public $cnic_back;
    public $ntn_number;
    public $ntn_certificate;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        
        'store_name' => 'required|string|max:255',
        'store_slug' => 'required|string|unique:vendors,store_slug',
        'business_type' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'country' => 'required|string|max:100',
        
        // Document validation
        'cnic_front' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'cnic_back' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'ntn_number' => 'required|string|max:255',
        'ntn_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ];

    protected $messages = [
        'cnic_front.required' => 'CNIC Front is required',
        'cnic_back.required' => 'CNIC Back is required',
        'ntn_number.required' => 'NTN Number is required',
        'ntn_certificate.required' => 'NTN Certificate is required',
        'store_slug.unique' => 'Store URL already taken. Please choose a different store name.',
        'email.unique' => 'Email already registered. Please use a different email.',
    ];

    public function updatedStoreName($value)
    {
        $this->store_slug = strtolower(str_replace(' ', '-', $value));
        $this->validateOnly('store_name');
        $this->validateOnly('store_slug');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);
        } elseif ($this->currentStep == 2) {
            $this->validate([
                'store_name' => 'required|string|max:255',
                'store_slug' => 'required|string|unique:vendors,store_slug',
                'business_type' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:100',
                'country' => 'required|string|max:100',
            ]);
        }
        
        $this->currentStep++;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    public function register()
    {
        
        $this->validate();

        try {
            DB::beginTransaction();

            // Create User
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'user_status' => 1,
            ]);

            $user->assignRole('Vendor');

            // Create Vendor
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'store_name' => $this->store_name,
                'store_slug' => $this->store_slug,
                'business_type' => $this->business_type,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'country' => $this->country,
                'status' => 0,
            ]);

            // Upload Documents with specific types
            $documents = [
                [
                    'type' => 'cnic_front',
                    'file' => $this->cnic_front,
                    'number' => null
                ],
                [
                    'type' => 'cnic_back',
                    'file' => $this->cnic_back,
                    'number' => null
                ],
                [
                    'type' => 'ntn_number',
                    'file' => null,
                    'number' => $this->ntn_number
                ],
                [
                    'type' => 'ntn_certificate',
                    'file' => $this->ntn_certificate,
                    'number' => $this->ntn_number 
                ]
            ];

            foreach ($documents as $document) {
                $filePath = null;
                
                if ($document['file']) {
                    $filePath = $document['file']->store('vendor-documents', 'public');
                }
                
                $vendor->documents()->create([
                    'document_type' => $document['type'],
                    'document_number' => $document['number'],
                    'document_file_path' => $filePath,
                ]);
            }

            DB::commit();
            // $vendor =  $user->email;
            RegisterVendor::dispatch($vendor);

            $this->dispatch('show-toast', type: 'success', message: 'Registration successful! Your account is pending approval.');
            
            // Reset form
            $this->reset();

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Handle specific database constraint violations
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'vendors_store_slug_unique') !== false) {
                    $this->addError('store_slug', 'This store URL is already taken. Please choose a different store name.');
                    $this->dispatch('show-toast', type: 'error', message: 'Store URL already exists. Please choose a different store name.');
                } elseif (strpos($e->getMessage(), 'users_email_unique') !== false) {
                    $this->addError('email', 'This email is already registered. Please use a different email.');
                    $this->dispatch('show-toast', type: 'error', message: 'Email already registered. Please use a different email.');
                } else {
                    $this->dispatch('show-toast', type: 'error', message: 'Registration failed. Some information already exists.');
                }
            } else {
                $this->dispatch('show-toast', type: 'error', message: 'Registration failed. Please try again.');
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Registration failed. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.vendor.registration');
    }
}
