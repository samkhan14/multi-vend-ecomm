@php
    use Carbon\Carbon;
    
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
    
    if ($hasActiveSale && $salePrice) {
        $displayPrice = $salePrice;
        $showStrikeThrough = true;
    } elseif ($product->product_discount && !$hasActiveSale) {
        $displayPrice = $product->product_price * (1 - $product->product_discount / 100);
        $showStrikeThrough = true;
    }
    
    $defaultImagesUrls = [];
    $variantImagesMap = []; 
    
    if($product->thumbnail_image) {
        $defaultImagesUrls[] = asset('storage/'.$product->thumbnail_image);
    }
    
    if($product->images && $product->images->isNotEmpty()) {
        foreach($product->images as $img) {
            $defaultImagesUrls[] = asset('storage/'.$img->image);
        }
    }
    
    if($product->productVariants && $product->productVariants->isNotEmpty()) {
        foreach($product->productVariants as $variant) {
            $variantImagesMap[$variant->id] = [];
            if($variant->images && $variant->images->isNotEmpty()) {
                foreach($variant->images as $img) {
                    $variantImagesMap[$variant->id][] = asset('storage/'.$img->image);
                }
            }
        }
    }
    
    if((empty($product->images) || $product->images->isEmpty()) && !empty($variantImagesMap)) {
        $firstVariantId = array_key_first($variantImagesMap);
        if(!empty($variantImagesMap[$firstVariantId])) {
            $defaultImagesUrls = array_merge($defaultImagesUrls, $variantImagesMap[$firstVariantId]);
        }
    }
    
    $defaultImagesUrls = array_unique($defaultImagesUrls);
    
    // Prepare variant data for JavaScript
    $variantData = [];
    $attributeGroups = [];
    
    foreach($product->productVariants as $variant) {
        $variantDiscount = 0;
        if($variant->sale_price && $variant->sale_price < $variant->price) {
            $variantDiscount = round((($variant->price - $variant->sale_price) / $variant->price) * 100);
        }
        
        $variantImages = [];
        if($variant->images && $variant->images->isNotEmpty()) {
            foreach($variant->images as $img) {
                $variantImages[] = asset('storage/'.$img->image);
            }
        }
        
        $variantAttributes = [];
        
        foreach($variant->variantValues as $vv) {
            if($vv->variantValue) {
                $attrName = strtolower($vv->variant->name ?? '');
                $attrValue = $vv->variantValue->value;
                $variantAttributes[$attrName] = $attrValue;
            }
        }
        
$variantData[$variant->id] = [
    'id' => $variant->id,
    'images' => !empty($variantImages) ? $variantImages : $defaultImagesUrls,
    'price' => $variant->price,
    'sale_price' => $variant->sale_price,
    'discount' => $variantDiscount,
    'stock' => $variant->stock,
    'sku' => $variant->sku,
    'attributes' => $variantAttributes
];
        foreach($variantAttributes as $attrName => $attrValue) {
            if (!isset($attributeGroups[$attrName])) {
                $attributeGroups[$attrName] = [];
            }
            if (!in_array($attrValue, $attributeGroups[$attrName])) {
                $attributeGroups[$attrName][] = $attrValue;
            }
        }
    }
    
    $colorValues = $attributeGroups['color'] ?? [];
    $sizeValues = $attributeGroups['size'] ?? [];
    $otherAttributes = array_diff_key($attributeGroups, array_flip(['color', 'size']));
    
    // Color mapping
    $colorMap = [
        'red' => '#ef4444', 'blue' => '#3b82f6', 'green' => '#22c55e',
        'yellow' => '#eab308', 'black' => '#000000', 'white' => '#ffffff',
        'purple' => '#a855f7', 'orange' => '#f97316', 'pink' => '#ec4899',
        'brown' => '#92400e', 'gray' => '#6b7280', 'grey' => '#6b7280',
        'navy' => '#1e3a8a', 'maroon' => '#991b1b', 'teal' => '#14b8a6',
    ];
@endphp

