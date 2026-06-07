<div>
    <div class="dashboard-page-content">
        <div class="row mb-9 align-items-center justify-content-between">
            <div class="col-sm-7 mb-8 mb-sm-0">
                <h2 class="fs-4 mb-0">Vendor Order Items</h2>
                <p class="mb-0">
                    {{ $vendor->store_name ?? 'N/A' }} (Vendor ID: {{ $vendor->id }})
                </p>
            </div>
            <div class="col-sm-5 text-sm-end">
                <a href="{{ route('admin.vendor') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="far fa-arrow-left me-2"></i> Back to Vendors
                </a>
            </div>
        </div>

        @php
            $wallet = $vendor->wallet;
            $availableBalance = (float) ($wallet->available_balance ?? 0);
            $pendingBalance = (float) ($wallet->pending_balance ?? 0);
            $totalEarned = (float) ($wallet->total_earned ?? 0);
            $totalWithdrawn = (float) ($wallet->total_withdrawn ?? 0);
        @endphp

        <div class="row mb-5">
            <div class="col-12 col-md-3 mb-4">
                <div class="card rounded-4 border-0 shadow-sm h-100">
                    <div class="card-body p-7">
                        <p class="mb-2 text-muted fs-14px">Available Balance</p>
                        <h4 class="mb-0 fw-bold">{{ formatCurrency($availableBalance, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-4">
                <div class="card rounded-4 border-0 shadow-sm h-100">
                    <div class="card-body p-7">
                        <p class="mb-2 text-muted fs-14px">Pending Balance</p>
                        <h4 class="mb-0 fw-bold text-warning">{{ formatCurrency($pendingBalance, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-4">
                <div class="card rounded-4 border-0 shadow-sm h-100">
                    <div class="card-body p-7">
                        <p class="mb-2 text-muted fs-14px">Total Earned</p>
                        <h4 class="mb-0 fw-bold text-success">{{ formatCurrency($totalEarned, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-4">
                <div class="card rounded-4 border-0 shadow-sm h-100">
                    <div class="card-body p-7">
                        <p class="mb-2 text-muted fs-14px">Total Withdrawn</p>
                        <h4 class="mb-0 fw-bold text-danger">{{ formatCurrency($totalWithdrawn, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 rounded-4 p-7">
            <div class="card-header bg-transparent px-0 pt-0 pb-7">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-5 col-12">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search by order, customer, product, SKU..."
                            class="form-control bg-input border-0">
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-7 pb-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0 table-borderless">
                        <thead class="table-light">
                            <tr>
                                <th class="align-middle" scope="col">Order #</th>
                                <th class="align-middle" scope="col">Customer</th>
                                <th class="align-middle" scope="col">Product</th>
                                <th class="align-middle text-center" scope="col">Qty</th>
                                <th class="align-middle text-end" scope="col">Item Total</th>
                                <th class="align-middle" scope="col">Status</th>
                                <th class="align-middle" scope="col">Date</th>
                                <th class="align-middle text-center" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orderItems as $item)
                                @php
                              $itemTotal = (float) ($item->base_subtotal ?? 0);
                                @endphp
                                <tr>
                                    <td>#{{ $item->order->order_number ?? 'N/A' }}</td>
                                    <td>
                                        {{ $item->order->name ?? 'N/A' }} <br>
                                        <small class="text-muted">{{ $item->order->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $item->product_name ?? $item->product->product_name ?? 'N/A' }}</span>
                                        @if (!empty($item->variant_name))
                                            <br>
                                            <small class="text-muted">{{ $item->variant_name }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity ?? 0 }}</td>
                                   <td class="text-end">{{ getGeneralSetting()->currency }} {{ number_format($itemTotal, 2) }}</td>
                                    <td>
                                        <span class="badge rounded-pill alert alert-secondary py-2 px-3 border-0 text-capitalize">
                                            {{ ucfirst($item->status ?? ($item->order->status ?? 'pending')) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ optional($item->order?->created_at)->format('d.m.Y') ?? 'N/A' }} <br>
                                        <small class="text-muted">{{ optional($item->order?->created_at)->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->order_id)
                                            <a href="{{ route('admin.orders.detail', $item->order_id) }}"
                                                class="btn btn-primary fs-13px btn-xs py-4">
                                                Detail
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary fs-13px btn-xs py-4"
                                                disabled>
                                                Detail
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <p class="text-muted mb-0">No order items found for this vendor.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 mb-4">
            {{ $orderItems->links() }}
        </div>
    </div>
</div>
