<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public ?string $orderNumber,
        public ?string $customerName,
        public ?string $customerEmail,
        public string $newStatus,
        public ?string $oldStatus,
        public float $grandTotal
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orderRef = $this->orderNumber ?: ('#' . $this->orderId);
        $newStatusLabel = $this->formatStatus($this->newStatus);

        return (new MailMessage)
            ->subject("Order {$orderRef} Status Updated")
            ->view('emails.orders.statusUpdated', [
                'orderId' => $this->orderId,
                'orderNumber' => $this->orderNumber,
                'customerName' => $this->customerName,
                'customerEmail' => $this->customerEmail,
                'newStatus' => $newStatusLabel,
                'oldStatus' => $this->oldStatus ? $this->formatStatus($this->oldStatus) : null,
                'grandTotal' => $this->grandTotal,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->orderId,
            'order_number' => $this->orderNumber,
            'status' => $this->newStatus,
        ];
    }

    private function formatStatus(string $status): string
    {
        return ucwords(str_replace('_', ' ', $status));
    }
}

    