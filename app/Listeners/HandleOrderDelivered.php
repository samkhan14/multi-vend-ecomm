<?php

namespace App\Listeners;

use App\Events\OrderDelivered;
use App\Models\OrderItem;
use App\Models\VendorWallet;
use App\Models\WalletTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class HandleOrderDelivered
{
    public function handle(OrderDelivered $event)
    {
        $order = $event->order;

        DB::transaction(function () use ($order) {

            $items = $order->items()
                ->where('wallet_added', false)
                ->get();

            if ($items->isEmpty()) return;

            $transactions = [];
            $vendorTotals = [];

            foreach ($items as $item) {

                $transactions[] = [
                    'vendor_id' => $item->vendor_id,
                    'order_item_id' => $item->id,
                    'type' => 'credit',
                    'source' => 'order_item',
                    'amount' => $item->vendor_earning,
                    'status' => 'pending',
                    'created_at' => now(),
                ];

                $vendorTotals[$item->vendor_id] =
                    ($vendorTotals[$item->vendor_id] ?? 0)
                    + $item->vendor_earning;
            }

            WalletTransaction::insert($transactions);

            foreach ($vendorTotals as $vendorId => $amount) {
                VendorWallet::where('vendor_id', $vendorId)
                    ->increment('pending_balance', $amount);
            }

            OrderItem::whereIn('id', $items->pluck('id'))
                ->update(['wallet_added' => true]);
        });
    }
}
