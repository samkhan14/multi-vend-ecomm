<?php

namespace App\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('Store Approval Update')
            ->view('emails.vendor.rejected', [
                'vendor' => $this->vendor,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}

