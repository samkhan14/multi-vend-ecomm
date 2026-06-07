<?php

namespace App\Livewire\Admin\EWallet;

use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EWalletIndex extends Component
{
    use WithPagination;

    public $availableBalance = 0.0;
    public $pendingBalance = 0.0;
    public $withdrawBalance = 0.0;

    public $totalTransactions = 0;
    public $completedTransactions = 0;
    public $pendingTransactions = 0;
    public $failedTransactions = 0;

    public $search = '';
    public $filterType = '';

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->hydrateWalletStats();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function viewTransaction(int $transactionId): void
    {
        $this->dispatch('show-toast', type: 'info', message: "Transaction detail for #EW-{$transactionId}.");
    }

    public function downloadReceipt(int $transactionId): void
    {
        $this->dispatch('show-toast', type: 'info', message: "Receipt generation queued for #EW-{$transactionId}.");
    }

    public function render()
    {
        return view('livewire.admin.e-wallet.e-wallet-index', [
            'transactions' => $this->transactions,
        ]);
    }

    public function getTransactionsProperty()
    {
        $query = $this->buildTransactionsQuery();
        $this->hydrateWalletStats(clone $query);

        return $query->paginate(10);
    }

    private function buildTransactionsQuery(): Builder
    {
        $vendorId = $this->resolveVendorId();

        $query = WalletTransaction::query()
            ->with([
                'vendor:id,store_name',
                'orderItem:id,product_name,price,base_price,quantity,commission,final_price,subtotal',
            ])
            ->latest();

        if (! $vendorId) {
            return $query->whereRaw('1 = 0');
        }

        $query->where('vendor_id', $vendorId);

        if (filled($this->search)) {
            $search = trim($this->search);
            $searchLike = "%{$search}%";

            $query->where(function (Builder $builder) use ($search, $searchLike) {
                if (is_numeric($search)) {
                    $builder->orWhere('id', (int) $search)
                        ->orWhere('order_item_id', (int) $search);
                }

                $builder->orWhere('description', 'like', $searchLike)
                    ->orWhere('status', 'like', $searchLike)
                    ->orWhere('type', 'like', $searchLike)
                    ->orWhere('source', 'like', $searchLike)
                    ->orWhereHas('orderItem', function (Builder $orderItemQuery) use ($searchLike) {
                        $orderItemQuery->where('product_name', 'like', $searchLike);
                    })
                    ->orWhereHas('vendor', function (Builder $vendorQuery) use ($searchLike) {
                        $vendorQuery->where('store_name', 'like', $searchLike);
                    });
            });
        }

        if (filled($this->filterType)) {
            if (in_array($this->filterType, ['credit', 'debit'], true)) {
                $query->where('type', $this->filterType);
            } else {
                $query->where('source', $this->filterType);
            }
        }

        return $query;
    }

    private function hydrateWalletStats(?Builder $query = null): void
    {
        $vendorId = $this->resolveVendorId();

        if (! $vendorId) {
            $this->totalTransactions = 0;
            $this->completedTransactions = 0;
            $this->pendingTransactions = 0;
            $this->failedTransactions = 0;
            $this->availableBalance = 0.0;
            $this->pendingBalance = 0.0;
            $this->withdrawBalance = 0.0;
            return;
        }

        $query = $query ?? WalletTransaction::query()->where('vendor_id', $vendorId);

        $this->totalTransactions = (clone $query)->count();
        $this->completedTransactions = (clone $query)->where('status', 'completed')->count();
        $this->pendingTransactions = (clone $query)->where('status', 'pending')->count();
        $this->failedTransactions = (clone $query)->where('status', 'cancelled')->count();

        $walletQuery = VendorWallet::query()->where('vendor_id', $vendorId);

        $this->availableBalance = (float) (clone $walletQuery)->sum('available_balance');
        $this->pendingBalance = (float) (clone $walletQuery)->sum('pending_balance');
        $this->withdrawBalance = (float) (clone $walletQuery)->sum('total_withdrawn');
    }

    private function resolveVendorId(): ?int
    {
        $userId = Auth::id();

        if (! $userId) {
            return null;
        }

        return Vendor::query()->where('user_id', $userId)->value('id');
    }
}
