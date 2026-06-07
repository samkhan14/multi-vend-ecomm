<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Dashboardpage extends Component
{
    // Filters
    public $dateFrom;
    public $dateTo;

    // Stats
    public $revenue = 0;
    public $ordersCount = 0;
    public $productsCount = 0;
    public $monthlyEarning = 0;
    public $deliveredOrdersCount = 0;
    public $refundedOrdersCount = 0;
    public $completedOrdersCount = 0;

    // Charts
    public $chartLabels = [];
    public $salesChartData = [];
    public $visitorsChartData = [];

    // Others
    public $latestOrders = [];
    public $vendor = null;

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo   = Carbon::now()->format('Y-m-d');

        if (Auth::user()->hasRole('Vendor')) {
            $this->vendor = Vendor::where('user_id', Auth::id())->first();
        }

        $this->loadDashboard();
    }

    public function applyFilters()
    {
        $this->loadDashboard();
        $this->dispatchCharts();
    }

    public function clearFilters()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo   = Carbon::now()->format('Y-m-d');

        $this->loadDashboard();
        $this->dispatchCharts();
    }

    private function dateRange()
    {
        return [
            Carbon::parse($this->dateFrom)->startOfDay(),
            Carbon::parse($this->dateTo)->endOfDay(),
        ];
    }

    private function loadDashboard()
    {
        /** ---------------- STATS ---------------- */

        if ($this->vendor) {
            $this->revenue = OrderItem::whereBetween('created_at', $this->dateRange())
                ->where('vendor_id', $this->vendor->id)
                ->sum('base_subtotal');

            $this->ordersCount = Order::whereHas('items', function ($q) {
                $q->where('vendor_id', $this->vendor->id);
            })->whereBetween('created_at', $this->dateRange())->count();

            $this->productsCount = Product::where('vendor_id', $this->vendor->id)->count();

            $this->monthlyEarning = OrderItem::whereBetween('created_at', $this->dateRange())
                ->where('vendor_id', $this->vendor->id)
                ->sum('base_subtotal');
        } else {
         $this->revenue = Order::whereBetween('created_at', $this->dateRange())->sum('base_amount');

            $this->ordersCount = Order::whereBetween('created_at', $this->dateRange())->count();

            $this->productsCount = Product::count();

      $this->monthlyEarning = Order::whereBetween('created_at', $this->dateRange())
    ->where('payment_status', 'paid')
    ->sum('base_amount');
        }

        $this->deliveredOrdersCount = Order::whereBetween('created_at', $this->dateRange())
            ->where('status', 'delivered')->count();

        $this->refundedOrdersCount = Order::whereBetween('created_at', $this->dateRange())
            ->where('status', 'refund')->count();

        $this->completedOrdersCount = Order::whereBetween('created_at', $this->dateRange())
            ->where('status', 'completed')->count();

        /** ---------------- CHART DATA ---------------- */

   $ordersData = $this->vendor
    ? OrderItem::whereBetween('created_at', $this->dateRange())
        ->where('vendor_id', $this->vendor->id)
        ->selectRaw('DATE(created_at) as date, SUM(subtotal) as total')
        ->groupBy('date')->pluck('total', 'date')
    : Order::whereBetween('created_at', $this->dateRange())
        ->selectRaw('DATE(created_at) as date, SUM(base_amount) as total')  // ← YAHAN CHANGE
        ->groupBy('date')->pluck('total', 'date');

        $ordersCountData = Order::whereBetween('created_at', $this->dateRange())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')->pluck('count', 'date');

        $this->chartLabels = [];
        $this->salesChartData = [];
        $this->visitorsChartData = [];

        for ($d = Carbon::parse($this->dateFrom); $d->lte(Carbon::parse($this->dateTo)); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $this->chartLabels[] = $d->format('M d');
            $this->salesChartData[] = (float) ($ordersData[$key] ?? 0);
            $this->visitorsChartData[] = (int) ($ordersCountData[$key] ?? 0);
        }

        /** ---------------- LATEST ORDERS ---------------- */

        $this->latestOrders = $this->vendor
            ? Order::whereHas('items', fn ($q) => $q->where('vendor_id', $this->vendor->id))
                ->latest()->take(10)->get()
            : Order::latest()->take(10)->get();
    }

    private function dispatchCharts()
    {
        $this->dispatch('refreshCharts', [
            'labels'       => $this->chartLabels,
            'salesData'    => $this->salesChartData,
            'ordersData'   => $this->visitorsChartData,
            'totalRevenue' => $this->revenue,
            'delivered'    => $this->deliveredOrdersCount,
            'refunded'     => $this->refundedOrdersCount,
            'completed'    => $this->completedOrdersCount,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboardpage');
    }
}
