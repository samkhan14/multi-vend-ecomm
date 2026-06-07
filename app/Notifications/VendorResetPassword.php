<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class VendorResetPassword extends ResetPassword
{
    protected function resetUrl($notifiable)
    {
        return url(route(
            'vendor.password.reset',
            [
                'token' => $this->token,
                'email' => $notifiable->email,
            ],
            false // 👈 IMPORTANT
        ));
    }
}
