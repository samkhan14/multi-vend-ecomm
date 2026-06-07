{{-- resources/views/frontend/compare.blade.php --}}
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
                    <span class="text-main-600">Compare Products</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Compare Section Start -->
<section class="compare py-80">
    <div class="container container-lg">


        @if($compares && $compares->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="compare-table">
                {{-- Products Row with Remove Button --}}
                <thead>
                    <tr>
                        <th scope="col" class="text-center bg-gray-100 p-5" width="200">
                            <span class="fs-6 fw-bold">Products</span>
                        </th>
                        
                        @foreach($compares as $compare)
                        @php $product = $compare->product; @endphp
                        <th scope="col" class="pb-9 position-relative compare-product-column" data-product-id="{{ $product->id }}" width="250">
                            {{-- Remove Button (X) with AJAX --}}
                            <a href="javascript:void(0)" 
                               class="remove-compare position-absolute top-0 end-0 mt-3 me-3 w-32 h-32 flex-center rounded-circle bg-danger text-white hover-bg-danger-dark z-1"
                               data-product-id="{{ $product->id }}"
                               title="Remove from compare">
                                <i class="ph-bold ph-x fs-5"></i>
                            </a>
                            
                            <p class="fs-18px text-center mt-4 mb-6 text-gray-900 fw-semibold">
                                {{ $product->product_name }}
                            </p>
                            <a href="{{ route('product.details', $product->product_slug) }}" class="text-center d-block">
                                <img src="{{ asset('storage/' . $product->thumbnail_image) }}" 
                                     class="img-fluid lazy-image" 
                                     alt="{{ $product->product_name }}"
                                     style="max-width: 210px; max-height: 280px;">
                            </a>
                        </th>
                        @endforeach
                        
                        {{-- If less than 4 products, show empty cells --}}
                        @for($i = $compares->count(); $i < 4; $i++)
                        <th scope="col" class="pb-9 bg-light empty-compare-cell" width="250">
                            <div class="text-center py-48">
                                <i class="ph-bold ph-plus-circle fs-48 text-gray-300"></i>
                                <p class="text-gray-500 mt-16">Add more products</p>
                                <a href="{{ route('products') }}" class="btn btn-outline-main rounded-8 py-8 px-24 mt-16">
                                    Browse Products
                                </a>
                            </div>
                        </th>
                        @endfor
                    </tr>
                </thead>
                
                <tbody>
                    {{-- Price Row --}}
                    <tr>
                        <th class="text-center bg-gray-100 p-5 fw-bold">
                            Price
                        </th>
                        
                        @foreach($compares as $compare)
                        @php 
                            $product = $compare->product;
                            
                            // ===== Sale Price Logic from Product Card =====
                            $currentDate = Carbon\Carbon::now();
                            $hasActiveSale = false;
                            $salePrice = null;
                            
                            if ($product->sale_price) {
                                $saleStart = $product->sale_start_date ? Carbon\Carbon::parse($product->sale_start_date) : null;
                                $saleEnd = $product->sale_end_date ? Carbon\Carbon::parse($product->sale_end_date) : null;
                                
                                // Check if sale is active
                                $hasActiveSale = (
                                    $saleStart && $currentDate >= $saleStart &&
                                    (!$saleEnd || $currentDate <= $saleEnd)
                                );
                                
                                if ($hasActiveSale) {
                                    $salePrice = $product->sale_price;
                                }
                            }
                            
                            // Price calculation with priority: Sale Price > Discount > Original
                            $originalPrice = $product->product_price;
                            $displayPrice = $originalPrice;
                            $showStrikeThrough = false;
                            $showSalePrice = false;
                            $discountPercent = $product->product_discount ? round($product->product_discount) : 0;
                            
                            if ($hasActiveSale && $salePrice) {
                                // Priority 1: Active Sale Price
                                $displayPrice = $salePrice;
                                $showStrikeThrough = true;
                                $showSalePrice = true;
                            } elseif ($product->product_discount && !$hasActiveSale) {
                                // Priority 2: Discount (only if no active sale)
                                $displayPrice = $product->product_price * (1 - $product->product_discount / 100);
                                $showStrikeThrough = true;
                            }
                            // Priority 3: Original Price (already set)
                            
                            $currency = $genralsetting->currency ?? '$';
                        @endphp
                        <td class="px-6 px-lg-9 text-center price-cell" data-product-id="{{ $product->id }}">
                            @if($showStrikeThrough)
                                <span class="text-gray-400 text-sm fw-normal text-decoration-line-through me-8">
                                    {{ $currency }}{{ number_format($originalPrice, 2) }}
                                </span>
                            @endif
                            <span class="{{ $showSalePrice ? 'text-success' : 'text-main-600' }} fw-bold fs-18px">
                                {{ $currency }}{{ number_format($displayPrice, 2) }}
                                @if($showSalePrice)
                                    <span class="text-success fw-normal fs-14px"> (Sale)</span>
                                @elseif($product->product_discount && !$hasActiveSale)
                                    <span class="text-gray-500 fw-normal fs-14px"> (-{{ $discountPercent }}%)</span>
                                @endif
                            </span>
                        </td>
                        @endforeach
                        
                        @for($i = $compares->count(); $i < 4; $i++)
                        <td class="px-6 px-lg-9 text-center text-gray-400 empty-cell">-</td>
                        @endfor
                    </tr>
                    
                    {{-- Stock Status Row --}}
                    <tr>
                        <th class="text-center bg-gray-100 p-5 fw-bold">
                            Stock Status
                        </th>
                        
                        @foreach($compares as $compare)
                        @php $product = $compare->product; @endphp
                        <td class="px-6 px-lg-9 text-center stock-cell" data-product-id="{{ $product->id }}">
                            @php $hasStock = $product->stock >= 1; @endphp
                            
                            @if($hasStock)
                                <i class="ph-bold ph-check-circle fs-5 text-success-600 me-2"></i>
                                <span class="fw-medium text-success-600">In Stock ({{ $product->stock }})</span>
                            @else
                                <i class="ph-bold ph-x-circle fs-5 text-danger me-2"></i>
                                <span class="fw-medium text-danger">Out of Stock</span>
                            @endif
                        </td>
                        @endforeach
                        
                        @for($i = $compares->count(); $i < 4; $i++)
                        <td class="px-6 px-lg-9 text-center text-gray-400 empty-cell">-</td>
                        @endfor
                    </tr>
                    
                    {{-- SKU Row --}}
                    <tr>
                        <th class="text-center bg-gray-100 p-5 fw-bold">
                            SKU
                        </th>
                        
                        @foreach($compares as $compare)
                        @php $product = $compare->product; @endphp
                        <td class="px-6 px-lg-9 text-center fw-medium sku-cell" data-product-id="{{ $product->id }}">
                            {{ $product->sku ?? 'N/A' }}
                        </td>
                        @endforeach
                        
                        @for($i = $compares->count(); $i < 4; $i++)
                        <td class="px-6 px-lg-9 text-center text-gray-400 empty-cell">-</td>
                        @endfor
                    </tr>
                    
                    {{-- Brand Row --}}
                    <tr>
                        <th class="text-center bg-gray-100 p-5 fw-bold">
                            Brand
                        </th>
                        
                        @foreach($compares as $compare)
                        @php $product = $compare->product; @endphp
                        <td class="px-6 px-lg-9 text-center fw-medium brand-cell" data-product-id="{{ $product->id }}">
                            {{ $product->brand->name ?? 'No Brand' }}
                        </td>
                        @endforeach
                        
                        @for($i = $compares->count(); $i < 4; $i++)
                        <td class="px-6 px-lg-9 text-center text-gray-400 empty-cell">-</td>
                        @endfor
                    </tr>
                    
             
                    
                    
                    {{-- Vendor/Store Row --}}
                    <tr>
                        <th class="text-center bg-gray-100 p-5 fw-bold">
                            Sold By
                        </th>
                        
                        @foreach($compares as $compare)
                        @php $product = $compare->product; @endphp
                        <td class="px-6 px-lg-9 text-center fw-medium vendor-cell" data-product-id="{{ $product->id }}">
                            <span class="py-2 px-8 text-xs rounded-pill text-main-two-600 bg-main-two-50 d-inline-block">
                                <i class="ph-fill ph-storefront me-1"></i> {{ $product->vendor->store_name ?? 'Store' }}
                            </span>
                        </td>
                        @endforeach
                        
                        @for($i = $compares->count(); $i < 4; $i++)
                        <td class="px-6 px-lg-9 text-center text-gray-400 empty-cell">-</td>
                        @endfor
                    </tr>
                    
                    {{-- Rating Row --}}
                    <tr>
                        <th class="text-center bg-gray-100 p-5 fw-bold">
                            Rating
                        </th>
                        
                        @foreach($compares as $compare)
                        @php 
                            $product = $compare->product;
                            
                            // Rating from AppServiceProvider (same as product card)
                            $productRatingData = $productRatings[$product->id] ?? [];
                            $avgRating = collect($productRatingData)->avg('rating') ?? 0;
                            $totalReviews = collect($productRatingData)->count();
                        @endphp
                        <td class="px-6 px-lg-9 text-center rating-cell" data-product-id="{{ $product->id }}">
                            <div class="d-flex align-items-center justify-content-center flex-wrap gap-6">
                                <span class="fw-semibold text-gray-900 me-2">{{ number_format($avgRating, 1) }}</span>
                                <div class="d-flex align-items-center gap-4">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($avgRating))
                                            <i class="ph-fill ph-star text-warning-600 fs-6"></i>
                                        @else
                                            <i class="ph-fill ph-star text-gray-300 fs-6"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ms-2 fs-14px text-gray-600">({{ $totalReviews > 0 ? $totalReviews : '0' }})</span>
                            </div>
                        </td>
                        @endforeach
                        
                        @for($i = $compares->count(); $i < 4; $i++)
                        <td class="px-6 px-lg-9 text-center text-gray-400 empty-cell">-</td>
                        @endfor
                    </tr>
                    
                    {{-- Short Description Row --}}
                    <tr>
                        <th class="text-center bg-gray-100 p-5 fw-bold">
                            Description
                        </th>
                        
                        @foreach($compares as $compare)
                        @php $product = $compare->product; @endphp
                        <td class="px-6 px-lg-9 text-center description-cell" data-product-id="{{ $product->id }}">
                            <p class="text-gray-600 mb-0 text-line-3" style="max-width: 250px; margin: 0 auto;">
                                {{ Str::limit(strip_tags($product->short_description), 100) }}
                            </p>
                        </td>
                        @endforeach
                        
                        @for($i = $compares->count(); $i < 4; $i++)
                        <td class="px-6 px-lg-9 text-center text-gray-400 empty-cell">-</td>
                        @endfor
                    </tr>
                    
                    {{-- Action Row --}}
                    <tr>
                        <th class="bg-gray-100"></th>
                        
                        @foreach($compares as $compare)
                        @php 
                            $product = $compare->product;
                            $hasStock = $product->stock >= 1;
                        @endphp
                        <td class="px-6 text-center py-7 action-cell" data-product-id="{{ $product->id }}">
                            @if($hasStock)
                                <a href="javascript:void(0)" 
                                   class="btn btn-main rounded-8 py-13 px-24 add_to_cart w-100"
                                   data-product-id="{{ $product->id }}">
                                    Add To Cart <i class="ph ph-shopping-cart ms-2"></i>
                                </a>
                            @else
                                <a href="javascript:void(0)" 
                                   class="btn btn-secondary rounded-8 py-13 px-24 w-100 opacity-50 disabled"
                                   style="cursor: not-allowed;">
                                    Out of Stock <i class="ph ph-shopping-cart ms-2"></i>
                                </a>
                            @endif
                        </td>
                        @endforeach
                        
                        @for($i = $compares->count(); $i < 4; $i++)
                        <td class="px-6 text-center py-7 empty-cell"></td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- Bottom Actions --}}
        <div class="flex-between flex-wrap gap-16 mt-32">
            <a href="{{ route('products') }}" class="btn btn-outline-main rounded-8 py-13 px-32">
                <i class="ph ph-arrow-left me-2"></i> Continue Shopping
            </a>
            
            @if($compares->count() > 0)
            <a href="javascript:void(0)" id="clear-all-compare" class="btn btn-danger rounded-8 py-13 px-32">
                <i class="ph-bold ph-trash me-2"></i> Clear All
            </a>
            @endif
        </div>
        
        @else
        {{-- Empty Compare List --}}
        <div class="empty-compare text-center py-80">
            <div class="mb-24">
                <i class="ph-bold ph-recycle fs-80 text-gray-300"></i>
            </div>
            <h4 class="mt-4 mb-3">Your compare list is empty</h4>
            <p class="text-gray-600 mb-32">You haven't added any products to compare yet.</p>
            <a href="{{ route('products') }}" class="btn btn-main rounded-8 py-13 px-32">
                Start Shopping <i class="ph ph-arrow-right ms-2"></i>
            </a>
        </div>
        @endif
    </div>
</section>
<!-- Compare Section End -->
@endsection