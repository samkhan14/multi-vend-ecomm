<?php

namespace App\Livewire\Admin\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\VendorBankDetail;
use GuzzleHttp\Client;

class ProfileIndex extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $name;

    #[Validate('required|email|max:255')]
    public $email;

    #[Validate('nullable|date_format:Y-m-d')]
    public $dob;

    #[Validate('nullable|string|max:500')]
    public $address;

    #[Validate('nullable|image|max:2048|mimes:jpeg,png,jpg,gif')]
    public $image;
    public $current_image;

    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Bank Details Properties
    public $account_title;
    public $iban_number;
    public $bank_name;
    public $bankDetail;
    
    // IBAN Validation ke liye properties
    public $isValidatingIBAN = false;
    public $ibanValid = null;
    public $ibanBankName = null; // Auto-fetched bank name
    public $ibanCountry = null;   // Auto-fetched country

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->dob = $user->dob ? $user->dob->format('Y-m-d') : '';
        $this->address = $user->address ?? '';
        $this->current_image = $user->image ?? '';
        
        $vendor = $user->vendor;
        if ($vendor) {
            $this->bankDetail = $vendor->bankDetail;
            if ($this->bankDetail) {
                $this->account_title = $this->bankDetail->account_title;
                $this->iban_number = $this->bankDetail->iban_number;
                $this->bank_name = $this->bankDetail->bank_name;
            }
        }
    }

    public function saveProfile()
    {
        $this->validate();

        $user = Auth::user();

        $user->name = $this->name;
        $user->email = $this->email;
        $user->dob = $this->dob ? $this->dob : null;
        $user->address = $this->address;

        if ($this->image) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $this->image->store('profile-images', 'public');
            $user->image = $imagePath;
            $this->current_image = $imagePath;

            $this->image = null;
        }
        
        $user->save();

        $this->dispatch('profile-image-updated', image: $user->image);
        $this->dispatch('show-toast', type: 'success', message: 'Profile updated successfully!');
    }

    // Auto-runs when IBAN field changes
    public function updatedIbanNumber($value)
    {
        if (empty($value) || strlen($value) < 8) {
            $this->ibanValid = null;
            $this->ibanBankName = null;
            $this->ibanCountry = null;
            return;
        }
        
        $this->isValidatingIBAN = true;
        $this->ibanValid = null;
        $this->ibanBankName = null;
        $this->ibanCountry = null;
        
        $result = $this->verifyIBANOnline($value);
        
        if ($result) {
            $this->ibanValid = $result['valid'] ?? false;
            $this->ibanBankName = $result['bank_name'] ?? null;
            $this->ibanCountry = $result['country'] ?? null;
            
            // Auto-fill bank name if available and user hasn't manually entered
            if ($this->ibanValid && $this->ibanBankName && empty($this->bank_name)) {
                $this->bank_name = $this->ibanBankName;
            }
        } else {
            $this->ibanValid = false;
        }
        
        $this->isValidatingIBAN = false;
    }

    // Real-time IBAN verification via free API (Worldwide)
    private function verifyIBANOnline($iban)
    {
        $iban = str_replace(' ', '', strtoupper($iban));
        
        $client = new Client();
        
        // Try multiple free APIs for better coverage
        $apis = [
            // API 1: Software Sphere (No API key)
            [
                'url' => 'https://softwaresphere.ca/api/iban/validate',
                'method' => 'POST',
                'body' => ['iban' => $iban]
            ],
            // API 2: IBANAPI with demo key (Limited but works for validation)
            [
                'url' => "https://ibanapi.com/api/v1/validate/{$iban}?api_key=demo",
                'method' => 'GET',
                'body' => null
            ]
        ];
        
        foreach ($apis as $api) {
            try {
                $response = $client->request($api['method'], $api['url'], [
                    'timeout' => 8,
                    'verify' => false,
                    'json' => $api['body']
                ]);
                
                $data = json_decode($response->getBody(), true);
                
                // Parse response based on API
                if (strpos($api['url'], 'softwaresphere') !== false) {
                    return [
                        'valid' => $data['valid'] ?? false,
                        'bank_name' => $data['bank_name'] ?? null,
                        'country' => $data['country'] ?? null
                    ];
                } elseif (strpos($api['url'], 'ibanapi') !== false) {
                    return [
                        'valid' => $data['valid'] ?? false,
                        'bank_name' => $data['bank_data']['name'] ?? null,
                        'country' => $data['country'] ?? null
                    ];
                }
            } catch (\Exception $e) {
                // Try next API
                continue;
            }
        }
        
        // If all APIs fail, use local basic validation (works for all countries)
        return $this->verifyIBANLocally($iban);
    }

    // Local basic validation (works for all countries)
    private function verifyIBANLocally($iban)
    {
        $iban = str_replace(' ', '', strtoupper($iban));
        
        // Basic IBAN structure: Country code (2 letters) + Check digits (2 digits) + BBAN
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return ['valid' => false, 'bank_name' => null, 'country' => null];
        }
        
        // Country code should be letters
        $countryCode = substr($iban, 0, 2);
        if (!ctype_alpha($countryCode)) {
            return ['valid' => false, 'bank_name' => null, 'country' => null];
        }
        
        // Check digits should be digits
        $checkDigits = substr($iban, 2, 2);
        if (!ctype_digit($checkDigits)) {
            return ['valid' => false, 'bank_name' => null, 'country' => null];
        }
        
        // Checksum validation (mod 97)
        $ibanCheck = substr($iban, 4) . substr($iban, 0, 4);
        $ibanCheck = preg_replace_callback('/[A-Z]/', function($match) {
            return ord($match[0]) - 55;
        }, $ibanCheck);
        
        $remainder = bcmod($ibanCheck, 97);
        $isValid = ($remainder == 1);
        
        return [
            'valid' => $isValid,
            'bank_name' => null,
            'country' => $countryCode
        ];
    }

    // Bank Details Save Method
    public function saveBankDetails()
    {
        // First check if IBAN is valid
        if ($this->ibanValid === false) {
            $this->addError('iban_number', 'Please enter a valid IBAN number');
            return;
        }
        
        // If not validated yet, validate now
        if ($this->ibanValid === null && !empty($this->iban_number)) {
            $result = $this->verifyIBANOnline($this->iban_number);
            if (!$result || !$result['valid']) {
                $this->addError('iban_number', 'Please enter a valid IBAN number');
                return;
            }
        }
        
        $this->validate([
            'account_title' => 'required|string|max:255',
            'iban_number' => 'required|string|max:100|unique:vendor_bank_details,iban_number,' . optional($this->bankDetail)->id,
            'bank_name' => 'required|string|max:255',
        ], [
            'account_title.required' => 'Account title is required',
            'iban_number.required' => 'IBAN number is required',
            'iban_number.unique' => 'This IBAN number is already used',
            'bank_name.required' => 'Bank name is required',
        ]);

        $vendor = Auth::user()->vendor;

        $bankDetail = VendorBankDetail::updateOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'account_title' => $this->account_title,
                'iban_number' => strtoupper(str_replace(' ', '', $this->iban_number)),
                'bank_name' => $this->bank_name,
            ]
        );

        $this->bankDetail = $bankDetail;

        $this->dispatch('show-toast', type: 'success', message: 'Bank details saved successfully!');
    }
    
    public function removeImage()
    {
        $this->image = null;
    }

    public function changePassword()
    {
        $validated = $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('show-toast', type: 'success', message: 'Password Changed Successfully!');
        $this->dispatch('close-password-modal');
    }

    public function render()
    {
        return view('livewire.admin.profile.profile-index');
    }
}


