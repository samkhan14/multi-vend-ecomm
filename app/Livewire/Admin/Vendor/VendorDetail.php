<?php

namespace App\Livewire\Admin\Vendor;

use App\Events\Vendor\VendorApproved;
use App\Events\Vendor\VendorBlocked;
use App\Events\Vendor\VendorRejected;
use App\Events\Vendor\VendorUnblocked;
use Livewire\Component;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\VendorDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\WithPagination;

class VendorDetail extends Component
{
    public $vendor;
    public $vendorId;
    public $totalSales = 0;
    public $totalRevenue = 0;

    use WithPagination;

    protected $listeners = ['refreshVendorData' => '$refresh'];

    public function mount($id)
    {
        $this->vendorId = $id;
        $this->loadVendorData();
    }

    private function loadVendorData()
    {
        $this->vendor = Vendor::with(['user', 'documents'])->findOrFail($this->vendorId);
        $this->calculateStatistics();
    }

private function calculateStatistics()
{
    // Get all order items for this vendor
    $orderItems = \App\Models\OrderItem::where('vendor_id', $this->vendor->id)->get();
    
    // Total sales = sum of quantities sold
    $this->totalSales = $orderItems->sum('quantity');
    
    // Total revenue = sum of base_subtotal (PKR value)
    $this->totalRevenue = $orderItems->sum('base_subtotal');
}

    public function getProductsProperty()
    {
        return Product::where('vendor_id', $this->vendor->id)
            ->where('status', 1)
            ->with(['category', 'brand', 'images'])
            ->paginate(12);
    }

    public function approveVendor()
    {
        $this->vendor->update([
            'status' => 1,
            'is_block' => 0,
        ]);

        VendorApproved::dispatch($this->vendor);

        $this->loadVendorData();
        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Vendor approved successfully!'
        );
    }

    public function rejectVendor()
    {
        $this->vendor->update([
            'status' => 0,
            'is_block' => 1,
        ]);

        VendorRejected::dispatch($this->vendor);

        $this->loadVendorData();
        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Vendor rejected successfully!'
        );
    }

    public function blockVendor()
    {
        $this->vendor->update(['is_block' => 1]);
        VendorBlocked::dispatch($this->vendor);

         // agar vendor login hai
        if (Auth::check() && Auth::id() == $this->vendor->user_id) {
            Auth::logout();

            Session::invalidate();
            Session::regenerateToken();
        }

        $this->loadVendorData();
        

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Vendor blocked successfully!'
        );
    }

    public function unblockVendor()
    {
        $this->vendor->update(['is_block' => 0]);
        VendorUnblocked::dispatch($this->vendor);
        $this->loadVendorData();
        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Vendor unblocked successfully!'
        );
    }

    public function render()
    {
        return view('livewire.admin.vendor.vendor-detail', [
            'products' => $this->products
        ]);
    }
}
