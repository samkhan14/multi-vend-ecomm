@extends('frontend.layouts.app')

@section('content')
<!-- Breadcrumb Start -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                       

                    <a href="{{ route('home') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                        Home
                    </a>
                </li>
                <li class="flex-align">—</li>
              
                <li class="text-sm">
                    <span class="text-main-600">Wishlist</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- ================================ Wishlist Section Start ================================ -->
<section class="wishlist py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-12">
                <div class="wishlist-table border border-gray-100 rounded-8 px-40 py-48">
                    <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                <table class="table style-three">
    <thead>
        <tr>
            <!-- Ab heading ka order data ke according -->
            <th class="h6 mb-0 text-lg fw-bold text-start">Product Image</th>  <!-- Naya heading -->
            <th class="h6 mb-0 text-lg fw-bold text-start">Product Name</th>
            <th class="h6 mb-0 text-lg fw-bold text-start">Price</th>
            <th class="h6 mb-0 text-lg fw-bold text-start">Stock Status</th>
            <th class="h6 mb-0 text-lg fw-bold text-start">Action</th>
            <th class="h6 mb-0 text-lg fw-bold text-start">Delete</th>  <!-- Delete last mein -->
        </tr>
    </thead>
    <tbody id="wishlist-items">
        @if($wishlists && $wishlists->count() > 0)
            @foreach($wishlists as $wishlist)
                @php
                    $product = $wishlist->product;
                    $originalPrice = $product->product_price;
                    $discountedPrice = $product->product_price;
                    
                    if($product->product_discount) {
                        $discountedPrice = $product->product_price * (1 - $product->product_discount / 100);
                    }
                @endphp
                
                <tr id="wishlist-row-{{ $product->id }}" >  
                    <!-- Product Image Column -->
                    <td class="text-start">
                        <div class="w-60 h-60 border border-gray-100 rounded-8 flex-center p-8">
                            <img src="{{ asset('storage/' . $product->thumbnail_image) }}" 
                                 class="w-full h-full object-fit-cover" 
                                 alt="{{ $product->product_name }}">
                        </div>
                    </td>
                    
                    <!-- Product Name Column -->
                    <td class="text-start">
                        <h6 class="text-md mb-0">
                            <a href="{{ route('product.details', $product->product_slug) }}" 
                               class="text-gray-900 hover-text-main-600">
                                {{ $product->product_name }}
                            </a>
                        </h6>
                    </td>
                    
                    <!-- Price Column -->
                    <td class="text-start">
                        <div class="flex-align gap-8">
                            @if($product->product_discount)
                                <span class="text-gray-400 text-sm fw-normal text-decoration-line-through">
                                    {{ $genralsetting->currency ?? '$' }} {{ number_format($originalPrice, 2) }}
                                </span>
                                <span class="text-main-600 fw-semibold">
                                    {{ $genralsetting->currency ?? '$' }} {{ number_format($discountedPrice, 2) }}
                                </span>
                            @else
                                <span class="text-main-600 fw-semibold">
                                    {{ $genralsetting->currency ?? '$' }} {{ number_format($originalPrice, 2) }}
                                </span>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Stock Column -->
                    <td class="text-start">
                        <span class="bg-success-50 text-success-600 px-20 py-6 rounded-pill text-sm fw-medium d-inline-block">
                            In Stock
                        </span>
                    </td>
                    
                    <!-- Action Column -->
                    <td class="text-start">
                        <a href="{{ route('product.details', $product->product_slug) }}" 
                           class="btn btn-outline-main rounded-8 py-13 px-24">
                            View Product
                        </a>
                    </td>
                    
                    <!-- Delete Column (Last) -->
                    <td class="text-start">
                        <a href="javascript:void(0)" 
                           class="remove-wishlist text-gray-600 hover-text-main-600"
                           data-product-id="{{ $product->id }}">
                            <i class="ph-bold ph-trash fs-5"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            
            <!-- Bottom Row - Update colspan to 6 -->
            <tr>
                <td colspan="6" class="border-0 py-8 px-0">
                    <div class="flex-between flex-wrap gap-16">
                        <a href="{{ route('products') }}" class="btn btn-outline-main rounded-8 py-13 px-32">
                            <i class="ph ph-arrow-left me-2"></i> Continue Shopping
                        </a>
                        <a href="{{ route('products') }}" class="btn btn-main rounded-8 py-13 px-32">
                            Shop More <i class="ph ph-arrow-right ms-2"></i>
                        </a>
                    </div>
                </td>
            </tr>
            
        @else
            <!-- Empty state - Update colspan to 6 -->
            <tr>
                <td colspan="6" class="text-center py-10">
                    <div class="empty-wishlist">
                        <div class="mb-24">
                            <i class="ph-bold ph-heart fs-80 text-gray-300"></i>
                        </div>
                        <h4 class="mt-4 mb-3">Your wishlist is empty</h4>
                        <p class="text-gray-600 mb-32">You haven't added any products to your wishlist yet.</p>
                        <a href="{{ route('products') }}" class="btn btn-main rounded-8 py-13 px-32">
                            Start Shopping <i class="ph ph-arrow-right ms-2"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endif
    </tbody>
</table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================================ Wishlist Section End ================================ -->


@endsection