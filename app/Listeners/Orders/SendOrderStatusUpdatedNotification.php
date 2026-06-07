<?php

namespace App\Listeners\Orders;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Support\Facades\Notification;

class SendOrderStatusUpdatedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(OrderStatusUpdated $event): void
    {
        $order = Order::query()->with('user')->find($event->orderId);

        if (! $order) {
            return;
        }

        $recipientEmail = $order->email ?: optional($order->user)->email;

        if (! $recipientEmail) {
            return;
        }

        Notification::route('mail', $recipientEmail)->notify(
            new OrderStatusUpdatedNotification(
                orderId: (int) $order->id,
                orderNumber: $order->order_number,
                customerName: $order->name ?: optional($order->user)->name,
                customerEmail: $recipientEmail,
                newStatus: $event->newStatus,
                oldStatus: $event->oldStatus,
                grandTotal: (float) $order->grand_total
            )
        );
    }
}

