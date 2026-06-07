@php
    use Carbon\Carbon;
    $productList = isset($products) ? $products : (isset($prd) ? [$prd] : []);
@endphp

@foreach ($productList as $product)
    @php
        // ===== Sale Price Logic =====
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
        
        // Price calculation
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

        // 👇 CHECK IF PRODUCT IS NORMAL GLASSES
        $isNormalGlasses = $product->isNormalGlasses();

        // Rating
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

    <div class="col-xl-4 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-duration="200">
        <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 mb-3 d-flex flex-column">
            
            <!-- Product Image Thumb -->
            <div class="product-card__thumb rounded-8 bg-gray-50 position-relative" style="height: 250px; overflow: hidden; flex-shrink: 0;">
                <a href="/product/{{ $product->product_slug }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <img src="{{ asset('storage/' . $product->thumbnail_image) }}" 
                         class="img-fluid" 
                         style="max-height: 100%; max-width: 100%; width: auto; height: auto; object-fit: contain;"
                         alt="{{ $product->product_name }}">
                </a>
                
                <!-- Discount & Hot Badges -->
                <div class="position-absolute top-0 start-0 mt-12 ms-12 z-1 d-flex flex-column gap-6">
                    @if($product->product_discount && !$hasActiveSale)
                        <span class="text-main-two-600 w-32 h-32 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-xs fw-semibold" style="font-size: 10px;">
                            -{{ $discountPercent }}%
                        </span>
                    @endif
                    
                    @if($hasActiveSale)
                        <span class="text-success w-32 h-32 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-xs fw-semibold" style="font-size: 10px;">
                            SALE
                        </span>
                    @endif
                    
                    @if($product->is_featured)
                        <span class="text-neutral-600 w-32 h-32 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-xs fw-semibold" style="font-size: 10px;">
                            HOT
                        </span>
                    @endif
                    
                    {{-- Glasses Badge --}}
                    @if($isNormalGlasses)
                        <span class="text-primary w-32 h-32 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-xs fw-semibold"
                              title="Prescription Required">
                            <i class="ph-bold ph-eyeglasses" style="font-size: 14px;"></i>
                        </span>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="position-absolute top-0 end-0 mt-12 me-12 z-1 d-flex flex-column gap-6">
                    <button type="button" 
                        class="w-32 h-32 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-neutral-600 hover-bg-main-two-600 hover-text-white border-0 wishlist-toggle" 
                        data-product-id="{{ $product->id }}"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-title="Add to Wishlist">
                        <i class="ph-bold ph-heart fs-6 wishlist-icon"></i>
                    </button>
                    
                    <button type="button" 
                            class="w-32 h-32 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-neutral-600 hover-bg-main-two-600 hover-text-white border-0 quick-view-btn" 
                            data-id="{{ $product->id }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#quickViewModal"
                            title="Quick View">
                        <i class="ph-bold ph-eye fs-6"></i>
                    </button>
                    
                    <button type="button" 
                            class="compare-toggle w-32 h-32 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm text-neutral-600 hover-bg-main-two-600 hover-text-white border-0 compare-btn"
                            data-product-id="{{ $product->id }}"
                            data-bs-toggle="tooltip"
                            title="Compare">
                        <i class="ph ph-shuffle fs-6"></i>
                    </button>
                </div>

                <!-- Countdown -->
                @if($showCountdown)
                <div class="countdown position-absolute start-50 translate-middle-x bottom-0 mb-12 w-100" 
                     id="{{ $countdownId }}" 
                     data-end-date="{{ $saleEndTimestamp }}">
                    <ul class="countdown-list style-four d-flex justify-content-center flex-wrap gap-4">
                        <li style="width: 40px!important; height: auto;" class="countdown-list__item d-flex flex-column align-items-center text-xs fw-medium text-white rounded-lg bg-neutral-600 px-2 py-1">
                            <span class="days text-md text-main-two-600 fw-medium">0</span>Days
                        </li>
                        <li style="width: 40px!important; height: auto;" class="countdown-list__item d-flex flex-column align-items-center text-xs fw-medium text-white rounded-lg bg-neutral-600 px-2 py-1">
                            <span class="hours text-md text-main-two-600 fw-medium">0</span>Hour
                        </li>
                        <li style="width: 40px!important; height: auto;" class="countdown-list__item d-flex flex-column align-items-center text-xs fw-medium text-white rounded-lg bg-neutral-600 px-2 py-1">
                            <span class="minutes text-md text-main-two-600 fw-medium">0</span>Min
                        </li>
                        <li style="width: 40px!important; height: auto;" class="countdown-list__item d-flex flex-column align-items-center text-xs fw-medium text-white rounded-lg bg-neutral-600 px-2 py-1">
                            <span class="seconds text-md text-main-two-600 fw-medium">0</span>Sec
                        </li>
                    </ul>
                </div>
                @endif
            </div>

            <!-- Product Content - REDUCED SPACING -->
            <div class="product-card__content w-100 d-flex flex-column" style="flex: 1; padding-top: 8px;">
                <!-- Product Title -->
                <h6 class="title text-md fw-semibold" style="margin: 5px 0 0 0 !important; min-height: 40px; overflow: hidden; font-size: 14px;">
                    <a href="/product/{{ $product->product_slug }}" class="link text-line-2" tabindex="0">
                        {{ $product->product_name }}
                    </a>
                </h6>

                <!-- Rating Stars -->
                <div class="d-flex align-items-center gap-4" style="min-height: 20px; margin-top: 5px;">
                    <div class="d-flex align-items-center gap-4">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                                <span class="text-xs fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star" style="font-size: 10px;"></i></span>
                            @else
                                <span class="text-xs fw-medium text-gray-300 d-flex"><i class="ph-fill ph-star" style="font-size: 10px;"></i></span>
                            @endif
                        @endfor
                    </div>
                    <span class="text-xs fw-medium text-gray-500" style="font-size: 10px;">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-xs fw-medium text-gray-500" style="font-size: 10px;">({{ $totalReviews > 0 ? $totalReviews : '0' }})</span>
                </div>

                <!-- Vendor Name -->
                <span class="py-1 px-6 text-xs rounded-pill text-main-two-600 bg-main-two-50 d-inline-block" style="margin-top: 6px; font-size: 10px; width: fit-content;">
                    <i class="ph-fill ph-storefront me-1" style="font-size: 10px;"></i> {{ $vendorName }}
                </span>

             <!-- Price Section -->
