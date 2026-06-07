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
                    <span class="text-main-600">Cart</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- ================================ Cart Section Start ================================ -->
<section class="cart py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-xl-9 col-lg-8">
                <div class="cart-table border border-gray-100 rounded-8 px-40 py-48">
                    <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                        <table class="table style-three">
                            <thead>
                                <tr>
                                    <th class="h6 mb-0 text-lg fw-bold">Product Name</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Price</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Quantity</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Subtotal</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Delete</th>

                                </tr>
                            </thead>
                            <tbody id="cartItemsBody">
                                <!-- Loading State -->
                                <tr id="cartLoadingState">
                                    <td colspan="5" class="text-center py-10">
                                        <div class="spinner-border text-main-600" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-3">Loading cart items...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex-between flex-wrap gap-16 mt-16">
                        <div class="flex-align gap-16">
                            <input type="text" id="couponCodeInput" class="common-input" placeholder="Coupon Code" value="{{ session('applied_coupon_code') }}">
                            <button type="button" id="applyCouponBtn" class="btn btn-main py-18 w-100 rounded-8">Apply Coupon</button>
                            <button type="button" id="removeCouponBtn" class="btn btn-outline-main py-18 w-100 rounded-8" style="{{ session('applied_coupon_code') ? '' : 'display: none;' }}">Remove</button>
                        </div>
                    </div>
                    
                    <!-- Table ke neeche buttons -->
                    <div class="row mt-5">
                        <div class="col-md-6">
                             <button type="button" id="clearCartBtn" class="btn btn-danger py-18 w-100 rounded-8">
                                <i class="ph ph-trash me-2"></i> Clear Shopping Cart
                            </button>
                        </div>
                        <div class="col-md-6">
                                <a href="{{ route('products') }}" class="btn btn-main py-18 w-100 rounded-8">
                                <i class="ph ph-arrow-left me-2 "></i> Continue Shopping
                            </a>
                        
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4">
                <div class="cart-sidebar border border-gray-100 rounded-8 px-24 py-40">
                    <h6 class="text-xl mb-32">Cart Totals</h6>
                    <div class="bg-color-three rounded-8 p-24">
                        <div class="mb-32 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Subtotal</span>
                            <span class="text-gray-900 fw-semibold" id="summarySubtotal">{{ $genralsetting->currency ?? '$' }}0.00</span>
                        </div>
                        
                        <div class="mb-32 flex-between gap-8" id="shippingSummaryRow" style="display: none;">
                            <span class="text-gray-900 font-heading-two">Shipping</span>
                            <span class="text-gray-900 fw-semibold" id="summaryShipping">Calculating...</span>
                        </div>
                        
                        <div class="mb-32 flex-between gap-8" id="couponSummaryRow" style="{{ session('applied_coupon_code') ? '' : 'display: none;' }}">
                            <span class="text-gray-900 font-heading-two">Discount</span>
                            <span class="text-gray-900 fw-semibold text-success" id="summaryDiscount">-{{ $genralsetting->currency ?? '$' }}{{ number_format(session('coupon_discount_amount', 0), 2) }}</span>
                        </div>
                        
                        <!-- Shipping Message - Right side mein shipping ke neeche -->
                        <div id="shippingMessageContainer" class="mt-2"></div>
                    </div>  
                    
                    <div class="bg-color-three rounded-8 p-24 mt-24">
                        <div class="flex-between gap-8">
                            <span class="text-gray-900 text-xl fw-semibold">Total</span>
                            <span class="text-gray-900 text-xl fw-semibold" id="summaryTotal">{{ $genralsetting->currency ?? '$' }}0.00</span>
                        </div>
                    </div>  
                    
                    <a href="{{ route('checkout.index') }}" id="checkoutBtn" class="btn btn-main mt-40 py-18 w-100 rounded-8 disabled">Proceed to checkout</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================================ Cart Section End ================================ -->



<script>
// Cart page pe aate hi automatically cart load kare
$(document).ready(function() {
    if (window.cartSystem) {
        window.cartSystem.loadCartItems();
    }
});
</script>
@endsection