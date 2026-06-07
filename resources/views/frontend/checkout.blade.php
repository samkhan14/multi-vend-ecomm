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
                <li class="flex-align">—</li>
                <li class="text-sm">
                    <span class="text-main-600">Checkout</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Checkout Page -->
<section class="checkout py-80">
    <div class="container container-lg">
        <!-- Messages -->
        @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif
        
        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
     <!-- Coupon Section (OPEN) -->
<div class="rounded-8 px-30 py-20 mb-40">
    <span class="fw-semibold">Have a coupon?</span>
    
    <!-- Coupon Input and Buttons Row -->
    <div class="row g-2 mt-3">
        <div class="col-md-6 col-lg-5">
            <input type="text" id="checkoutCouponCode" class="form-control" placeholder="Enter coupon code" value="{{ session('applied_coupon_code') }}">
        </div>
        <div class="col-md-3 col-lg-2">
            <button type="button" id="applyCheckoutCoupon" class="btn btn-main w-100">Apply</button>
        </div>
        <div class="col-md-3 col-lg-2">
            <button type="button" id="removeCheckoutCoupon" class="btn btn-danger w-100 fw-semibold shadow-sm" 
                    style="{{ session('applied_coupon_code') ? '' : 'display: none;' }}">
                <i class="ph ph-x-circle me-1"></i> Remove
            </button>
        </div>
    </div>
    
    <!-- Message Container - Now with limited width -->
    <div class="row">
        <div class="col-md-6 col-lg-5">
            <div id="checkoutCouponMessage" class="mt-2"></div>
        </div>
    </div>
</div>
        
        <form id="checkoutForm" action="{{ route('checkout.place.order') }}" method="POST">
            @csrf
            <input type="hidden" name="payment_method" value="cod">
            <input type="hidden" name="country" id="countryHidden">
            
            <div class="row">
                <!-- Left Column - Form -->
                <div class="col-xl-9 col-lg-8">
                    <div class="pe-xl-5">
                        <div class="row gy-3">
                            <div class="col-sm-6">
                                <input type="text" name="first_name" class="common-input @error('first_name') is-invalid @enderror" 
                                       placeholder="First Name *" value="{{ old('first_name') }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-sm-6">
                                <input type="text" name="last_name" class="common-input @error('last_name') is-invalid @enderror" 
                                       placeholder="Last Name *" value="{{ old('last_name') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                                 <div class="col-12">
                                <input type="tel" name="phone" class="common-input @error('phone') is-invalid @enderror" 
                                       placeholder="Phone Number *" value="{{ old('phone') }}" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-12">
                                <input type="email" name="email" class="common-input @error('email') is-invalid @enderror" 
                                       placeholder="Email Address *" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                        <div class="col-12">
    <div class="form-group">
        <label for="countryDisplay" class="form-label text-gray-700 mb-2">Country</label>
        <input type="text" id="countryDisplay" class="form-control bg-light" 
               placeholder="Country" readonly 
               style="background-color: #f8f9fa; cursor: default;">
        <small class="text-muted mt-1 d-block">
            <i class="ph ph-info me-1"></i>Auto-detected from currency
        </small>
    </div>
