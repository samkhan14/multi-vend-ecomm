@extends('frontend.layouts.app')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    <a href="{{ route('home') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">Home</a>
                </li>
                <li class="flex-align">></li>
                <li class="text-sm">
                    <a href="{{ route('checkout.index') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">Checkout</a>
                </li>
                <li class="flex-align">></li>
                <li class="text-sm">
                    <span class="text-main-600">Order Confirmation</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Thank You Section -->
<section class="thankyou py-80">
    <div class="container container-lg">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-5" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <div class="row justify-content-center">
            <div style="margin-top: 20px !important;">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <!-- Success Icon -->
                       
                        
                        <!-- Success Message -->
                        <div class="text-center mb-6 mt-3">
                            <h2 class="text-success mb-3">Thank You For Your Order!</h2>
                            <p class="lead mb-4">Your order has been received and is being processed.</p>
                            
                            <div class="d-inline-block mb-4 p-3 bg-light rounded">
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span>You can download your invoice as PDF</span>
                            </div>
                        </div>
                        
                        <!-- Order Details -->
                        <div class="order-details bg-light p-4 rounded mb-5">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <h4 class="mb-0"><i class="fas fa-receipt me-2"></i> Order Details</h4>
                                <a href="{{ route('checkout.invoice', $order->order_number) }}" 
                                   class="btn btn-danger btn-sm">
                                    <i class="fas fa-download me-2"></i> Download Invoice PDF
                                </a>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-hashtag me-2"></i>Order Number:</strong>
                                    <div class="fw-bold text-primary">{{ $order->order_number }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-calendar me-2"></i>Order Date:</strong>
                                    <div>{{ $order->created_at->format('F d, Y h:i A') }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-user me-2"></i>Customer Name:</strong>
                                    <div>{{ $order->name }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong><i class="fas fa-envelope me-2"></i>Email:</strong>
                                    <div>{{ $order->email }}</div>
                                </div>
                            </div>
                            
                            <!-- Shipping Address -->
                            <div class="mt-4 pt-3 border-top">
                                <strong><i class="fas fa-map-marker-alt me-2"></i>Shipping Address:</strong>
                                <div class="mt-2 p-3 bg-white rounded">
                                    {{ $order->address }}<br>
                                    {{ $order->city }}, {{ $order->state }}<br>
                                    {{ $order->country }} - {{ $order->pincode }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="order-summary p-4 border rounded mb-5">
                            <h4 class="mb-4"><i class="fas fa-shopping-bag me-2"></i> Order Summary</h4>
                            
                            <!-- Order Items -->
                            @foreach($order->items as $item)
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $item->product_name }}</div>
                                    @if($item->variant_name)
                                    <small class="text-muted">Variant: {{ $item->variant_name }}</small>
                                    @endif
                                    <div class="text-muted small mt-1">
                                        Qty: {{ $item->quantity }} × 
                                        {{ getUserCurrency() }} {{ number_format($item->price, 2) }}
                                    </div>
                                </div>
                                <div class="fw-bold">
                                    {{ getUserCurrency() }} {{ number_format($item->subtotal, 2) }}
                                </div>
                            </div>
                            @endforeach
                            
                            <!-- Order Totals -->
                            <div class="mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>{{ getUserCurrency() }} {{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                
                                @if($order->shipping_charges > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>{{ getUserCurrency() }} {{ number_format($order->shipping_charges, 2) }}</span>
                                </div>
                                @else
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Shipping:</span>
                                    <span><i class="fas fa-check me-1"></i>Free</span>
                                </div>
                                @endif
                                
                                @if($order->coupon_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount ({{ $order->coupon_code }}):</span>
                                    <span>-{{ getUserCurrency() }} {{ number_format($order->coupon_amount, 2) }}</span>
                                </div>
                                @endif
                                
                                <div class="d-flex justify-content-between fw-bold fs-5 mt-3 pt-3 border-top">
                                    <span>Grand Total:</span>
                                    <span class="text-primary">{{ getUserCurrency() }} {{ number_format($order->grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="text-center">
                            <a href="{{ route('checkout.invoice', $order->order_number) }}" 
                               class="btn btn-danger me-3 mb-3">
                                <i class="fas fa-file-pdf me-2"></i> Download Invoice
                            </a>
                            
                            <a href="{{ route('products') }}" class="btn btn-primary me-3 mb-3">
                                <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                            </a>
                            
                            <a href="{{ route('home') }}" class="btn btn-outline-dark mb-3">
                                <i class="fas fa-home me-2"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection