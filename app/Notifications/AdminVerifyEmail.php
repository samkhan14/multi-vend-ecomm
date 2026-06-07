<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AdminVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Admin Account')
            ->line('Please verify your email to activate your admin account.')
            ->action('Verify Admin Email', $url)
            ->line('If you did not create an admin account, no action is required.');
    }
}