</div>
                                <div class="col-12">
                                <input type="text" name="city" class="common-input @error('city') is-invalid @enderror" 
                                       placeholder="City *" value="{{ old('city') }}" required>
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-12">
                                <input type="text" name="state" class="common-input @error('state') is-invalid @enderror" 
                                       placeholder="State / Province *" value="{{ old('state') }}" required>
                                @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <input type="text" name="address" class="common-input @error('address') is-invalid @enderror" 
                                       placeholder="Address *" value="{{ old('address') }}" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                        
                            
                            <div class="col-12">
                                <input type="text" name="zip" class="common-input @error('zip') is-invalid @enderror" 
                                       placeholder="Post Code / ZIP *" value="{{ old('zip') }}" required>
                                @error('zip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                       
                            
                            <div class="col-12">
                                <div class="my-40">
                                    <h6 class="text-lg mb-24">Additional Information</h6>
                                    <textarea name="additional_notes" class="common-input @error('additional_notes') is-invalid @enderror" 
                                              rows="3" placeholder="Notes about your order">{{ old('additional_notes') }}</textarea>
                                    @error('additional_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            <!-- Payment Method - Only COD -->
                            <div class="col-12">
                                <div class="my-40">
                                    <h6 class="text-lg mb-24">Payment Method</h6>
                                    <div class="payment-method-option p-3 border rounded">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                            <label class="form-check-label" for="cod">
                                                <i class="fas fa-money-bill-wave text-success me-2"></i>
                                                Cash on Delivery (COD)
                                            </label>
                                        </div>
                                        <small class="text-muted ms-4">Pay when you receive your order</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
         <!-- Right Column - Order Summary -->
<div class="col-xl-3 col-lg-4">
    <div class="checkout-sidebar">
        <div class="bg-color-three rounded-8 p-24 text-center">
            <span class="text-gray-900 text-xl fw-semibold">Your Orders</span>
        </div>
        
        <div class="border border-gray-100 rounded-8 px-3 px-md-4 py-40 mt-24">
            <!-- Cart Items with responsive flex -->
            <div id="checkoutCartItems">
                <!-- Items will load here -->
            </div>
            
            <!-- Discount Row -->
            <div id="checkoutDiscountRow" class="border-top pt-3 mt-3" style="display: none;">
                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <span class="text-gray-700">Discount</span>
                    <span class="text-success fw-bold" id="checkoutDiscountAmount">-$0.00</span>
                </div>
            </div>
            
            <!-- Shipping Row -->
            <div id="checkoutShippingRow" class="border-top pt-3 mt-3">
                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <span class="text-gray-700">Shipping</span>
                    <span class="fw-bold text-nowrap" id="checkoutShippingAmount">Calculating...</span>
                </div>
            </div>
            
            <!-- Shipping Message -->
            <div id="checkoutShippingMessage" class="mt-2 small text-break"></div>
            
            <div class="border-top pt-30 mt-30">
                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                    <span class="fw-semibold">Subtotal</span>
                    <span class="fw-bold text-nowrap" id="checkoutSubtotal">{{ $genralsetting->currency ?? '$' }}0.00</span>
                </div>
                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <span class="fw-semibold fs-5">Total</span>
                    <span class="fw-bold fs-5 text-main-600 text-nowrap" id="checkoutTotal">{{ $genralsetting->currency ?? '$' }}0.00</span>
                </div>
            </div>
        </div>
        
        <button type="submit" id="placeOrderBtn" class="btn btn-main mt-40 py-18 w-100 rounded-8">
            Place Order
        </button>
        
      
    </div>
</div>
            </div>
        </form>
    </div>
</section>
<style>
/* Checkout page responsive fixes */
@media (max-width: 768px) {
    .checkout .container {
        padding-left: 20px;
        padding-right: 20px;
    }
    
    .checkout-sidebar .border {
        padding-left: 20px !important;
        padding-right: 20px !important;
    }
    
    .text-nowrap {
        white-space: nowrap;
    }
    
    .text-break {
        word-break: break-word;
    }
    
    .btn-main, .btn-danger {  /* Yahan btn-outline-danger ki jagah btn-danger kiya */
        white-space: nowrap;
        font-size: 14px;
        padding: 10px 15px;
    }
    
    /* Coupon section responsive */
    .row.g-2 .col-md-6,
    .row.g-2 .col-md-3 {
        margin-bottom: 10px;
    }
}

/* Remove Coupon Button - Visible by default */
.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background-color: #bb2d3b;
    border-color: #b02a37;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
}

/* Message container styling */
#checkoutCouponMessage {
    width: 100%;
}

#checkoutCouponMessage .alert {
    margin-bottom: 0;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-size: 0.95rem;
}

#checkoutCouponMessage .alert-success {
    background-color: #d1e7dd;
    border-color: #badbcc;
    color: #0f5132;
}

#checkoutCouponMessage .alert-danger {
    background-color: #f8d7da;
    border-color: #f5c2c7;
    color: #842029;
}

#checkoutCouponMessage .alert-warning {
    background-color: #fff3cd;
    border-color: #ffecb5;
    color: #664d03;
}
</style>
@endsection