<!-- ========== QUICK VIEW MODERN UI ========== -->
<style>
/* Modern Quick View Styles */
.qv-modern {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

/* Image Gallery */
.qv-main-image {
    border-radius: 24px;
    background: #f8fafc;
    padding: 20px;
    transition: all 0.3s ease;
}

.qv-thumb-item {
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
    width: 80px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qv-thumb-item:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.qv-thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}

/* Typography */
.qv-title {
    font-size: 24px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 8px;
    line-height: 1.3;
}

.qv-price {
    font-size: 32px;
    font-weight: 700;
    color: #2563eb;
    line-height: 1.2;
}

.qv-old-price {
    font-size: 18px;
    color: #94a3b8;
    text-decoration: line-through;
    margin-left: 12px;
}

.qv-discount-badge {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.qv-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    font-size: 14px;
}

.qv-star {
    color: #f59e0b;
    font-size: 16px;
}

.qv-star-empty {
    color: #cbd5e1;
}

.qv-sku {
    background: #f1f5f9;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 13px;
    color: #475569;
}

/* Variant Buttons */
.qv-section-title {
    font-size: 15px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 12px;
    letter-spacing: 0.3px;
}

.qv-color-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 3px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.qv-color-btn:hover:not([disabled]) {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #3b82f6;
}

.qv-color-btn.active {
    border-color: #2563eb;
    border-width: 3px;
    box-shadow: 0 0 0 2px white, 0 0 0 4px #2563eb;
}

.qv-color-btn[disabled] {
    opacity: 0.5;
    cursor: not-allowed;
}

.qv-size-btn {
    min-width: 20px;
    padding: 10px 10px;
    border: 2px solid #e2e8f0;
    border-radius: 40px;
    background: white;
    font-weight: 500;
    color: #334155;
    transition: all 0.2s ease;
}

.qv-size-btn:hover:not([disabled]) {
    border-color: #3b82f6;
    background: #eff6ff;
    color: #2563eb;
}

.qv-size-btn.active {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

.qv-size-btn[disabled] {
    opacity: 0.5;
    cursor: not-allowed;
}

.qv-attr-btn {
    padding: 8px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 40px;
    background: white;
    font-weight: 500;
    color: #334155;
    transition: all 0.2s ease;
}

.qv-attr-btn:hover:not([disabled]) {
    border-color: #3b82f6;
    background: #eff6ff;
    color: #2563eb;
}

.qv-attr-btn.active {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

/* Stock Status */
.qv-stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 500;
}

.qv-stock-in {
    background: #dcfce7;
    color: #166534;
}

.qv-stock-out {
    background: #fee2e2;
    color: #991b1b;
}

/* Quantity Input */
.qv-quantity {
    display: flex;
    align-items: center;
    border: 2px solid #e2e8f0;
    border-radius: 40px;
    overflow: hidden;
    width: fit-content;
}

.qv-qty-btn {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: none;
    font-size: 18px;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s;
}

.qv-qty-btn:hover {
    background: #f1f5f9;
    color: #2563eb;
}

.qv-qty-input {
    width: 70px;
    height: 44px;
    border: none;
    border-left: 2px solid #e2e8f0;
    border-right: 2px solid #e2e8f0;
    text-align: center;
    font-weight: 500;
    font-size: 16px;
}

.qv-qty-input:focus {
    outline: none;
}

/* Add to Cart Button */
.qv-add-to-cart {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border: none;
    border-radius: 40px;
    padding: 14px 28px;
    font-weight: 600;
    font-size: 16px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    width: 100%;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.qv-add-to-cart:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
}

.qv-add-to-cart:disabled {
    background: #94a3b8;
    box-shadow: none;
    cursor: not-allowed;
}

/* Wishlist */
.qv-wishlist {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    padding: 8px 0;
    transition: all 0.2s;
}

.qv-wishlist:hover {
    color: #dc2626;
}

.qv-wishlist i {
    font-size: 18px;
}

/* Divider */
.qv-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, #e2e8f0, transparent);
    margin: 20px 0;
}

/* Description */
.qv-description {
    color: #475569;
    line-height: 1.6;
    font-size: 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .qv-title {
        font-size: 20px;
    }
    
    .qv-price {
        font-size: 28px;
    }
    
    .qv-color-btn {
        width: 36px;
        height: 36px;
    }
    
    .qv-size-btn {
        min-width: 40px;
        padding: 8px 12px;
    }
}
</style>

<!-- ========== PRODUCT DATA FOR JAVASCRIPT ========== -->
<div id="qv-product-data" 
     data-product-id="{{ $product->id }}"
     data-product-stock="{{ $product->stock }}"
     data-currency="{{ $genralsetting->currency }}"
     data-variants='@json($variantData)'
     style="display: none;"></div>

<!-- ========== MODERN QUICK VIEW CONTENT ========== -->
<div class="qv-modern p-3 p-md-4 position-relative">
        <button type="button" class="btn-close position-absolute" 
            style="top: 5px; right: 15px; z-index: 1050;" 
            data-bs-dismiss="modal" 
            aria-label="Close"></button>

    <div class="row g-4 g-lg-5">
        <!-- Left Column - Images -->
        <div class="col-lg-6">
            <div class="position-sticky" style="top: 20px;">
                <!-- Main Image -->
                <div class="qv-main-image mb-4 text-center">
                    <img src="{{ $defaultImagesUrls[0] ?? asset('assets/images/thumbs/no-image.png') }}" 
                         class="img-fluid" 
                         style="max-height: 350px; width: 100%; object-fit: contain;"
                         alt="{{ $product->product_name }}"
                         id="modalMainProductImage">
                </div>
                
                <!-- Thumbnails -->
                @if(count($defaultImagesUrls) > 1)
                <div class="d-flex flex-wrap gap-3 justify-content-center" id="modalThumbnailContainer">
                    @foreach($defaultImagesUrls as $index => $imageUrl)
                    <div class="qv-thumb-item" 
                         data-image="{{ $imageUrl }}"
                         onclick="document.getElementById('modalMainProductImage').src = this.dataset.image">
                        <img src="{{ $imageUrl }}" alt="">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        
        <!-- Right Column - Details -->
        <div class="col-lg-6">
            <!-- Product Title -->
            <h1 class="qv-title">{{ $product->product_name }}</h1>
            
            <!-- Rating & SKU -->
            <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                <div class="qv-rating">
                    @php $averageRating = $averageRating ?? 0; @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($averageRating))
                            <i class="ph-fill ph-star qv-star"></i>
                        @else
                            <i class="ph-fill ph-star qv-star-empty"></i>
                        @endif
                    @endfor
                    <span>({{ $totalReviews ?? 0 }} reviews)</span>
                </div>
                <span class="qv-sku">SKU: <span id="modalProductSku">{{ $product->product_code ?? 'N/A' }}</span></span>
            </div>
            
            <!-- Price Section -->
         <div class="d-flex align-items-center gap-3 mb-4">
    <span class="qv-price" id="modalCurrentPrice">
        {{ getUserCurrency() }} {{ number_format(convertPrice($displayPrice), 2) }}
    </span>
    
    <!-- @if($showStrikeThrough)
    <span class="qv-old-price" id="modalOriginalPrice">
        {{ getUserCurrency() }} {{ number_format(convertPrice($originalPrice), 2) }}
    </span>
    @endif -->
    
    @if($product->product_discount && !$hasActiveSale)
    <span class="qv-discount-badge" id="modalDiscountBadge">
        <i class="ph ph-lightning"></i> {{ $product->product_discount }}% OFF
    </span>
    @endif
    
    @if($hasActiveSale)
    <span class="qv-discount-badge" id="modalSaleBadge" style="background: linear-gradient(135deg, #16a34a, #15803d);">
        <i class="ph ph-tag"></i> SALE
    </span>
    @endif
