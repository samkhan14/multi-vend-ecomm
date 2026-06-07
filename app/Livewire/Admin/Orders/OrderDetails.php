<?php

namespace App\Livewire\Admin\Orders;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrderDetails extends Component
{
    public $orderId;
    public $order;
    public $orderItems;
    public $statusHistories;
    public $selectedStatus;
    public $paymentStatus;
    public $notes;

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrder();
     
    }

    public function loadOrder()
    {
        // 👇 IMPORTANT: 'items.prescription' add karo
        $this->order = Order::with([
            'user', 
            'items.product', 
            'items.productVariant', 
            'items.vendor.user',
            'items.prescription' // 👈 YEH LINE ADD KARO - prescription load karne ke liye
        ])->findOrFail($this->orderId);

        // Filter order items for vendor users
        if (Auth::user()->hasRole('Vendor')) {
            $vendor = Vendor::where('user_id', Auth::id())->first();
            $this->orderItems = $this->order->items->where('vendor_id', $vendor->id);
        } else {
            $this->orderItems = $this->order->items;
        }

        $this->selectedStatus = $this->order->status;
        $this->paymentStatus  = $this->order->payment_status;
        $this->notes = $this->order->notes;

        // Load status histories
        $this->statusHistories = OrderStatusHistory::where('order_id', $this->orderId)
            ->latest()
            ->get();
    }

    public function updatePaymentStatus()
    {
        $this->validate([
            'paymentStatus' => 'required|in:paid,unpaid',
        ]);

        $this->order->update([
            'payment_status' => $this->paymentStatus,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Payment status updated successfully!');
    }

    public function updateStatus()
    {
        $this->validate([
            'selectedStatus' => 'required|in:pending,processing,completed,cancelled,delivered,refund',
        ]);

        $oldStatus = (string) $this->order->status;
        if ($oldStatus === $this->selectedStatus) {
            $this->dispatch('show-toast', type: 'info', message: 'Order is already in the selected status.');
            return;
        }

        // Update order status
        $this->order->update([
            'status' => $this->selectedStatus,
        ]);

        // Create status history
        OrderStatusHistory::create([
            'order_id' => $this->orderId,
            'order_status' => $this->selectedStatus,
        ]);

        // Update shipped_at or delivered_at timestamps
        if ($this->selectedStatus === 'completed' && !$this->order->delivered_at) {
            $this->order->update(['delivered_at' => now()]);
        }

        $this->dispatchOrderStatusUpdatedEvent($oldStatus, $this->selectedStatus);

        $this->loadOrder();
        $this->dispatch('show-toast', type: 'success', message: 'Order status updated successfully!');
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        if (! in_array($status, ['pending', 'processing', 'delivered', 'completed', 'cancelled', 'refund'], true)) {
            $this->dispatch('show-toast', type: 'error', message: 'Invalid item status selected.');
            return;
        }

        $query = OrderItem::query()
            ->where('order_id', $this->orderId)
            ->whereKey($itemId);

        // Vendors can only update their own order items.
        if (Auth::user()->hasRole('Vendor')) {
            $vendor = Vendor::where('user_id', Auth::id())->first();

            if (! $vendor) {
                $this->dispatch('show-toast', type: 'error', message: 'Vendor profile not found.');
                return;
            }

            $query->where('vendor_id', $vendor->id);
        }

        $item = $query->first();

        if (! $item) {
            $this->dispatch('show-toast', type: 'error', message: 'Order item not found.');
            return;
        }

        $oldStatus = $item->status;

        $statusChange = null;

        DB::transaction(function () use ($item, $status, $oldStatus, &$statusChange) {
            $item->update(['status' => $status]);
            $this->syncWalletForOrderItem($item, $status, (string) $oldStatus);
            $statusChange = $this->syncOrderStatusFromItems();
        });

        if ($statusChange) {
            $this->dispatchOrderStatusUpdatedEvent($statusChange['old'], $statusChange['new']);
        }

        $this->loadOrder();
        $this->dispatch('show-toast', type: 'success', message: 'Item status updated successfully.');
    }

    private function syncWalletForOrderItem(OrderItem $item, string $newStatus, string $oldStatus): void
    {
        $vendorId = $item->vendor_id ?: 7;

        if ((int) $item->vendor_id !== (int) $vendorId) {
            $item->updateQuietly(['vendor_id' => $vendorId]);
        }

        $amount = (float) (($item->final_price ?? 0) > 0 ? $item->final_price : $item->subtotal);

        if ($amount <= 0) {
            return;
        }

        $wallet = VendorWallet::query()->firstOrCreate(
            ['vendor_id' => $vendorId],
            [
                'available_balance' => 0,
                'pending_balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
            ]
        );

        $transaction = WalletTransaction::query()->where('order_item_id', $item->id)->where('source', 'order_item')->first();

        if (! $transaction) {
            $transaction = new WalletTransaction([
                'vendor_id' => $vendorId,
                'order_item_id' => $item->id,
                'type' => 'credit',
                'source' => 'order_item',
                'amount' => $amount,
                'status' => 'pending',
                'description' => 'Order item wallet earning',
            ]);
        } else {
            $transaction->vendor_id = $vendorId;
            $transaction->amount = $amount;
        }

        if (in_array($newStatus, ['pending', 'processing'], true)) {
            if (! $transaction->exists || $transaction->status === 'cancelled') {
                $wallet->increment('pending_balance', $amount);
                $transaction->status = 'pending';
                $transaction->save();
                $item->updateQuietly(['wallet_added' => true]);
                return;
            }

            if ($transaction->status === 'completed' && in_array($oldStatus, ['completed', 'delivered'], true)) {
                return;
            }

            $transaction->status = 'pending';
            $transaction->save();
            return;
        }

        if (in_array($newStatus, ['completed', 'delivered'], true)) {
            if (! $transaction->exists) {
                $transaction->status = 'completed';
                $transaction->save();
                $wallet->increment('available_balance', $amount);
                $wallet->increment('total_earned', $amount);
                $item->updateQuietly(['wallet_added' => true]);
                return;
            }

            if ($transaction->status === 'pending') {
                $wallet->decrement('pending_balance', $amount);
                $wallet->increment('available_balance', $amount);
                $wallet->increment('total_earned', $amount);
            } elseif ($transaction->status === 'cancelled') {
                $wallet->increment('available_balance', $amount);
                $wallet->increment('total_earned', $amount);
            }

            $transaction->status = 'completed';
            $transaction->save();
            $item->updateQuietly(['wallet_added' => true]);
            return;
        }

        if (in_array($newStatus, ['cancelled', 'refund'], true)) {
            if ($transaction->exists && $transaction->status === 'pending') {
                $wallet->decrement('pending_balance', $amount);
            }

            if ($transaction->exists) {
                $transaction->status = 'cancelled';
                $transaction->save();
            }

            $item->updateQuietly(['wallet_added' => false]);
        }
    }

    private function syncOrderStatusFromItems(): ?array
    {
        $statuses = OrderItem::query()
            ->where('order_id', $this->orderId)
            ->pluck('status')
            ->filter()
            ->values();

        if ($statuses->isEmpty()) {
            return null;
        }

        $nextOrderStatus = match (true) {
            $statuses->every(fn ($value) => $value === 'cancelled') => 'cancelled',
            $statuses->every(fn ($value) => in_array($value, ['completed', 'delivered'], true)) => 'completed',
            $statuses->contains('processing') => 'processing',
            $statuses->contains('pending') => 'pending',
            $statuses->every(fn ($value) => $value === 'refund') => 'refund',
            default => 'processing',
        };

        if ($this->order->status !== $nextOrderStatus) {
            $oldStatus = (string) $this->order->status;
            $this->order->update(['status' => $nextOrderStatus]);

            OrderStatusHistory::create([
                'order_id' => $this->orderId,
                'order_status' => $nextOrderStatus,
            ]);

            if ($nextOrderStatus === 'completed' && ! $this->order->delivered_at) {
                $this->order->update(['delivered_at' => now()]);
            }

            return [
                'old' => $oldStatus,
                'new' => $nextOrderStatus,
            ];
        }

        if ($nextOrderStatus === 'completed' && ! $this->order->delivered_at) {
            $this->order->update(['delivered_at' => now()]);
        }

        return null;
    }

    private function dispatchOrderStatusUpdatedEvent(?string $oldStatus, string $newStatus): void
    {
        if ((string) $oldStatus === $newStatus) {
            return;
        }

        OrderStatusUpdated::dispatch(
            (int) $this->orderId,
            $oldStatus,
            $newStatus
        );
    }

    public function saveNotes()
    {
        $this->order->update([
            'notes' => $this->notes,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Notes saved successfully!');
    }

    public function render()
    {
        return view('livewire.admin.orders.order-details');
    }
}
