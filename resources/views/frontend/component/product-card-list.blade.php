@php
    use Carbon\Carbon;
    
    // ===== Sale Logic =====
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
    
    // ===== Price Calculation =====
    $originalPrice = $product->product_price;
    $displayPrice = $originalPrice;
    $showStrikeThrough = false;
    $showSalePrice = false;
    $discountPercent = 0;
    
    if ($hasActiveSale && $salePrice) {
        $displayPrice = $salePrice;
        $showStrikeThrough = true;
        $showSalePrice = true;
        $discountPercent = round((($originalPrice - $salePrice) / $originalPrice) * 100);
    } elseif ($product->product_discount && !$hasActiveSale) {
        $displayPrice = $product->product_price * (1 - $product->product_discount / 100);
        $showStrikeThrough = true;
        $discountPercent = round($product->product_discount);
    }
    
    // 👇 CHECK IF PRODUCT IS NORMAL GLASSES
    $isNormalGlasses = $product->isNormalGlasses();
    
    // ===== Rating =====
    $productRatingData = $productRatings[$product->id] ?? [];
    $avgRating = collect($productRatingData)->avg('rating') ?? 0;
    $totalReviews = collect($productRatingData)->count();
    $totalReviewsFormatted = $totalReviews > 1000 ? round($totalReviews/1000, 1) . 'k' : $totalReviews;
    
    // ===== Stock & Vendor =====
    $hasStock = $product->stock >= 1;
    $vendorName = $product->vendor->store_name ?? 'Store';
    $currency = $genralsetting->currency ?? '$';
    
    // ===== Countdown =====
    $uniqueId = uniqid();
    $countdownId = 'countdown-' . $product->id . '-' . $uniqueId;
    $showCountdown = ($hasActiveSale && $product->sale_end_date);
    $saleEndTimestamp = $showCountdown ? Carbon::parse($product->sale_end_date)->timestamp : null;
    
    // ===== Sold Progress =====
    // $soldCount = $product->sold_count ?? rand(18, 35);
    // $totalStock = $product->stock + $soldCount;
    // $soldPercentage = $totalStock > 0 ? round($soldCount / $totalStock * 100) : 35;
@endphp


<!-- ===== LIST VIEW CARD - LEFT IMAGE, RIGHT CONTENT ===== -->
<div class="listview-card-wrapper" style="width: 100%; margin-bottom: 20px;">
    <div style="display: flex; background: #fff; border: 1px solid #e9ecef; border-radius: 16px; padding: 16px; transition: all 0.3s; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
        
        <div style="width: 40%; position: relative;">
            <a href="{{ route('product.details', $product->product_slug) }}" style="display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px; height: 200px; overflow: hidden;">
                <img src="{{ asset('storage/' . $product->thumbnail_image) }}" alt="{{ $product->product_name }}" style="max-width: 90%; max-height: 180px; object-fit: contain;">
            </a>
            
            <div style="position: absolute; left: 12px; top: 12px; display: flex; flex-direction: column; gap: 8px;">
                @if($product->product_discount && !$hasActiveSale)
                    <span style="background: #28a745; color: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 12px; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        -{{ $discountPercent }}%
                    </span>
                @endif
                
                @if($hasActiveSale)
                    <span style="background: #dc3545; color: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 12px; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        SALE
                    </span>
                @endif
                
                @if($product->is_featured)
                    <span style="background: #ffc107; color: #000; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 12px; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        HOT
                    </span>
                @endif
                
                {{-- Glasses Badge --}}
                @if($isNormalGlasses)
                    <span style="background: #0d6efd; color: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 12px; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"
                          title="Prescription Required">
                        <i class="ph-bold ph-eyeglasses"></i>
                    </span>
                @endif
            </div>
            
            @if($showCountdown)
            <div style="position: absolute; bottom: 10px; left: 0; right: 0; text-align: center;" 
                 id="{{ $countdownId }}" 
                 data-end-date="{{ $saleEndTimestamp }}">
                <ul style="display: flex; justify-content: center; gap: 5px; list-style: none; padding: 0; margin: 0;">
                    <li style="background: rgba(0,0,0,0.7); color: white; padding: 5px 8px; border-radius: 6px; font-size: 12px; min-width: 40px; text-align: center;">
                        <span class="days" style="display: block; font-size: 16px; font-weight: 600; color: #28a745;">0</span>
                        <span style="font-size: 10px;">Days</span>
                    </li>
                    <li style="background: rgba(0,0,0,0.7); color: white; padding: 5px 8px; border-radius: 6px; font-size: 12px; min-width: 40px; text-align: center;">
                        <span class="hours" style="display: block; font-size: 16px; font-weight: 600; color: #28a745;">0</span>
                        <span style="font-size: 10px;">Hours</span>
                    </li>
                    <li style="background: rgba(0,0,0,0.7); color: white; padding: 5px 8px; border-radius: 6px; font-size: 12px; min-width: 40px; text-align: center;">
                        <span class="minutes" style="display: block; font-size: 16px; font-weight: 600; color: #28a745;">0</span>
                        <span style="font-size: 10px;">Mins</span>
                    </li>
                    <li style="background: rgba(0,0,0,0.7); color: white; padding: 5px 8px; border-radius: 6px; font-size: 12px; min-width: 40px; text-align: center;">
                        <span class="seconds" style="display: block; font-size: 16px; font-weight: 600; color: #28a745;">0</span>
                        <span style="font-size: 10px;">Secs</span>
                    </li>
                </ul>
            </div>
            @endif
        </div>
        
        <div style="width: 60%; padding-left: 20px;">
            
            <h6 style="font-size: 18px; font-weight: 600; margin: 0 0 8px 0; line-height: 1.4;">
                <a href="{{ route('product.details', $product->product_slug) }}" style="color: #1a1a1a; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $product->product_name }}
                </a>
            </h6>
            
            <span style="display: inline-block; background: #e8f5e9; color: #28a745; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; margin-bottom: 12px;">
                <i class="ph-fill ph-storefront me-1"></i> {{ $vendorName }}
            </span>
            
            <!-- Rating Row -->
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                <div style="display: flex; align-items: center; gap: 2px;">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($avgRating))
                            <span style="font-size: 14px; color: #ffc107;"><i class="ph-fill ph-star"></i></span>
                        @else
                            <span style="font-size: 14px; color: #dee2e6;"><i class="ph-fill ph-star"></i></span>
                        @endif
                    @endfor
                </div>
                <span style="font-size: 14px; color: #6c757d; font-weight: 500;">{{ number_format($avgRating, 1) }}</span>
                <span style="font-size: 14px; color: #6c757d; font-weight: 500;">({{ $totalReviews }})</span>
            </div>

        <!-- Price Row -->
<div style="margin-bottom: 20px;">
    @if($showStrikeThrough)
        <span style="color: #adb5bd; font-size: 16px; font-weight: 600; text-decoration: line-through; margin-right: 10px;">
            {{ getUserCurrency() }}{{ number_format(convertPrice($originalPrice), 2) }}
        </span>
    @endif
    <span style="color: #1a1a1a; font-size: 18px; font-weight: 700; {{ $showSalePrice ? 'color: #28a745;' : '' }}">
        {{ getUserCurrency() }}{{ number_format(convertPrice($displayPrice), 2) }}
    </span>
</div>

            {{-- 👇 CONDITIONAL BUTTON --}}
            @if($hasStock)
                @if($isNormalGlasses)
                    {{-- Normal Glasses - View Details Button --}}
                    <a href="{{ route('product.details', $product->product_slug) }}" 
                       class="btn bg-primary text-white py-11 px-24 rounded-8 d-flex align-items-center justify-content-center gap-8 fw-medium w-100"
                       style="background: #0d6efd; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 500; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; cursor: pointer; transition: all 0.3s;"
                       onmouseover="this.style.background='#0b5ed7';"
                       onmouseout="this.style.background='#0d6efd';">
                        <i class="ph ph-eye me-1"></i> View Details
                    </a>
                @else
                    {{-- Regular Products - Add to Cart Button --}}
                    <a href="javascript:void(0)" 
                       class="product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 d-flex align-items-center justify-content-center gap-8 fw-medium w-100 add_to_cart"
                       data-product-id="{{ $product->id }}"
                       style="background: #f8f9fa; color: #1a1a1a; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 500; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; cursor: pointer; transition: all 0.3s;"
                       onmouseover="this.style.background='#28a745'; this.style.color='white';"
                       onmouseout="this.style.background='#f8f9fa'; this.style.color='#1a1a1a';"
                       tabindex="0">
                        Add To Cart <i class="ph ph-shopping-cart"></i>
                    </a>
                @endif
            @else
                {{-- Out of Stock --}}
                <a href="javascript:void(0)" 
                   class="btn bg-secondary text-white py-11 px-24 rounded-8 d-flex align-items-center justify-content-center gap-8 fw-medium w-100 opacity-50 disabled"
                   style="background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 500; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; cursor: not-allowed; opacity: 0.5;"
                   tabindex="-1">
                    Out of Stock <i class="ph ph-shopping-cart"></i>
                </a>
            @endif
        </div>
    </div>
</div>

<style>
.listview-card-wrapper > div:hover {
    border-color: #28a745 !important;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}
</style>