<div class="product-card__price mt-8 mb-12" style="min-height: 38px;">
    @if($showStrikeThrough)
        <span class="text-gray-400 text-sm fw-semibold text-decoration-line-through d-block" style="font-size: 12px;">
            {{ getUserCurrency() }} {{ number_format(convertPrice($originalPrice), 2) }}
        </span>
        <span class="text-heading text-sm fw-semibold {{ $showSalePrice ? 'text-success' : '' }}" style="font-size: 14px;">
            {{ getUserCurrency() }} {{ number_format(convertPrice($displayPrice), 2) }}
            @if($showSalePrice)
                <span class="text-success fw-normal" style="font-size: 10px;"> (Sale)</span>
            @endif
        </span>
    @else
        <span class="text-heading text-sm fw-semibold {{ $showSalePrice ? 'text-success' : '' }}" style="font-size: 14px;">
            {{ getUserCurrency() }} {{ number_format(convertPrice($displayPrice), 2) }}
            @if($product->product_discount && !$hasActiveSale)
                <span class="text-gray-500 fw-normal" style="font-size: 10px;"> (-{{ $discountPercent }}%)</span>
            @endif
        </span>
    @endif
</div>

                {{-- Button Section --}}
                <div style="margin-top: auto;">
                    @if($hasStock)
                        @if($isNormalGlasses)
                            <a href="/product/{{ $product->product_slug }}" 
                               class="product-card__cart btn bg-primary text-white hover-bg-main-600 hover-text-white py-6 px-20 rounded-8 d-flex align-items-center justify-content-center gap-6 fw-medium w-100"
                               style="font-size: 12px; padding: 6px 12px;">
                                <i class="ph ph-eye me-1" style="font-size: 12px;"></i> View Details
                            </a>
                        @else
                            <a href="javascript:void(0)" 
                               class="product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-6 px-20 rounded-8 d-flex align-items-center justify-content-center gap-6 fw-medium w-100 add_to_cart"
                               data-product-id="{{ $product->id }}"
                               style="font-size: 12px; padding: 6px 12px;">
                                Add To Cart <i class="ph ph-shopping-cart" style="font-size: 12px;"></i>
                            </a>
                        @endif
                    @else
                        <a href="javascript:void(0)" 
                           class="product-card__cart btn bg-secondary text-white py-6 px-20 rounded-8 d-flex align-items-center justify-content-center gap-6 fw-medium w-100 opacity-50 disabled"
                           style="cursor: not-allowed; font-size: 12px; padding: 6px 12px;" tabindex="-1">
                            Out of Stock <i class="ph ph-shopping-cart" style="font-size: 12px;"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach