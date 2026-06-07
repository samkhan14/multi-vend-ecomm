<?php

namespace App\Livewire\Admin\Payouts;

use App\Events\PayoutRequestSubmitted;
use App\Models\PayoutRequest;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class PayoutRequestsIndex extends Component
{
    use WithPagination;

    public $requestAmount = '';
    public $requestNote = '';
    public $adminNote = '';
    public $search = '';
    public $statusFilter = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function submitRequest(): void
    {
        $this->validate([
            'requestAmount' => 'required|numeric|min:0.01',
            'requestNote' => 'nullable|string|max:1000',
        ]);

        $vendorId = $this->resolveVendorId();

        if (! $vendorId) {
            $this->dispatch('show-toast', type: 'error', message: 'Vendor profile not found.');
            return;
        }

        try {
            $amount = round((float) $this->requestAmount, 2);

            $payoutRequest = DB::transaction(function () use ($vendorId, $amount) {
                $wallet = VendorWallet::query()
                    ->where('vendor_id', $vendorId)
                    ->lockForUpdate()
                    ->first();

                if (! $wallet) {
                    throw ValidationException::withMessages([
                        'requestAmount' => 'Wallet not found for this vendor.',
                    ]);
                }

                $pendingPayoutAmount = (float) PayoutRequest::query()
                    ->where('vendor_id', $vendorId)
                    ->where('status', 'pending')
                    ->sum('amount');

                $remainingWithdrawable = max((float) $wallet->available_balance - $pendingPayoutAmount, 0);

                if ($amount > $remainingWithdrawable) {
                    throw ValidationException::withMessages([
                        'requestAmount' => 'Insufficient withdrawable balance after pending payout requests.',
                    ]);
                }

                return PayoutRequest::query()->create([
                    'vendor_id' => $vendorId,
                    'amount' => $amount,
                    'status' => 'pending',
                    'request_note' => filled($this->requestNote) ? trim($this->requestNote) : null,
                ]);
            });

            PayoutRequestSubmitted::dispatch((int) $payoutRequest->id);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            $this->dispatch('show-toast', type: 'error', message: 'Unable to submit payout request right now.');
            return;
        }

        $this->requestAmount = '';
        $this->requestNote = '';
        $this->resetPage();
        $this->dispatch('show-toast', type: 'success', message: 'Payout request submitted successfully.');
    }

    public function approveRequest(int $requestId): void
    {
        if (! $this->canManagePayouts()) {
            $this->dispatch('show-toast', type: 'error', message: 'You are not allowed to approve payout requests.');
            return;
        }

        try {
            DB::transaction(function () use ($requestId) {
                $request = PayoutRequest::query()
                    ->whereKey($requestId)
                    ->lockForUpdate()
                    ->first();

                if (! $request || $request->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'request' => 'Payout request is not pending.',
                    ]);
                }

                $wallet = VendorWallet::query()
                    ->where('vendor_id', $request->vendor_id)
                    ->lockForUpdate()
                    ->first();

                if (! $wallet) {
                    throw ValidationException::withMessages([
                        'request' => 'Vendor wallet not found.',
                    ]);
                }

                if ((float) $request->amount > (float) $wallet->available_balance) {
                    throw ValidationException::withMessages([
                        'request' => 'Insufficient available balance to approve this request.',
                    ]);
                }

                $walletTransaction = WalletTransaction::query()->create([
                    'vendor_id' => $request->vendor_id,
                    'order_item_id' => null,
                    'type' => 'debit',
                    'source' => 'payout',
                    'amount' => (float) $request->amount,
                    'status' => 'completed',
                    'description' => 'Payout approved for request #PR-'.str_pad((string) $request->id, 6, '0', STR_PAD_LEFT),
                ]);

                $wallet->available_balance = (float) $wallet->available_balance - (float) $request->amount;
                $wallet->total_withdrawn = (float) $wallet->total_withdrawn + (float) $request->amount;
                $wallet->save();

                $request->status = 'approved';
                $request->admin_note = filled($this->adminNote) ? trim($this->adminNote) : null;
                $request->processed_by = Auth::id();
                $request->processed_at = now();
                $request->wallet_transaction_id = $walletTransaction->id;
                $request->save();
            });
        } catch (ValidationException $exception) {
            $message = (string) collect($exception->errors())->flatten()->first();
            $this->dispatch('show-toast', type: 'error', message: $message ?: 'Unable to approve payout request.');
            return;
        } catch (\Throwable $exception) {
            report($exception);
            $this->dispatch('show-toast', type: 'error', message: 'Unable to approve payout request right now.');
            return;
        }

        $this->adminNote = '';
        $this->dispatch('show-toast', type: 'success', message: 'Payout request approved successfully.');
    }

    public function rejectRequest(int $requestId): void
    {
        if (! $this->canManagePayouts()) {
            $this->dispatch('show-toast', type: 'error', message: 'You are not allowed to reject payout requests.');
            return;
        }

        $updated = PayoutRequest::query()
            ->whereKey($requestId)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'admin_note' => filled($this->adminNote) ? trim($this->adminNote) : null,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

        if (! $updated) {
            $this->dispatch('show-toast', type: 'error', message: 'Payout request is not pending.');
            return;
        }

        $this->adminNote = '';
        $this->dispatch('show-toast', type: 'success', message: 'Payout request rejected.');
    }

    public function render()
    {
        return view('livewire.admin.payouts.payout-requests-index', [
            'payoutRequests' => $this->payoutRequests,
            'canManagePayouts' => $this->canManagePayouts(),
            'hasVendorProfile' => (bool) $this->resolveVendorId(),
        ]);
    }

    public function getPayoutRequestsProperty()
    {
        return $this->buildPayoutRequestsQuery()->paginate(10);
    }

    private function buildPayoutRequestsQuery(): Builder
    {
        $query = PayoutRequest::query()
            ->with([
                'vendor:id,store_name',
                'processedBy:id,name',
            ])
            ->latest();

        if (! $this->canManagePayouts()) {
            $vendorId = $this->resolveVendorId();

            if (! $vendorId) {
                return $query->whereRaw('1 = 0');
            }

            $query->where('vendor_id', $vendorId);
        }

        if (filled($this->statusFilter) && in_array($this->statusFilter, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $this->statusFilter);
        }

        if (filled($this->search)) {
            $search = trim($this->search);
            $like = "%{$search}%";

            $query->where(function (Builder $builder) use ($search, $like) {
                if (is_numeric($search)) {
                    $builder->orWhere('id', (int) $search);
                }

                $builder->orWhere('request_note', 'like', $like)
                    ->orWhere('admin_note', 'like', $like)
                    ->orWhereHas('vendor', function (Builder $vendorQuery) use ($like) {
                        $vendorQuery->where('store_name', 'like', $like);
                    });
            });
        }

        return $query;
    }

    private function canManagePayouts(): bool
    {
        return (bool) Auth::user()?->hasRole('Super Admin');
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
