<div>
    <div class="dashboard-page-content">

        <div class="row mb-9 align-items-center">
            <div class="col-12">
                <h2 class="fs-4 mb-0">E-Wallet</h2>
                <p class="mb-0">Manage your wallet and transactions</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 col-md-4 mb-4">
                <div class="card rounded-4 border-0 shadow-sm h-100">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                <i class="far fa-wallet fa-2x text-primary"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted fs-14px">Available Balance</p>
                                <h3 class="mb-0 fw-bold">{{ formatCurrency($availableBalance) }}</h3>
                            </div>
                        </div>
                        <small class="text-muted">Ready to withdraw</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mb-4">
                <div class="card rounded-4 border-0 shadow-sm h-100">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="far fa-clock fa-2x text-warning"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted fs-14px">Pending Balance</p>
                                <h3 class="mb-0 fw-bold text-warning">{{ formatCurrency($pendingBalance) }}</h3>
                            </div>
                        </div>
                        <small class="text-muted">Not available for withdrawal yet</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mb-4">
                <div class="card rounded-4 border-0 shadow-sm h-100">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                                <i class="far fa-arrow-up fa-2x text-danger"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-muted fs-14px">Withdraw Balance</p>
                                <h3 class="mb-0 fw-bold text-danger">{{ formatCurrency($withdrawBalance) }}</h3>
                            </div>
                        </div>
                        <small class="text-muted">All time withdrawn</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-header p-7 bg-transparent border-0">
                        <h5 class="card-title fs-6 mb-0">Transaction Overview</h5>
                    </div>
                    <div class="card-body p-7">
                        <div class="row text-center">
                            <div class="col-6 col-md-3 mb-4 mb-md-0">
                                <div class="rounded-circle bg-info bg-opacity-10 p-4 d-inline-flex mb-3">
                                    <i class="far fa-exchange-alt fa-2x text-info"></i>
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $totalTransactions }}</h4>
                                <p class="mb-0 text-muted fs-14px">Total Transactions</p>
                            </div>
                            <div class="col-6 col-md-3 mb-4 mb-md-0">
                                <div class="rounded-circle bg-success bg-opacity-10 p-4 d-inline-flex mb-3">
                                    <i class="far fa-check-circle fa-2x text-success"></i>
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $completedTransactions }}</h4>
                                <p class="mb-0 text-muted fs-14px">Completed</p>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-4 d-inline-flex mb-3">
                                    <i class="far fa-clock fa-2x text-warning"></i>
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $pendingTransactions }}</h4>
                                <p class="mb-0 text-muted fs-14px">Pending</p>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="rounded-circle bg-danger bg-opacity-10 p-4 d-inline-flex mb-3">
                                    <i class="far fa-times-circle fa-2x text-danger"></i>
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $failedTransactions }}</h4>
                                <p class="mb-0 text-muted fs-14px">Cancelled</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-header p-7 bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="card-title fs-6 mb-0">Transaction History</h5>
                            </div>
                            <div class="col-md-6 mt-4 mt-md-0">
                                <div class="d-flex gap-2">
                                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                        placeholder="Search transactions...">
                                    <select wire:model.live="filterType" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="credit">Credit</option>
                                        <option value="debit">Debit</option>
                                        <option value="order_item">Order Item</option>
                                        <option value="refund">Refund</option>
                                        <option value="payout">Payout</option>
                                        <option value="adjustment">Adjustment</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-7">
                        <div wire:loading class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <div class="table-responsive" wire:loading.remove>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Product</th>
                                        <th>Product Price</th>
                                        <th>Commission</th>
                                        <th>Final Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        {{-- <th class="text-center">Actions</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                        @php
                                            $isCredit = $transaction->type === 'credit';
                                            $typeClass = $isCredit ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                                            $typeIcon = $isCredit ? 'fa-arrow-down' : 'fa-arrow-up';
                                            $orderItem = $transaction->source === 'order_item' ? $transaction->orderItem : null;
                                            $hasOrderItem = (bool) $orderItem;
                                            $displayStatus = $transaction->status;
                                            $statusClass = match ($displayStatus) {
                                                'completed' => 'bg-success',
                                                'pending' => 'bg-warning',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                            $statusIcon = match ($displayStatus) {
                                                'completed' => 'fa-check-circle',
                                                'pending' => 'fa-clock',
                                                'cancelled' => 'fa-times-circle',
                                                default => 'fa-info-circle',
                                            };
                                            $statusLabel = ucwords(str_replace(['_', '-'], ' ', (string) $displayStatus));
                                        $unitPrice = $hasOrderItem ? (float) ($orderItem->base_price ?? $orderItem->price ?? 0) : null;
                                            $quantity = $hasOrderItem ? max((int) ($orderItem->quantity ?? 1), 1) : null;
                                            $commission = $hasOrderItem ? (float) ($orderItem->commission ?? 0) : null;
                                            $baseAmount = $hasOrderItem ? (float) ($orderItem->subtotal ?? 0) : null;
                                            $finalAmount = null;

                                            if ($hasOrderItem) {
                                                $finalAmount = (float) ($orderItem->final_price ?? 0);

                                                if ($finalAmount <= 0) {
                                                    $finalAmount = (float) $transaction->amount;
                                                }

                                                if ($finalAmount <= 0) {
                                                    $finalAmount = max($baseAmount - $commission, 0);
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#EW-{{ str_pad((string) $transaction->id, 6, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $typeClass }}">
                                                    <i class="far {{ $typeIcon }} me-1"></i>
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                                <div>
                                                    <small class="text-muted">{{ ucwords(str_replace('_', ' ', $transaction->source)) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="{{ $isCredit ? 'text-success' : 'text-danger' }}">
                                                    {{ $isCredit ? '+' : '-' }} {{ formatCurrency($transaction->amount) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if($hasOrderItem)
                                                    <div class="fw-semibold">{{ $orderItem->product_name ?? 'N/A' }}</div>
                                                    <small class="text-muted">Item #{{ $orderItem->id }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hasOrderItem)
                                                   <div>{{ getGeneralSetting()->currency_symbol }} {{ number_format($unitPrice, 2) }}</div>
                                                    <small class="text-muted">Qty: {{ $quantity }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hasOrderItem)
                                                    <span class="text-danger">-{{ formatCurrency($commission) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hasOrderItem)
                                                    <strong>{{ formatCurrency($finalAmount) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $statusClass }}">
                                                    <i class="far {{ $statusIcon }} me-1"></i>
                                                    {{ $statusLabel ?: 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-semibold">{{ $transaction->created_at?->format('d M Y') }}</div>
                                                    <small class="text-muted">{{ $transaction->created_at?->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $transaction->description ?: '-' }}</span>
                                                <div>
                                                    <small class="text-muted">Vendor: {{ $transaction->vendor->store_name ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            {{-- <td class="text-center">
                                                <div class="d-flex flex-nowrap justify-content-center gap-2">
                                                    <button type="button" wire:click="viewTransaction({{ $transaction->id }})" class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="far fa-eye"></i>
                                                    </button>
                                                    <button type="button" wire:click="downloadReceipt({{ $transaction->id }})" class="btn btn-sm btn-outline-secondary" title="Download Receipt">
                                                        <i class="far fa-download"></i>
                                                    </button>
                                                </div>
                                            </td> --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="far fa-inbox fa-3x mb-3 d-block"></i>
                                                    <p class="mb-0">No transactions found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