</div>
            
            <!-- Description -->
            <p class="qv-description mb-4">{{ $product->short_description ?? $product->long_description ?? 'No description available' }}</p>
            
            <!-- Stock Status -->
            <div class="mb-4">
                @php
                    $totalStock = $product->productVariants->sum('stock') ?: $product->stock;
                @endphp
                <span class="qv-stock-badge {{ $totalStock > 0 ? 'qv-stock-in' : 'qv-stock-out' }}" id="modalStockStatus">
                    <i class="ph {{ $totalStock > 0 ? 'ph-check-circle' : 'ph-x-circle' }}"></i>
                    <span id="modalStockText">{{ $totalStock > 0 ? 'In Stock' : 'Out of Stock' }}</span>
                    <span id="modalStockQuantity">({{ $totalStock }} available)</span>
                </span>
            </div>
            
            <!-- ========== VARIANTS SELECTION ========== -->
            @if($product->productVariants && $product->productVariants->isNotEmpty())
            <div class="mb-4">
                <input type="hidden" id="modalSelectedVariantId" value="">
                
                {{-- COLOR ATTRIBUTES --}}
                @if(!empty($colorValues))
                <div class="mb-4" data-attribute="color">
                    <h6 class="qv-section-title">Select Color</h6>
                    <div class="d-flex flex-wrap gap-3" id="colorButtonsContainer">
                        @foreach($colorValues as $colorValue)
                            @php
                                $hasStock = false;
                                foreach($variantData as $vid => $vdata) {
                                    if(isset($vdata['attributes']['color']) && $vdata['attributes']['color'] == $colorValue && $vdata['stock'] > 0) {
                                        $hasStock = true;
                                        break;
                                    }
                                }
                                
                                $colorCode = $colorMap[strtolower(trim($colorValue))] ?? '#cccccc';
                            @endphp
                            
                            <button type="button" 
                                    class="qv-color-btn"
                                    style="background-color: {{ $colorCode }};"
                                    title="{{ $colorValue }}"
                                    data-attribute="color"
                                    data-value="{{ $colorValue }}"
                                    data-color="{{ $colorValue }}"
                                    {{ !$hasStock ? 'disabled' : '' }}>
                                @if(!$hasStock)
                                <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 2px; height: 20px; background: #ef4444; rotate: 45deg;"></span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif
                
                {{-- SIZE ATTRIBUTES --}}
                @if(!empty($sizeValues))
                <div class="mb-4" data-attribute="size">
                    <h6 class="qv-section-title">Select Size</h6>
                    <div class="d-flex flex-wrap gap-3" id="sizeButtonsContainer">
                        @foreach($sizeValues as $sizeValue)
                            @php
                                $hasStock = false;
                                foreach($variantData as $vid => $vdata) {
                                    if(isset($vdata['attributes']['size']) && $vdata['attributes']['size'] == $sizeValue && $vdata['stock'] > 0) {
                                        $hasStock = true;
                                        break;
                                    }
                                }
                            @endphp
                            
                            <button type="button" 
                                    class="qv-size-btn"
                                    data-attribute="size"
                                    data-value="{{ $sizeValue }}"
                                    data-size="{{ $sizeValue }}"
                                    {{ !$hasStock ? 'disabled' : '' }}>
                                {{ $sizeValue }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif
                
                {{-- OTHER ATTRIBUTES --}}
                @foreach($otherAttributes as $attrName => $attrValues)
                <div class="mb-4" data-attribute="{{ $attrName }}">
                    <h6 class="qv-section-title">Select {{ ucfirst($attrName) }}</h6>
                    <div class="d-flex flex-wrap gap-3" id="{{ $attrName }}ButtonsContainer">
                        @foreach($attrValues as $attrValue)
                            @php
                                $hasStock = false;
                                foreach($variantData as $vid => $vdata) {
                                    if(isset($vdata['attributes'][$attrName]) && $vdata['attributes'][$attrName] == $attrValue && $vdata['stock'] > 0) {
                                        $hasStock = true;
                                        break;
                                    }
                                }
                            @endphp
                            
                            <button type="button" 
                                    class="qv-attr-btn"
                                    data-attribute="{{ $attrName }}"
                                    data-value="{{ $attrValue }}"
                                    {{ !$hasStock ? 'disabled' : '' }}>
                                {{ $attrValue }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
                
                <div class="mt-3 text-muted small" id="modalNoVariantMessage"></div>
            </div>
            @endif
            
            <!-- Quantity and Add to Cart -->
            <div class="row g-3 align-items-end mb-4">
                <div class="col-auto">
                    <h6 class="qv-section-title mb-2">Quantity</h6>
                    <div class="qv-quantity">
                        <button type="button" class="qv-qty-btn" id="modalQtyDown">−</button>
                        <input type="number" class="qv-qty-input" id="modalQuantityInput" value="1" min="1" max="{{ $product->stock }}" data-stock="{{ $product->stock }}">
                        <button type="button" class="qv-qty-btn" id="modalQtyUp">+</button>
                    </div>
                </div>
                <div class="col">
                    <h6 class="qv-section-title mb-2">&nbsp;</h6>
               {{-- Quick View Modal Button --}}
@if($product->stock > 0)
    @php
        $isNormalGlasses = $product->isNormalGlasses();
    @endphp
    
    @if($isNormalGlasses)
        {{-- Normal Glasses - View Details Button --}}
        <a href="{{ route('product.details', $product->product_slug) }}" 
           class="qv-add-to-cart"
           style="display: flex; align-items: center; justify-content: center; gap: 8px; background: #0d6efd; color: white; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 500;">
            <i class="ph ph-eye"></i>
            View Details
        </a>
    @else
        {{-- Regular Products - Add to Cart Button --}}
        <button class="qv-add-to-cart" id="modalAddToCartBtn" data-product-id="{{ $product->id }}">
            <i class="ph ph-shopping-cart-simple"></i>
            Add to Cart
        </button>
    @endif
@else
    <button class="qv-add-to-cart" style="background: #94a3b8;" disabled>
        <i class="ph ph-shopping-cart-simple"></i>
        Out of Stock
    </button>
@endif
                </div>
            </div>
            
            <!-- Wishlist -->
            <a href="javascript:void(0)" class="qv-wishlist wishlist-toggle" data-product-id="{{ $product->id }}">
                <i class="ph-fill ph-heart"></i>
                Add to Wishlist
            </a>
        </div>
    </div>
</div>
