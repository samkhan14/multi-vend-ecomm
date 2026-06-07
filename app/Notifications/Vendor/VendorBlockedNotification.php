<?php

namespace App\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorBlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $vendor;

    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Store Access Update')
            ->view('emails.vendor.blocked', [
                'vendor' => $this->vendor,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}

