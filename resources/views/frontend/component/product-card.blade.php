@php
    use Carbon\Carbon;
    $productList = isset($products) ? $products : (isset($prd) ? [$prd] : []);
@endphp

@foreach ($productList as $product)
    @php
        $currentDate = Carbon::now();
        $hasActiveSale = false;
        $salePrice = null;
        
        if ($product->sale_price) {
            $saleStart = $product->sale_start_date ? Carbon::parse($product->sale_start_date) : null;
            $saleEnd = $product->sale_end_date ? Carbon::parse($product->sale_end_date) : null;
            
            $hasActiveSale = (
                $saleStart && $currentDate >= $saleStart &&
                (!$saleEnd || $currentDate <= $saleEnd)
            );
            
            if ($hasActiveSale) {
                $salePrice = $product->sale_price;
            }
        }
        
        $originalPrice = $product->product_price;
        $displayPrice = $originalPrice;
        $showStrikeThrough = false;
        $showSalePrice = false;
        
        if ($hasActiveSale && $salePrice) {
            $displayPrice = $salePrice;
            $showStrikeThrough = true;
            $showSalePrice = true;
        } elseif ($product->product_discount && !$hasActiveSale) {
            $displayPrice = $product->product_price * (1 - $product->product_discount / 100);
            $showStrikeThrough = true;
        }
        
        // 👇 CHECK IF PRODUCT IS GLASSES
        $isGlasses = false;
        $glassesCategory = \App\Models\Category::whereRaw('LOWER(category_name) = ?', ['glasses'])->first();
        
        if ($glassesCategory && $product->category) {
            $cat = $product->category;
            while ($cat) {
                if ($cat->id == $glassesCategory->id) {
                    $isGlasses = true;
                    break;
                }
                $cat = $cat->parent;
            }
        }
        
        $isNormalGlasses = $isGlasses && ($product->product_type ?? 'normal') === 'normal';
        
        // Rating from AppServiceProvider
        $productRatingData = $productRatings[$product->id] ?? [];
        $avgRating = collect($productRatingData)->avg('rating') ?? 0;
        $totalReviews = collect($productRatingData)->count();

        $hasStock = $product->stock >= 1;
        $vendorName = $product->vendor->store_name ?? 'Store';
        $currency = $genralsetting->currency ?? '$';
        
        $discountPercent = $product->product_discount ? round($product->product_discount) : 0;
        
        $uniqueId = uniqid();
        $countdownId = 'countdown-' . $product->id . '-' . $uniqueId;
        
        $showCountdown = ($hasActiveSale && $product->sale_end_date);
        $saleEndTimestamp = $showCountdown ? Carbon::parse($product->sale_end_date)->timestamp : null;
    @endphp

    <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-duration="200">
        <div class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 mb-4">
            
            <div class="product-card__thumb rounded-8 bg-gray-50 position-relative" style="height: 280px; overflow: hidden;">
                <a href="/product/{{ $product->product_slug }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <img src="{{ asset('storage/' . $product->thumbnail_image) }}" 
                         class="img-fluid" 
                         style="max-height: 100%; max-width: 100%; width: auto; height: auto; object-fit: contain;"
                         alt="{{ $product->product_name }}">
                </a>
                
                <!-- Discount & Hot Badges -->
                <div class="position-absolute top-0 start-0 mt-16 ms-16 z-1 d-flex flex-column gap-8">
                    @if($product->product_discount && !$hasActiveSale)
                        <span class="text-main-two-600 w-40 h-40 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-xs fw-semibold">
                            -{{ $discountPercent }}%
                        </span>
                    @endif
                    
                    @if($hasActiveSale)
                        <span class="text-success w-40 h-40 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-xs fw-semibold">
                            SALE
                        </span>
                    @endif
                    
                    @if($product->is_featured)
                        <span class="text-neutral-600 w-40 h-40 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-xs fw-semibold">
                            HOT
                        </span>
                    @endif
                    
                    {{--  NEW: Glasses Badge --}}
                    @if($isNormalGlasses)
                         <button type="button" 
                            class="w-40 h-40 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-neutral-600 hover-bg-main-two-600 hover-text-white border-0 " 
                         
                            title="Prescription Required">
                        <i class="ph-bold ph-eyeglasses fs-5"></i>
                    </button>
                    @endif
                </div>

                <!-- DIRECT ACTION BUTTONS -->
                <div class="position-absolute top-0 end-0 mt-16 me-16 z-1 d-flex flex-column gap-8">
                    <!-- Wishlist Button -->
                    <button type="button" 
                        class="w-40 h-40 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-neutral-600 hover-bg-main-two-600 hover-text-white border-0 wishlist-toggle" 
                        data-product-id="{{ $product->id }}"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-title="Add to Wishlist">
                        <i class="ph-bold ph-heart fs-5 wishlist-icon"></i>
                    </button>
                    
                    <!-- Quick View Button -->
                    <button type="button" 
                            class="w-40 h-40 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-neutral-600 hover-bg-main-two-600 hover-text-white border-0 quick-view" 
                            data-id="{{ $product->id }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#quickViewModal"
                            title="Quick View">
                        <i class="ph-bold ph-eye fs-5"></i>
                    </button>
                    
                    <!-- Compare Button -->
                    <button type="button" 
                            class="compare-toggle w-40 h-40 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-neutral-600 hover-bg-main-two-600 hover-text-white border-0 compare-btn"
                            data-product-id="{{ $product->id }}"
                            data-bs-toggle="tooltip"
                            title="Compare">
                        <i class="ph ph-shuffle fs-5"></i>
                    </button>
                </div>

                <!-- Countdown -->
                @if($showCountdown)
                <div class="countdown position-absolute start-50 translate-middle-x bottom-0 mb-20 w-100" 
                     id="{{ $countdownId }}" 
                     data-end-date="{{ $saleEndTimestamp }}">
                    <ul class="countdown-list style-four d-flex justify-content-center flex-wrap gap-8">
                        <li class="countdown-list__item d-flex flex-column align-items-center text-sm fw-medium text-white rounded-lg bg-neutral-600 px-3 py-2">
                            <span class="days text-2xl text-main-two-600 fw-medium">0</span>Days
                        </li>
                        <li class="countdown-list__item d-flex flex-column align-items-center text-sm fw-medium text-white rounded-lg bg-neutral-600 px-3 py-2">
                            <span class="hours text-2xl text-main-two-600 fw-medium">0</span>Hour
                        </li>
                        <li class="countdown-list__item d-flex flex-column align-items-center text-sm fw-medium text-white rounded-lg bg-neutral-600 px-3 py-2">
                            <span class="minutes text-2xl text-main-two-600 fw-medium">0</span>Min
                        </li>
                        <li class="countdown-list__item d-flex flex-column align-items-center text-sm fw-medium text-white rounded-lg bg-neutral-600 px-3 py-2">
                            <span class="seconds text-2xl text-main-two-600 fw-medium">0</span>Sec
                        </li>
                    </ul>
                </div>
                @endif
            </div>

            <!-- Product Content -->
            <div class="product-card__content mt-16 w-100">
                <!-- Product Title -->
                <h6 class="title text-lg fw-semibold my-16">
                    <a href="/product/{{ $product->product_slug }}" class="link text-line-2" tabindex="0">
                        {{ $product->product_name }}
                    </a>
                </h6>

                <!-- Rating Stars -->
                <div class="d-flex align-items-center gap-6">
                    <div class="d-flex align-items-center gap-8">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                                <span class="text-xs fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                            @else
                                <span class="text-xs fw-medium text-gray-300 d-flex"><i class="ph-fill ph-star"></i></span>
                            @endif
                        @endfor
                    </div>
                    <span class="text-xs fw-medium text-gray-500">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-xs fw-medium text-gray-500">({{ $totalReviews > 0 ? $totalReviews : '0' }})</span>
                </div>

                <!-- Vendor Name -->
                <span class="py-2 px-8 text-xs rounded-pill text-main-two-600 bg-main-two-50 mt-16 d-inline-block">
                    <i class="ph-fill ph-storefront me-1"></i>  {{ $vendorName }}
                </span>

                <!-- Price -->
     <!-- Price -->
