<?php

namespace App\Livewire\Admin\Vendor;

use App\Models\OrderItem;
use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class VendorOrderItems extends Component
{
    use WithPagination;

    public $vendorId;
    public $search = '';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount($id)
    {
        $this->vendorId = (int) $id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getVendorProperty()
    {
        return Vendor::with(['user', 'wallet'])->findOrFail($this->vendorId);
    }

    public function getOrderItemsProperty()
    {
        $searchLike = '%' . trim($this->search) . '%';

        return OrderItem::query()
            ->where('vendor_id', $this->vendorId)
            ->with([
                'order:id,order_number,name,email,status,created_at',
                'product:id,product_name',
            ])
            ->when(trim($this->search) !== '', function ($query) use ($searchLike) {
                $query->where(function ($searchQuery) use ($searchLike) {
                    $searchQuery
                        ->where('product_name', 'like', $searchLike)
                        ->orWhere('product_sku', 'like', $searchLike)
                        ->orWhere('variant_name', 'like', $searchLike)
                        ->orWhereHas('order', function ($orderQuery) use ($searchLike) {
                            $orderQuery->where('order_number', 'like', $searchLike)
                                ->orWhere('name', 'like', $searchLike)
                                ->orWhere('email', 'like', $searchLike);
                        });
                });
            })
            ->latest()
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.vendor.vendor-order-items', [
            'vendor' => $this->vendor,
            'orderItems' => $this->orderItems,
        ]);
    }
}
