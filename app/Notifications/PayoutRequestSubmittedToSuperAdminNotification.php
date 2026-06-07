<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutRequestSubmittedToSuperAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PayoutRequest $payoutRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vendorName = $this->payoutRequest->vendor?->store_name
            ?? $this->payoutRequest->vendor?->user?->name
            ?? 'Vendor';

        return (new MailMessage)
            ->subject('New Vendor Payout Request Submitted')
            ->view('emails.admin.payoutRequestSubmitted', [
                'payoutRequest' => $this->payoutRequest,
                'vendorName' => $vendorName,
                'amount' => (float) $this->payoutRequest->amount,
                'requestNote' => $this->payoutRequest->request_note,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payout_request_id' => $this->payoutRequest->id,
            'vendor_id' => $this->payoutRequest->vendor_id,
            'amount' => (float) $this->payoutRequest->amount,
            'status' => $this->payoutRequest->status,
        ];
    }
}