<div class="product-card__price mt-16 mb-30">
    @if($showStrikeThrough)
        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">
            {{ getUserCurrency() }} {{ number_format(convertPrice($originalPrice), 2) }}
        </span>
    @endif
    <span class="text-heading text-md fw-semibold {{ $showSalePrice ? 'text-success' : '' }}">
        {{ getUserCurrency() }} {{ number_format(convertPrice($displayPrice), 2) }}
        @if($showSalePrice)
            <span class="text-success fw-normal"> (Sale)</span>
        @elseif($product->product_discount && !$hasActiveSale)
            <span class="text-gray-500 fw-normal"> (-{{ $discountPercent }}%)</span>
        @endif
    </span>
</div>

                {{-- 👇 CONDITIONAL BUTTON --}}
                @if($hasStock)
                    @if($isNormalGlasses)
                        {{-- Normal Glasses - View Details Button --}}
                        <a href="/product/{{ $product->product_slug }}" 
                           class="product-card__cart btn bg-primary text-white hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 d-flex align-items-center justify-content-center gap-8 fw-medium w-100"
                           tabindex="0">
                            <i class="ph ph-eye me-1"></i> View Details
                        </a>
                    @else
                        {{-- Regular Products - Add to Cart Button --}}
                        <a href="javascript:void(0)" 
                           class="product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 d-flex align-items-center justify-content-center gap-8 fw-medium w-100 add_to_cart"
                           data-product-id="{{ $product->id }}" tabindex="0">
                            Add To Cart <i class="ph ph-shopping-cart"></i>
                        </a>
                    @endif
                @else
                    {{-- Out of Stock - For All Products --}}
                    <a href="javascript:void(0)" 
                       class="product-card__cart btn bg-secondary text-white py-11 px-24 rounded-8 d-flex align-items-center justify-content-center gap-8 fw-medium w-100 opacity-50 disabled"
                       style="cursor: not-allowed;" tabindex="-1">
                        Out of Stock <i class="ph ph-shopping-cart"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach