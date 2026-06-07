<div>
    <div class="dashboard-page-content">

        <div class="row mb-9 align-items-center justify-content-between">
            <div class="col-sm-6 mb-8 mb-sm-0">
                <h2 class="fs-4 mb-0">Order Details</h2>
                <p class="mb-0">Order #{{ $order->order_number }}</p>
            </div>
        </div>

        <div class="card rounded-4">
            <header class="card-header bg-transparent p-7">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-lg-0 mb-6">
                        <span class="d-inline-block">
                            <i class="far fa-calendar me-3"></i>{{ $order->created_at->format('D, M d, Y, h:i A') }}
                        </span>
                        <br>
                        <small class="text-muted">Order ID: {{ $order->order_number }}</small>
                    </div>
                    <div class="col-md-6 ml-auto d-flex justify-content-md-end flex-wrap">

                        {{-- Payment status (PAID / UNPAID) --}}
                        @can('orders.status')
                            <div class="mw-210 me-5 my-3">
                                <select class="form-select" wire:model="paymentStatus" wire:change="updatePaymentStatus">
                                    <option value="">Payment status</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                </select>
                            </div>
                        @endcan

                        {{-- Order status (pending / delivered ...) --}}
                        @can('orders.status')
                            <div class="mw-210 me-5 my-3">
                                <select class="form-select" wire:model="selectedStatus">
                                    <option value="">Change status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="refund">Refund</option>
                                </select>
                            </div>
                            <button wire:click="updateStatus" class="btn btn-primary my-3">Save</button>
                        @endcan

                        <a class="btn btn-dark print ms-5 my-3" href="#" onclick="window.print()">
                            <i class="far fa-print"></i>
                        </a>
                    </div>

                </div>
            </header>

            <div class="card-body p-7">
                <div class="row mb-8 mt-4 order-info-wrap">
                    {{-- Customer Info --}}
                    <div class="col-md-4 mb-md-0 mb-7">
                        <div class="d-flex flex-nowrap">
                            <div class="icon-wrap">
                                <span class="rounded-circle px-6 py-5 bg-green-light me-6 text-green d-inline-block">
                                    <i class="fas fa-user px-1"></i>
                                </span>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-4">Customer</h6>
                                <p class="mb-4">
                                    @if(auth()->user()->hasRole('Vendor'))
                                        {{ $order->name }}
                                    @else
                                        {{ $order->name }}<br>
                                        {{ $order->email }}<br>
                                        {{ $order->mobile }}
                                    @endif
                                </p>
                                @if (!auth()->user()->hasRole('Vendor') && $order->user_id)
                                    <a href="#" class="btn-link-custom">View profile</a>
                                @elseif(!auth()->user()->hasRole('Vendor'))
                                    <span class="text-muted">Guest User</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Order Info --}}
                    <div class="col-md-4 mb-md-0 mb-7">
                        <div class="d-flex flex-nowrap">
                            <div class="icon-wrap">
                                <span class="rounded-circle p-5 bg-green-light me-6 text-green d-inline-block">
                                    <i class="fas fa-truck px-2"></i>
                                </span>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-4">Order Info</h6>
                                <p class="mb-4">
                                    Shipping: {{ $order->courier_name ?? 'Not assigned' }}<br>
                                    Pay method: {{ $order->payment_method ?? 'N/A' }}<br>
                                    Status: <span class="text-capitalize fw-bold">{{ $order->status }}</span>
                                </p>
                                @if ($order->tracking_number)
                                    <p class="mb-0"><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Delivery Address --}}
                    @if(!auth()->user()->hasRole('Vendor'))
                        <div class="col-md-4">
                            <div class="d-flex flex-nowrap">
                                <div class="icon-wrap">
                                    <span class="rounded-circle p-5 bg-green-light me-6 text-green d-inline-block">
                                        <i class="fas fa-map-marker-alt px-2"></i>
                                    </span>
                                </div>
                                <div class="media-body">
                                    <h6 class="mb-4">Deliver to</h6>
                                    <p class="mb-4">
                                        {{ $order->address }}<br>
                                        {{ $order->city }}, {{ $order->state }}<br>
                                        {{ $order->country }} - {{ $order->pincode }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-lg-12 border-bottom">
                        {{-- Order Items Table --}}
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        @if(!auth()->user()->hasRole('Vendor'))
                                            <th>Vendor</th>
                                        @endif
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th class="text-end">Total</th>
                                        <th>Prescription</th> {{-- 👈 NEW COLUMN --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orderItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center flex-nowrap">
                                                    @if ($item->product && $item->product->thumbnail_image)
                                                        <img src="{{ asset('storage/' . $item->product->thumbnail_image) }}"
                                                            alt="{{ $item->product_name }}" width="60" height="70" class="me-3">
                                                    @endif
                                                    <div>
                                                        <p class="fw-semibold text-body-emphasis mb-0">
                                                            {{ $item->product_name }}
                                                        </p>
                                                        @if ($item->variant_name)
                                                            <small class="text-muted">{{ $item->variant_name }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            @if(!auth()->user()->hasRole('Vendor'))
                                                <td>
                                                    @if($item->vendor_id)
                                                        {{ $item->vendor->user->name ?? 'N/A' }}
                                                    @else
                                                        <span class="text-muted">Direct Sale</span>
                                                    @endif
                                                </td>
                                            @endif
                                           <td>{{ getGeneralSetting()->currency }} {{ number_format($item->base_price ?? $item->price, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                @can('orders.status')
                                                    <select class="form-select form-select-sm"
                                                        wire:change="updateItemStatus({{ $item->id }}, $event.target.value)">
                                                        <option value="pending" @selected(($item->status ?? 'pending') === 'pending')>Pending</option>
                                                        <option value="processing" @selected($item->status === 'processing')>
                                                            Processing</option>
                                                        <option value="delivered" @selected($item->status === 'delivered')>
                                                            Delivered</option>
                                                        <option value="completed" @selected($item->status === 'completed')>
                                                            Completed</option>
                                                        <option value="cancelled" @selected($item->status === 'cancelled')>
                                                            Cancelled</option>
                                                        <option value="refund" @selected($item->status === 'refund')>Refund
                                                        </option>
                                                    </select>
                                                @else
                                                    <span class="text-capitalize">{{ $item->status ?? 'pending' }}</span>
                                                @endcan
                                            </td>
                                            <td class="text-end">{{ getGeneralSetting()->currency }} {{ number_format($item->base_subtotal ?? $item->subtotal, 2) }}</td>

                                            <td class="text-center">
                                                @if($item->prescription)
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="scrollToPrescription({{ $item->id }})"
                                                        title="View Prescription">
                                                        <i class="far fa-eye"></i> DAW
                                                    </button>
                                                @else
                                                    <span class="badge bg-secondary">No Daw</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->hasRole('Vendor') ? 6 : 7 }}"
                                                class="text-center text-muted">No items found</td>
                                        </tr>
                                    @endforelse

                                    {{-- Order Summary --}}
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('Vendor') ? 6 : 7 }}">
                                            @php
                                             $itemsBaseSubtotal = $orderItems->sum(function ($item) {
    return (float) ($item->base_price ?? $item->price ?? 0) * (int) ($item->quantity ?? 0);
});
                                                $itemsDiscountTotal = (float) $orderItems->sum('discount');
                                                $itemsTaxTotal = (float) $orderItems->sum('tax');
                                                $itemsSubtotalTotal = (float) $orderItems->sum('subtotal');
                                                $itemsCommissionTotal = (float) $orderItems->sum('commission');
                                                $vendorNetTotal = $orderItems->sum(function ($item) {
                                                    $finalPrice = (float) ($item->final_price ?? 0);
                                                    $subtotal = (float) ($item->subtotal ?? 0);
                                                    $commission = (float) ($item->commission ?? 0);

                                                    return $finalPrice > 0 ? $finalPrice : max($subtotal - $commission, 0);
                                                });
                                            @endphp
                                            <div class="d-flex flex-column align-items-end justify-content-end">
                                                <div class="mw-40 w-40">
                                                    <div class="d-flex w-100">
                                                        <span class="d-inline-block w-50">Subtotal:</span>
                                                        <span class="d-inline-block w-50 text-end fw-normal">
                                                          {{ getGeneralSetting()->currency }} {{ number_format($itemsBaseSubtotal, 2) }}
                                                        </span>
                                                    </div>
                                                    @if ($itemsDiscountTotal > 0)
                                                        <div class="d-flex w-100">
                                                            <span class="d-inline-block w-50">Item Discount:</span>
                                                            <span
                                                                class="d-inline-block w-50 text-end fw-normal text-success">
                                                                -{{ getGeneralSetting()->currency }} {{ number_format($order->coupon_amount, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($itemsTaxTotal > 0)
                                                        <div class="d-flex w-100">
                                                            <span class="d-inline-block w-50">Tax:</span>
                                                            <span class="d-inline-block w-50 text-end fw-normal">
                                                                {{ formatCurrency($itemsTaxTotal, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($itemsCommissionTotal > 0)
                                                        <div class="d-flex w-100">
                                                            <span class="d-inline-block w-50">Commission:</span>
                                                            <span
                                                                class="d-inline-block w-50 text-end fw-normal text-danger">
                                                                -{{ formatCurrency($itemsCommissionTotal, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if (!auth()->user()->hasRole('Vendor'))
                                                        <div class="d-flex w-100">
                                                            <span class="d-inline-block w-50">Shipping:</span>
                                                            <span class="d-inline-block w-50 text-end fw-normal">
                                                                {{ getGeneralSetting()->currency }} {{ number_format($order->shipping_charges / $order->conversion_rate, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($order->coupon_amount > 0)
                                                        <div class="d-flex w-100">
                                                            <span class="d-inline-block w-50">Discount
                                                                ({{ $order->coupon_code }}):</span>
                                                            <span
                                                                class="d-inline-block w-50 text-end fw-normal text-success">
                                                               @php
    $discountInBase = $order->coupon_amount;
    if($order->conversion_rate && $order->conversion_rate > 0) {
        $discountInBase = $order->coupon_amount / $order->conversion_rate;
    }
@endphp

-{{ getGeneralSetting()->currency }} {{ number_format($discountInBase, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="d-flex w-100 mb-5">
                                                        <span class="d-inline-block w-50">Grand total:</span>
                                                        <span class="d-inline-block w-50 text-end fs-5 fw-semibold">
                                                         @if(auth()->user()->hasRole('Vendor'))
    @php
        $vendorGrandTotal = $orderItems->sum(function ($item) {
            return ((float) ($item->base_price ?? $item->price ?? 0)) * ((int) ($item->quantity ?? 0));
        });
    @endphp
    {{ getGeneralSetting()->currency }} {{ number_format($vendorGrandTotal, 2) }}
@else
    {{ getGeneralSetting()->currency }} {{ number_format($order->base_amount, 2) }}
@endif
                                                        </span>
                                                    </div>
                                                    <div class="d-flex w-100">
                                                        <span class="d-inline-block w-50 text-muted">Payment
                                                            Status:</span>
                                                        <span class="d-inline-block w-50 text-end fs-20 fw-semibold">
                                                            @php
                                                                $statuspayment = match ($order->payment_status) {
                                                                    'unpaid' => 'alert-warning text-warning',
                                                                    'paid' => 'alert-success text-success',
                                                                    default => 'alert-secondary text-secondary',
                                                                };
                                                            @endphp
                                                            <span
                                                                class="badge rounded-pill alert {{ $statuspayment }} fs-12px px-4 py-3 text-capitalize">
                                                                {{ $order->payment_status }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex w-100">
                                                        <span class="d-inline-block w-50 text-muted">Status:</span>
                                                        <span class="d-inline-block w-50 text-end fs-20 fw-semibold">
                                                            @php
                                                                $statusClass = match ($order->status) {
                                                                    'pending' => 'alert-warning text-warning',
                                                                    'processing' => 'alert-info text-info',
                                                                    'delivered' => 'alert-success text-success',
                                                                    'completed' => 'alert-success text-success',
                                                                    'cancelled' => 'alert-danger text-danger',
                                                                    'refund' => 'alert-secondary text-secondary',
                                                                    default => 'alert-secondary text-secondary',
                                                                };
                                                            @endphp
                                                            <span
                                                                class="badge rounded-pill alert {{ $statusClass }} fs-12px px-4 py-3 text-capitalize">
                                                                {{ $order->status }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-4 ">
                        {{-- Payment Info --}}
                        <div class="box shadow-sm bg-body-tertiary p-6 mb-4">
                            <h6 class="mb-6">Payment Info</h6>
                            <div>
                                <p class="mb-2">
                                    <strong>Method:</strong> {{ $order->payment_method ?? 'N/A' }}
                                </p>
                                @if ($order->payment_gateway)
                                    <p class="mb-2">
                                        <strong>Gateway:</strong> {{ $order->payment_gateway }}
                                    </p>
                                @endif
                                @if ($order->transaction_id)
                                    <p class="mb-2">
                                        <strong>Transaction ID:</strong> {{ $order->transaction_id }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Status History --}}
                        @if ($statusHistories->count() > 0)
                            <div class="box shadow-sm bg-body-tertiary p-6 mb-4">
                                <h6 class="mb-6">Status History</h6>
                                <div class="timeline">
                                    @foreach ($statusHistories as $history)
                                        <div class="mb-3">
                                            <small class="text-muted">{{ $history->created_at->format('M d, Y h:i A') }}</small>
                                            <p class="mb-0 text-capitalize">
                                                <strong>{{ $history->order_status }}</strong>
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                       
                    </div>

                    <div class="col-lg-4">
   {{-- Notes Section --}}
                        <div class="h-25 pt-4">
                            <div class="mb-6">
                                <label class="mb-5 fs-13px ls-1 fw-semibold text-uppercase">Notes</label>
                                <textarea class="form-control" wire:model="notes" rows="4"
                                    placeholder="Type some note"></textarea>
                            </div>
                            <button wire:click="saveNotes" class="btn btn-primary">Save note</button>
                        </div>
                    </div>
                  
                </div>
            </div>
        </div>

    <!-- Prescription Details Toggle Section -->
    @if($orderItems->contains(function ($item) {
        return $item->prescription; }))
        <div class="mt-5">
            <div class="card rounded-4 border-primary">
                <div class="card-header bg-primary bg-opacity-10 py-3 d-flex justify-content-between align-items-center"
                    style="cursor: pointer;" onclick="togglePrescriptionSection()">
                    <h5 class="mb-0 text-primary">
                        <i class="far fa-eye me-2"></i> Prescription Details
                        <span
                            class="badge bg-primary ms-2">{{ $orderItems->filter(function ($item) {
            return $item->prescription; })->count() }}</span>
                    </h5>
                    <i class="fas fa-chevron-down text-primary" id="prescriptionToggleIcon"></i>
                </div>

                <div class="card-body" id="prescriptionSection" style="display: none;">
                    @foreach($orderItems as $item)
                        @if($item->prescription)
                            <div class="border-bottom pb-4 mb-4" id="prescription-{{ $item->id }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0">
                                        {{ $item->product_name }}
                                        @if($item->variant_name)
                                            <small class="text-muted">({{ $item->variant_name }})</small>
                                        @endif
                                    </h6>
                                    <span class="badge bg-light text-dark">Order Item #{{ $loop->iteration }}</span>
                                </div>

                                <div class="row g-4">
                                    <!-- Right Eye -->
                                    <div class="col-md-6">
                                        <div class="bg-light p-4 rounded-3">
                                            <h6 class="fw-bold text-primary mb-3">
                                                <i class="fas fa-eye me-2"></i>Right Eye (OD)
                                            </h6>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td class="text-muted ps-0" width="40%">Axis:</td>
                                                    <td class="fw-semibold">{{ $item->prescription->right_axis }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted ps-0">Spherical (SPH):</td>
                                                    <td class="fw-semibold">{{ $item->prescription->right_spherical }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted ps-0">Cylindrical (CYL):</td>
                                                    <td class="fw-semibold">{{ $item->prescription->right_cylindrical }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Left Eye -->
                                    <div class="col-md-6">
                                        <div class="bg-light p-4 rounded-3">
                                            <h6 class="fw-bold text-primary mb-3">
                                                <i class="fas fa-eye me-2"></i>Left Eye (OS)
                                            </h6>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td class="text-muted ps-0" width="40%">Axis:</td>
                                                    <td class="fw-semibold">{{ $item->prescription->left_axis }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted ps-0">Spherical (SPH):</td>
                                                    <td class="fw-semibold">{{ $item->prescription->left_spherical }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted ps-0">Cylindrical (CYL):</td>
                                                    <td class="fw-semibold">{{ $item->prescription->left_cylindrical }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
<!-- Prescription Image -->
@if($item->prescription->prescription_image)
<div class="mt-4">
    <div class="bg-light p-3 rounded-3">
        <h6 class="fw-bold text-primary mb-3">
            <i class="fas fa-image me-2"></i>Prescription Image
        </h6>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <img src="{{ asset('storage/' . $item->prescription->prescription_image) }}" 
                 alt="Prescription" 
                 style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                 class="rounded border"
                 onclick="window.open('{{ asset('storage/' . $item->prescription->prescription_image) }}', '_blank')">
            <a href="{{ asset('storage/' . $item->prescription->prescription_image) }}" 
               target="_blank" 
               class="btn btn-sm btn-outline-primary py-1 px-3"
               style="font-size: 12px;">
                <i class="fas fa-eye me-1"></i> View
            </a>
        </div>
    </div>
</div>
@endif
                                @if($item->prescription->notes)
                                    <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded-3">
                                        <i class="fas fa-sticky-note text-warning me-2"></i>
                                        <span class="fw-semibold">Notes:</span> {{ $item->prescription->notes }}
                                    </div>
                                @endif

                                @if(!$loop->last)
                                    <hr class="my-4">
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Add this JavaScript --}}
        <script>
            function scrollToPrescription (itemId) {
                // First, show the prescription section if hidden
                var section = document.getElementById('prescriptionSection');
                var icon = document.getElementById('prescriptionToggleIcon');

                if (section.style.display === 'none') {
                    section.style.display = 'block';
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }

                // Then scroll to the specific prescription
                setTimeout(function () {
                    var element = document.getElementById('prescription-' + itemId);
                    if (element) {
                        element.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        // Highlight effect
                        element.style.transition = 'background-color 0.5s ease';
                        element.style.backgroundColor = '#fff3cd';
                        setTimeout(function () {
                            element.style.backgroundColor = '';
                        }, 1500);
                    }
                }, 300); // Small delay for section to open
            }
        </script>

        <style>
            #prescriptionSection {
                transition: all 0.3s ease;
            }

            .prescription-item:last-child {
                border-bottom: none !important;
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }

            .bg-light {
                background-color: #f8f9fa !important;
            }

            .table td {
                border-top: none;
                padding: 0.5rem 0;
            }
        </style>
    @endif
    </div>





</div>