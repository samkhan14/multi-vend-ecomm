<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VendorVerifyEmail;
use App\Notifications\AdminVerifyEmail;
use App\Notifications\VendorResetPassword;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use VendorResetPassword as GlobalVendorResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'address',
        'image',
        'user_type',
        'user_status',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }


    public function sendEmailVerificationNotification()
    {
        $isVendor = Vendor::where('user_id', $this->id)->exists();

        if ($isVendor) {
            $this->notify(new VendorVerifyEmail());
        } else {
            $this->notify(new AdminVerifyEmail());
        }
    }

    public function sendPasswordResetNotification($token)
    {
        if ($this->hasRole('Vendor')) {
            $this->notify(new VendorResetPassword($token));
            return;
        }
        // admin / normal
        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
    }

    public function vendorId()
    {
        return $this->hasRole('Vendor')
            ? $this->vendor->id
            : null;
    }   
}
