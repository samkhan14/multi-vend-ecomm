@extends('frontend.layouts.app')

@section('seo')
    <title>{{ $product->meta_title ?? $product->product_name }}</title>
    <meta name="description" content="{{ $product->meta_description }}">
    <meta name="keywords" content="{{ $product->meta_keywords }}">
@endsection

@section('content')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <ul class="flex-align gap-8 flex-wrap">
                <ol class="breadcrumb justify-content-start py-1 mb-0" style="--bs-breadcrumb-divider: '—' !important;">
                    <li class="breadcrumb-item">
                        <a title="Home" href="/" class="text-decoration-none">Home</a>
                    </li>
                    
                    @if($product->category)
                        @php
                            function buildCategoryBreadcrumb($category, &$items = [], &$urlParts = []) {
                                if ($category->parent) {
                                    buildCategoryBreadcrumb($category->parent, $items, $urlParts);
                                }
                                array_push($urlParts, $category->url);
                                $items[] = [
                                    'name' => $category->category_name,
                                    'url' => '/' . implode('/', $urlParts)
                                ];
                                return $items;
                            }
                            
                            $categoryPath = [];
                            $urlParts = [];
                            if ($product->category) {
                                buildCategoryBreadcrumb($product->category, $categoryPath, $urlParts);
                            }
                        @endphp
                        
                        @foreach($categoryPath as $cat)
                            <li class="breadcrumb-item">
                                <a href="/products{{ $cat['url'] }}" class="text-decoration-none">
                                    {{ $cat['name'] }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                    
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $product->product_name }}
                    </li>
                </ol>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->


<!-- ========================== Product Details Two Start =========================== -->
<section class="product-details py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-xl-9">
                <div class="row gy-4">
                    <div class="col-xl-6">
                        <div class="product-details__left">
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
                                $showSalePrice = false;
                                
                                if ($hasActiveSale && $salePrice) {
                                    $displayPrice = $salePrice;
                                    $showStrikeThrough = true;
                                    $showSalePrice = true;
                                } elseif ($product->product_discount && !$hasActiveSale) {
                                    $displayPrice = $product->product_price * (1 - $product->product_discount / 100);
                                    $showStrikeThrough = true;
                                }
                                
                                $showCountdown = ($hasActiveSale && $product->sale_end_date);
                                $saleEndTimestamp = $showCountdown ? Carbon::parse($product->sale_end_date)->timestamp : null;
                                $uniqueId = uniqid();
                                $countdownId = 'countdown-' . $product->id . '-' . $uniqueId;
                                
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
                            @endphp
                            
                            <!-- Main Image Slider -->
                            <div class="product-details__thumb-slider border border-gray-100 rounded-16">
                                @if(!empty($defaultImagesUrls))
                                    @foreach($defaultImagesUrls as $index => $imageUrl)
                                    <div>
                                        <div class="product-details__thumb flex-center h-100">
                                            <img src="{{ $imageUrl }}" alt="{{ $product->product_name }}" class="main-product-image" data-image-index="{{ $index }}">
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div>
                                        <div class="product-details__thumb flex-center h-100">
                                            <img src="{{ asset('assets/images/thumbs/no-image.png') }}" alt="No Image" class="main-product-image">
                                        </div>
                                    </div>
                                @endif
                            </div>

                          
                            @if(count($defaultImagesUrls) > 1)
                            <div class="mt-24">
                                <div class="product-details__images-slider" id="thumbnailContainer" data-product-images='@json($defaultImagesUrls)'>
                                    @foreach($defaultImagesUrls as $imageUrl)
                                    <div>
                                        <div class="max-w-120 max-h-120 h-100 flex-center border border-gray-100 rounded-16 p-8 cursor-pointer thumbnail-item" 
                                             onclick="changeMainImage('{{ $imageUrl }}')">
                                            <img src="{{ $imageUrl }}" alt="" style="max-height: 100px; object-fit: contain;">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right Column - Product Details -->
                    <div class="col-xl-6">
                        <div class="product-details__content">
                            
                            <!--  SPECIAL OFFER COUNTDOWN - Only shows when sale is active -->
                            @if($showCountdown)
                            <div class="flex-center mb-24 flex-wrap gap-16 bg-color-one rounded-8 py-16 px-24 position-relative z-1">
                                <img src="{{ asset('frontend/template/assets/images/bg/details-offer-bg.png') }}" alt="" class="position-absolute inset-block-start-0 inset-inline-start-0 w-100 h-100 z-n1">
                                <div class="flex-align gap-16">
                                    <span class="text-white text-sm">Special Offer:</span>
                                </div>
                                <div class="countdown" id="{{ $countdownId }}" data-end-date="{{ $saleEndTimestamp }}">
                                    <ul class="countdown-list flex-align flex-wrap">
                                        <li class="countdown-list__item text-heading flex-align gap-4 text-xs fw-medium w-30 h-28 rounded-4 border border-main-600 p-0 flex-center">
                                            <span class="days">0</span>d
                                        </li>
                                        <li class="countdown-list__item text-heading flex-align gap-4 text-xs fw-medium w-30 h-28 rounded-4 border border-main-600 p-0 flex-center">
                                            <span class="hours">0</span>h
                                        </li>
                                        <li class="countdown-list__item text-heading flex-align gap-4 text-xs fw-medium w-30 h-28 rounded-4 border border-main-600 p-0 flex-center">
                                            <span class="minutes">0</span>m
                                        </li>
                                        <li class="countdown-list__item text-heading flex-align gap-4 text-xs fw-medium w-30 h-28 rounded-4 border border-main-600 p-0 flex-center">
                                            <span class="seconds">0</span>s
                                        </li>
                                    </ul>
                                </div>
                                <span class="text-white text-xs">Remains until the end of the offer</span>
                            </div>
                            @endif
                            
                            <!-- Price Section -->
                           <!-- Price Section -->
<div class="flex-align gap-16 flex-wrap mb-32" id="priceSection">
    <div class="flex-align gap-8">
        {{-- Discount Badge - Only if no active sale --}}
        @if($product->product_discount && !$hasActiveSale)
        <div class="flex-align gap-8 text-main-two-600">
            <i class="ph-fill ph-seal-percent text-xl"></i>
            <span id="discountBadge">{{ $product->product_discount }}% OFF</span>
        </div>
        @endif
        
        {{-- Sale Badge - When sale is active --}}
        @if($hasActiveSale)
        <div class="flex-align gap-8 text-success">
            <i class="ph-fill ph-tag text-xl"></i>
            <span id="saleBadge">SALE</span>
        </div>
        @endif
        
        <h6 class="mb-0 {{ $hasActiveSale ? 'text-success' : '' }}" id="currentPrice">
            {{ getUserCurrency() }} {{ number_format(convertPrice($displayPrice), 2) }}
        </h6>
    </div>
    
    @if($showStrikeThrough)
    <div class="flex-align gap-8">
        <span class="text-gray-700">Regular Price</span>
        <h6 class="text-xl text-gray-400 mb-0 fw-medium text-decoration-line-through" id="originalPrice">
            {{ getUserCurrency() }} {{ number_format(convertPrice($originalPrice), 2) }}
        </h6>
    </div>
    @endif
</div>
                            
                            <!-- Product Name -->
                            <h5 class="mb-12">{{ $product->product_name }}</h5>
                            
                            <!-- Rating -->
                            <div class="flex-align flex-wrap gap-12 mb-6">
                                <div class="flex-align gap-12 flex-wrap">
                                    <div class="flex-align gap-8">
                                        @php $averageRating = $averageRating ?? 0; @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($averageRating))
                                                <span class="text-xs fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                            @else
                                                <span class="text-xs fw-medium text-gray-400 d-flex"><i class="ph-fill ph-star"></i></span>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm fw-medium text-neutral-600">{{ number_format($averageRating, 1) }} Star Rating</span>
                                    <span class="text-sm fw-medium text-gray-500">({{ $totalReviews ?? 0 }})</span>
                                </div>
                                <span class="text-sm fw-medium text-gray-500">|</span>
                                <span class="text-gray-900"> <span class="text-gray-400">SKU:</span> <span id="productSku">{{ $product->product_code ?? 'N/A' }}</span> </span>
                            </div>
                            
                            <span class="mt-32 pt-32 text-gray-700 border-top border-gray-100 d-block"></span>
                            
                            <!-- Description -->
                            <p class="text-gray-700 mb-4">{{ $product->short_description ?? 'No description available.' }}</p>

                            <!-- Stock Status -->
                            <p class="mb-32 @if($product->stock <= 0) text-danger @endif" id="stockStatus">
                                <i class="ph ph-location"></i>
                                <span id="stockText" class="@if($product->stock <= 0) text-danger @endif">
                                    {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                                <span class="ms-2" id="stockQuantity">({{ $product->stock }} available)</span>
                            </p>

                            <!-- ========== FIXED VARIANTS SELECTION WITH COLORS AND SIZES ========== -->
                            @if($product->productVariants && $product->productVariants->isNotEmpty())
                            <div class="mt-32">
                                <h6 class="mb-16">Select Variant:</h6>
                                
                                @php
                                    // Organize variants by type
                                    $colorVariants = [];
                                    $sizeVariants = [];
                                    $otherVariants = [];
                                    $variantData = [];
                                    $attributeGroups = [];
                                    
                                    foreach($product->productVariants as $variant) {
                                        // Store complete variant data
                                        $variantDiscount = 0;
                                        if($variant->sale_price && $variant->sale_price < $variant->price) {
                                            $variantDiscount = round((($variant->price - $variant->sale_price) / $variant->price) * 100);
                                        }
                                        
                                        $variantImage = '';
                                        $variantImages = [];
                                        
                                        if($variant->images && $variant->images->isNotEmpty()) {
                                            $firstImage = $variant->images->first();
                                            if($firstImage && !empty($firstImage->image)) {
                                                $variantImage = asset('storage/'.$firstImage->image);
                                            }
                                            
                                            foreach($variant->images as $img) {
                                                $variantImages[] = asset('storage/'.$img->image);
                                            }
                                        }
                                        
                                        if(empty($variantImage) && $product->thumbnail_image) {
                                            $variantImage = asset('storage/'.$product->thumbnail_image);
                                        }
                                        
                                        $displayName = '';
                                        $variantAttributes = [];
                                        
                                        foreach($variant->variantValues as $vv) {
                                            if($vv->variantValue) {
                                                $displayName .= $vv->variantValue->value . ' ';
                                                $attrName = strtolower($vv->variant->name ?? '');
                                                $attrValue = $vv->variantValue->value;
                                                $variantAttributes[$attrName] = $attrValue;
                                            }
                                        }
                                        $displayName = trim($displayName) ?: $variant->sku;
                                        
                                        $variantData[$variant->id] = [
                                            'id' => $variant->id,
                                            'image' => $variantImage,
                                            'images' => $variantImages,
                                            'price' => number_format($variant->price, 2),
                                            'sale_price' => $variant->sale_price ? number_format($variant->sale_price, 2) : '',
                                            'discount' => $variantDiscount,
                                            'stock' => $variant->stock,
                                            'stock_text' => $variant->stock > 0 ? 'In Stock' : 'Out of Stock',
                                            'sku' => $variant->sku,
                                            'display_name' => $displayName,
                                            'attributes' => $variantAttributes
                                        ];
                                        
                                        // Build attribute groups for UI
                                        foreach($variantAttributes as $attrName => $attrValue) {
                                            if (!isset($attributeGroups[$attrName])) {
                                                $attributeGroups[$attrName] = [];
                                            }
                                            if (!in_array($attrValue, $attributeGroups[$attrName])) {
                                                $attributeGroups[$attrName][] = $attrValue;
                                            }
                                        }
                                    }
                                    
                                    // Separate color and size for display
                                    $colorValues = $attributeGroups['color'] ?? [];
                                    $sizeValues = $attributeGroups['size'] ?? [];
                                    
                                    // Get all other attributes (besides color and size)
                                    $otherAttributes = array_diff_key($attributeGroups, array_flip(['color', 'size']));
                                @endphp
                                
                                {{-- Hidden field to store variant data --}}
                                <input type="hidden" id="variantData" value='@json($variantData)'>
                                <input type="hidden" id="selectedVariantId" value="">
                                
                                {{-- COLOR ATTRIBUTES - Always show first as swatches --}}
                                @if(!empty($colorValues))
                                <div class="mb-24 attribute-group" data-attribute="color">
                                    <label class="text-gray-900 d-block mb-12 fw-semibold text-capitalize">
                                        Color:
                                        <span class="fw-normal text-gray-600 ms-2 selected-attribute-value" id="selected-color"></span>
                                    </label>
                                    <div class="d-flex flex-wrap gap-12">
                                        @foreach($colorValues as $colorValue)
                                            @php
                                                // Find a variant with this color
                                                $matchingVariant = null;
                                                $matchingVariantId = null;
                                                foreach($variantData as $vid => $vdata) {
                                                    if(isset($vdata['attributes']['color']) && $vdata['attributes']['color'] == $colorValue) {
                                                        $matchingVariantId = $vid;
                                                        $matchingVariant = $vdata;
                                                        break;
                                                    }
                                                }
                                                
                                                // Color code mapping
                                                $colorCode = '#cccccc'; // Default gray
                                                $colorMap = [
                                                    'red' => '#ff0000', 
                                                    'blue' => '#0000ff', 
                                                    'green' => '#00ff00',
                                                    'yellow' => '#ffff00', 
                                                    'black' => '#000000', 
                                                    'white' => '#ffffff',
                                                    'purple' => '#800080', 
                                                    'orange' => '#ffa500', 
                                                    'pink' => '#ffc0cb',
                                                    'brown' => '#a52a2a', 
                                                    'gray' => '#808080', 
                                                    'grey' => '#808080',
                                                    'navy' => '#000080', 
                                                    'maroon' => '#800000', 
                                                    'teal' => '#008080',
                                                    'olive' => '#808000', 
                                                    'lime' => '#00ff00', 
                                                    'aqua' => '#00ffff',
                                                    'silver' => '#c0c0c0', 
                                                    'gold' => '#ffd700',
                                                    'beige' => '#f5f5dc',
                                                    'ivory' => '#fffff0',
                                                    'cyan' => '#00ffff',
                                                    'magenta' => '#ff00ff',
                                                    'violet' => '#ee82ee',
                                                    'indigo' => '#4b0082',
                                                    'turquoise' => '#40e0d0',
                                                    'lavender' => '#e6e6fa',
                                                    'coral' => '#ff7f50',
                                                    'salmon' => '#fa8072',
                                                    'khaki' => '#f0e68c',
                                                    'plum' => '#dda0dd',
                                                    'peach' => '#ffdab9',
                                                    'mint' => '#98fb98',
                                                    'charcoal' => '#36454f',
                                                    'cream' => '#fffdd0',
                                                    'wine' => '#722f37',
                                                    'rose' => '#ff007f',
                                                    'sky' => '#87ceeb',
                                                    'leaf' => '#4f8a2c',
                                                ];
                                                
                                                $colorLower = strtolower(trim($colorValue));
                                                
                                                // Direct match
                                                if (isset($colorMap[$colorLower])) {
                                                    $colorCode = $colorMap[$colorLower];
                                                } else {
                                                    // Partial match
                                                    foreach ($colorMap as $key => $code) {
                                                        if (strpos($colorLower, $key) !== false) {
                                                            $colorCode = $code;
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // Check if this color has any in-stock variants
                                                $hasStock = false;
                                                foreach($variantData as $vid => $vdata) {
                                                    if(isset($vdata['attributes']['color']) && $vdata['attributes']['color'] == $colorValue && $vdata['stock'] > 0) {
                                                        $hasStock = true;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            
                                            <button type="button" 
                                                    class="color-swatch border-2 rounded-circle p-0 m-0 position-relative"
                                                    style="width: 24px !important; height: 24px; background-color: {{ $colorCode }} !important; border-color: #dee2e6; cursor: {{ $hasStock ? 'pointer' : 'not-allowed' }}; opacity: {{ $hasStock ? '1' : '0.5' }}; transition: all 0.2s;"
                                                    title="{{ $colorValue }}"
                                                    data-attribute="color"
                                                    data-value="{{ $colorValue }}"
                                                    onclick="selectAttribute(this, 'color')"
                                                    {{ !$hasStock ? 'disabled' : '' }}>
                                                @if(!$hasStock)
                                                <span class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.5); border-radius: 50%;">
                                                    <i class="ph ph-x text-danger fw-bold" style="font-size: 20px;"></i>
                                                </span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                {{-- SIZE ATTRIBUTES - Show below colors as boxes --}}
                                @if(!empty($sizeValues))
                                <div class="mb-24 attribute-group" data-attribute="size">
                                    <label class="text-gray-900 d-block mb-12 fw-semibold text-capitalize">
                                        Size:
                                        <span class="fw-normal text-gray-600 ms-2 selected-attribute-value" id="selected-size"></span>
                                    </label>
                                    <div class="d-flex flex-wrap gap-12">
                                        @foreach($sizeValues as $sizeValue)
                                            @php
                                                // Check if this size has any in-stock variants
                                                $hasStock = false;
                                                foreach($variantData as $vid => $vdata) {
                                                    if(isset($vdata['attributes']['size']) && $vdata['attributes']['size'] == $sizeValue && $vdata['stock'] > 0) {
                                                        $hasStock = true;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            
                                            <button type="button" 
                                                    class="size-btn px-20 py-10 rounded-8 border-2 bg-transparent fw-medium"
                                                    style="border-color: #dee2e6; transition: all 0.2s; min-width: 70px; {{ !$hasStock ? 'opacity: 0.5;' : '' }} cursor: {{ $hasStock ? 'pointer' : 'not-allowed' }};"
                                                    data-attribute="size"
                                                    data-value="{{ $sizeValue }}"
                                                    onclick="selectAttribute(this, 'size')"
                                                    {{ !$hasStock ? 'disabled' : '' }}>
                                                {{ $sizeValue }}
                                                @if(!$hasStock)
                                                <small class="d-block text-danger" style="font-size: 10px;">Out of stock</small>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                {{-- OTHER ATTRIBUTES (if any) - As boxes --}}
                                @foreach($otherAttributes as $attrName => $attrValues)
                                <div class="mb-24 attribute-group" data-attribute="{{ $attrName }}">
                                    <label class="text-gray-900 d-block mb-12 fw-semibold text-capitalize">
                                        {{ ucfirst($attrName) }}:
                                        <span class="fw-normal text-gray-600 ms-2 selected-attribute-value" id="selected-{{ $attrName }}"></span>
                                    </label>
                                    <div class="d-flex flex-wrap gap-12">
                                        @foreach($attrValues as $attrValue)
                                            @php
                                                // Check if this attribute value has any in-stock variants
                                                $hasStock = false;
                                                foreach($variantData as $vid => $vdata) {
                                                    if(isset($vdata['attributes'][$attrName]) && $vdata['attributes'][$attrName] == $attrValue && $vdata['stock'] > 0) {
                                                        $hasStock = true;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            
                                            <button type="button" 
                                                    class="attribute-btn px-20 py-10 rounded-8 border-2 bg-transparent fw-medium"
                                                    style="border-color: #dee2e6; transition: all 0.2s; min-width: 70px; {{ !$hasStock ? 'opacity: 0.5;' : '' }}"
                                                    data-attribute="{{ $attrName }}"
                                                    data-value="{{ $attrValue }}"
                                                    onclick="selectAttribute(this, '{{ $attrName }}')"
                                                    {{ !$hasStock ? 'disabled' : '' }}>
                                                {{ $attrValue }}
                                                @if(!$hasStock)
                                                <small class="d-block text-danger" style="font-size: 10px;">Out of stock</small>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                                
                                {{-- No variant selected message --}}
                                <div class="mt-2 text-muted small" id="noVariantMessage">
                                    <i class="ph ph-info"></i> Select a variant to see details
                                </div>
                            </div>
                            @endif
                            <!-- ========== END VARIANTS SELECTION ========== -->
                            
                            <span class="mt-32 pt-32 text-gray-700 border-top border-gray-100 d-block"></span>

                            <!-- Add to Cart Section -->
                            <div class="mt-32">
                                <div class="row align-items-end">
                                    <div class="col-sm-4">
                                        <label class="text-heading fw-semibold mb-8 d-block">Quantity:</label>
                                        <div class="d-flex rounded-4 overflow-hidden">
                                            <button type="button" class="quantity__minus flex-shrink-0 h-48 w-48 text-neutral-600 bg-gray-50 flex-center hover-bg-main-600 hover-text-white shop-down">
                                                <i class="ph ph-minus"></i>
                                            </button>
                                            <input type="number" class="quantity__input flex-grow-1 border border-gray-100 border-start-0 border-end-0 text-center w-32 px-16" 
                                                   id="quantityInput" value="1" min="1" max="{{ $product->stock }}" 
                                                   data-stock="{{ $product->stock }}" name="quantity">
                                            <button type="button" class="quantity__plus flex-shrink-0 h-48 w-48 text-neutral-600 bg-gray-50 flex-center hover-bg-main-600 hover-text-white shop-up">
                                                <i class="ph ph-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-8 mt-3 mt-sm-0">
                                        @if($product->stock > 0)
                                        <a href="javascript:void(0)" class="btn btn-main flex-center gap-8 rounded-8 py-16 fw-normal w-100 add_to_cart"
                                           data-product-id="{{ $product->id }}" id="addToCartBtn"
                                           onclick="addToCartWithVariant(event)">
                                            <i class="ph ph-shopping-cart-simple text-lg"></i>
                                            Add To Cart
                                        </a>
                                        @else
                                        <a href="javascript:void(0)" class="btn btn-main flex-center gap-8 rounded-8 py-16 fw-normal w-100 opacity-50 disabled" style="cursor:not-allowed;">
                                            <i class="ph ph-shopping-cart-simple text-lg"></i>
                                            Out of Stock
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="mt-32">
                                <a href="javascript:void(0)" class="text-decoration-none fw-semibold fs-6 d-flex align-items-center wishlist-toggle"
                                   data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add To Wishlist"
                                   data-product-id="{{ $product->id }}">
                                    <i class="ph-fill ph-heart text-2xl me-2"></i>
                                    <span>Add to wishlist</span>
                                </a>
                            </div>
                            
                            <!-- Product Meta -->
                          <ul class="list-unstyled border-top pt-32 mt-32">
    <li class="d-flex mb-4 align-items-center">
        <span class="fw-semibold">SKU:</span>
        <span class="ms-2" id="productSkuMeta">{{ $product->product_code ?? 'N/A' }}</span>
    </li>
    @if($product->category)
    <li class="d-flex mb-4 align-items-center">
        <span class="fw-semibold">Category:</span>
        <span class="ms-2">{{ $product->category->category_name }}</span>
    </li>
    @endif
    @if($product->brand)
    <li class="d-flex mb-4 align-items-center">
        <span class="fw-semibold">Brand:</span>
        <span class="ms-2">{{ $product->brand->name }}</span>
    </li>
    @endif

 <!-- Add this right after Add to Cart section in your product detail page -->
<!-- Yeh code aapki product detail blade mein dalna hai -->

@php
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
@endphp

@if($isGlasses && ($product->product_type ?? 'normal') === 'normal')
    <div class="mt-4 p-4 border border-primary rounded-3 bg-light" id="prescriptionSection">
        <h5 class="text-primary mb-3">
            <i class="far fa-eye me-2"></i> Enter Your Prescription
        </h5>
        <p class="small text-muted mb-3">Please enter your prescription details as per your doctor's prescription.</p>
          <div class="alert alert-info mt-3 mb-0 small">
    <i class="fas fa-info-circle me-2"></i>
    <strong>How to submit prescription:</strong>
    <ul class="mb-0 mt-2">
        <li>✅ <strong>Option 1:</strong> Upload prescription image only (text fields optional)</li>
        <li>✅ <strong>Option 2:</strong> Fill ALL prescription text fields completely (image optional)</li>
        <li>✅ <strong>Option 3:</strong> Upload image + fill all text fields</li>
        <li>❌ <strong>Not allowed:</strong> Partial text entries (some fields filled, some empty)</li>
    </ul>
</div>
        <div class="row g-4">
            <!-- Right Eye -->
            <div class="col-md-6">
                <div class="bg-white p-3 rounded-3">
                    <h6 class="fw-bold text-primary mb-3">Right Eye (OD)</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Axis <span class="text-danger">*</span>
                            <small class="text-muted">(0-180°)</small>
                        </label>
                        <input type="number" 
                               class="form-control form-control-sm prescription-field" 
                               id="rightAxis" 
                               placeholder="e.g., 90" 
                               min="0" 
                               max="180" 
                               step="1"
                               >
                        <div class="invalid-feedback">Axis must be between 0-180°</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Spherical (SPH) <span class="text-danger">*</span>
                            <small class="text-muted">(-20 to +20)</small>
                        </label>
                        <input type="number" 
                               class="form-control form-control-sm prescription-field" 
                               id="rightSpherical" 
                               placeholder="e.g., -2.50" 
                               step="0.25" 
                               min="-20" 
                               max="20"
                               >
                        <div class="invalid-feedback">Spherical must be between -20 to +20</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Cylindrical (CYL) <span class="text-danger">*</span>
                            <small class="text-muted">(-10 to +10)</small>
                        </label>
                        <input type="number" 
                               class="form-control form-control-sm prescription-field" 
                               id="rightCylindrical" 
                               placeholder="e.g., -1.25" 
                               step="0.25" 
                               min="-10" 
                               max="10"
                               >
                        <div class="invalid-feedback">Cylindrical must be between -10 to +10</div>
                    </div>
                </div>
            </div>
            
            <!-- Left Eye -->
            <div class="col-md-6">
                <div class="bg-white p-3 rounded-3">
                    <h6 class="fw-bold text-primary mb-3">Left Eye (OS)</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Axis <span class="text-danger">*</span>
                            <small class="text-muted">(0-180°)</small>
                        </label>
                        <input type="number" 
                               class="form-control form-control-sm prescription-field" 
                               id="leftAxis" 
                               placeholder="e.g., 85" 
                               min="0" 
                               max="180" 
                               step="1"
                               >
                        <div class="invalid-feedback">Axis must be between 0-180°</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Spherical (SPH) <span class="text-danger">*</span>
                            <small class="text-muted">(-20 to +20)</small>
                        </label>
                        <input type="number" 
                               class="form-control form-control-sm prescription-field" 
                               id="leftSpherical" 
                               placeholder="e.g., -2.25" 
                               step="0.25" 
                               min="-20" 
                               max="20"
                               >
                        <div class="invalid-feedback">Spherical must be between -20 to +20</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Cylindrical (CYL) <span class="text-danger">*</span>
                            <small class="text-muted">(-10 to +10)</small>
                        </label>
                        <input type="number" 
                               class="form-control form-control-sm prescription-field" 
                               id="leftCylindrical" 
                               placeholder="e.g., -1.00" 
                               step="0.25" 
                               min="-10" 
                               max="10"
                               >
                        <div class="invalid-feedback">Cylindrical must be between -10 to +10</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Prescription Image Upload - Sirf Image Field -->
<!-- Prescription Image Upload -->
<div class="mt-4">
    <label class="form-label fw-semibold">
        Upload Prescription Image <span class="text-danger">*</span>
    </label>
    <input type="file" 
           class="form-control" 
           id="prescriptionImage" 
           name="prescription_image"
           accept="image/jpeg,image/png,image/jpg,image/webp"
           >
    <div class="invalid-feedback" id="prescriptionImageError">
        Please upload a prescription image (JPG, PNG, WEBP, Max: 5MB)
    </div>
    <small class="text-muted">Allowed: JPG, PNG, WEBP (Max: 5MB)</small>
</div>
    </div>

    <style>
    .prescription-field.is-invalid {
        border-color: #dc3545;
        background-image: none;
    }
    .prescription-field:focus.is-invalid {
        box-shadow: 0 0 0 0.2rem rgba(220,53,69,0.25);
    }
    </style>
@endif
</ul>
                            
                          
                       
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
        <!-- Sidebar -->
<div class="col-xl-3">
    <div class="product-details__sidebar py-40 px-32 border border-gray-100 rounded-16">
        
        <!-- ✅ VENDOR INFO - Dynamic with store button -->
        <div class="flex-between bg-main-600 rounded-pill p-8 mb-32">
            <div class="flex-align gap-8">
                <span class="w-44 h-44 bg-white rounded-circle flex-center text-2xl text-main-600">
                    <i class="ph ph-storefront"></i>
                </span>
                <span class="text-white">by {{ $product->vendor->store_name ?? 'Marketpro' }}</span>
            </div>
            <a href="{{ route('vendor.show', $product->vendor->store_slug) }}" class="btn btn-white rounded-pill text-uppercase px-4 py-2" style="font-size: 13px;">
                View Store
            </a>
        </div>
        
        <!-- ✅ PRODUCT META - SKU, Category, Brand -->
        <div class="mb-32">
            <ul class="list-unstyled">
                <li class="d-flex justify-content-between align-items-center py-3 border-bottom border-gray-100">
                    <span class="text-gray-600">SKU:</span>
                    <span class="fw-semibold text-dark" id="sidebarSku">{{ $product->product_code ?? 'N/A' }}</span>
                </li>
                
                @if($product->category)
                <li class="d-flex justify-content-between align-items-center py-3 border-bottom border-gray-100">
                    <span class="text-gray-600">Category:</span>
                    <span class="fw-semibold text-dark">
                        <a href="{{ route('products', ['category' => $product->category->id]) }}" class="text-decoration-none text-dark hover-text-main-600">
                            {{ $product->category->category_name }}
                        </a>
                    </span>
                </li>
                @endif
                
                @if($product->brand)
                <li class="d-flex justify-content-between align-items-center py-3 border-bottom border-gray-100">
                    <span class="text-gray-600">Brand:</span>
                    <span class="fw-semibold text-dark">
                        <a href="" class="text-decoration-none text-dark hover-text-main-600">
                            {{ $product->brand->name }}
                        </a>
                    </span>
                </li>
                @endif
                
                <li class="d-flex justify-content-between align-items-center py-3">
                    <span class="text-gray-600">Availability:</span>
                    <span class="fw-semibold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                        <i class="ph-fill ph-circle-fill fs-6 me-1"></i>
                        {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                    </span>
                </li>
            </ul>
        </div>
        
        <!-- ✅ PRICE SUMMARY -->
        <div class="mb-32 bg-light p-4 rounded-3">
            <div class="flex-between flex-wrap gap-8 pb-3 mb-3 border-bottom border-gray-200">
                <span class="text-gray-600">Price</span>
                <h6 class="text-lg mb-0" id="sidebarPrice">  {{ getUserCurrency() }} {{ number_format(convertPrice($displayPrice), 2) }}</h6>
            </div>
  
        </div>

   

 
    </div>
</div>
        </div>

        <!-- Tabs Section -->
        <div class="pt-80">
            <div class="product-dContent border rounded-24">
                <div class="product-dContent__header border-bottom border-gray-100">
                    <div class="d-flex justify-content-center">
                        <button class="tab-btn active py-3 px-6 mx-2 border-0 bg-transparent fs-5 fw-semibold" data-target="product-details-content">Description</button>
                        <button class="tab-btn py-3 px-6 mx-2 border-0 bg-transparent fs-5 fw-semibold" data-target="reviews-content">
                            Reviews @if($reviewCount > 0)<span class="badge bg-primary ms-2">{{ $reviewCount }}</span>@endif
                        </button>
                    </div>
                </div>
                
                <div class="product-dContent__box">
                    <div id="product-details-content" class="tab-content active">
                        <div class="mb-40 p-4">
                            <h6 class="mb-24">Product Description</h6>
                            <div class="product-description">
                                @if(!empty($product->long_description))
                                    {!! nl2br(e($product->long_description)) !!}
                                @elseif(!empty($product->short_description))
                                    {{ $product->short_description }}
                                @else
                                    <p class="text-muted">No detailed description available.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div id="reviews-content" class="tab-content" style="display: none;">
                        <div class="p-4">
                            <h6 class="mb-24">Customer Reviews</h6>
                            <div class="row g-4">
                                <div class="col-12">
                                    @if($totalReviews > 0)
                                        <div class="reviews-list">
                                            @foreach($reviews as $review)
                                            <div class="review-item border-bottom pb-44 mb-44">
                                                <div class="d-flex align-items-start gap-24">
                                                    <div class="w-52 h-52 rounded-circle flex-shrink-0 bg-secondary d-flex align-items-center justify-content-center text-white fw-bold">
                                                        {{ strtoupper(substr($review->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="flex-between align-items-start gap-8">
                                                            <div>
                                                                <h6 class="mb-12 text-md">{{ $review->name }}</h6>
                                                                <div class="flex-align gap-8">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        @if($i <= $review->rating)
                                                                            <span class="text-warning"><i class="ph-fill ph-star"></i></span>
                                                                        @else
                                                                            <span class="text-gray-400"><i class="ph-fill ph-star"></i></span>
                                                                        @endif
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <span class="text-gray-800 text-xs">{{ $review->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                        <p class="text-gray-700 mt-24">{{ $review->review }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                            @if($reviews->hasPages())
                                            <div class="d-flex justify-content-center mt-8">{{ $reviews->links() }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-10">
                                            <i class="ph-fill ph-chats-circle fa-3x text-muted mb-4"></i>
                                            <h4 class="mb-3">No Reviews Yet</h4>
                                            <p class="text-muted mb-4">Be the first to share your thoughts about this product!</p>
                                        </div>
                                    @endif
                                    <div class="text-center mt-8">
                                        <button class="btn btn-main rounded-pill write-review-btn">Write a Review</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Related Products -->
@if($relatedProducts && $relatedProducts->count() > 0)
<section class="container pt-13 pb-13 pb-lg-20">
    <div class="row">
        <div class="col-12">
            <h5 class="mb-8 text-center">You May Also Like</h5>
            <div class="row gy-50px">
               @include('frontend.component.product-card', ['products' => $relatedProducts])
            </div>
        </div>
    </div>
</section>
@endif

<!-- Write Review Modal -->
<div class="modal fade" id="writeReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reviewForm" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Your Rating *</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required><label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Your Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->check() ? auth()->user()->name : '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Your Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->check() ? auth()->user()->email : '' }}" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Your Review *</label>
                        <textarea name="review" class="form-control" rows="4" placeholder="Share your experience..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitReviewBtn">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript - FIXED: Thumbnail click now works for all variants -->





<script>

    
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined') {
        $('.product-details__thumb-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.product-details__images-slider'
        });
        
        $('.product-details__images-slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.product-details__thumb-slider',
            dots: false,
            arrows: false,
            focusOnSelect: true,
            responsive: [
                { breakpoint: 768, settings: { slidesToShow: 3 } },
                { breakpoint: 480, settings: { slidesToShow: 2 } }
            ]
        });
    }
    
    function startCountdown(elementId, endTimestamp) {
        const countdownElement = document.getElementById(elementId);
        if (!countdownElement) return;
        
        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            const distance = endTimestamp - now;
            
            if (distance <= 0) {
                countdownElement.style.display = 'none';
                return;
            }
            
            const days = Math.floor(distance / (60 * 60 * 24));
            const hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
            const minutes = Math.floor((distance % (60 * 60)) / 60);
            const seconds = distance % 60;
            
            const daysSpan = countdownElement.querySelector('.days');
            const hoursSpan = countdownElement.querySelector('.hours');
            const minutesSpan = countdownElement.querySelector('.minutes');
            const secondsSpan = countdownElement.querySelector('.seconds');
            
            if (daysSpan) daysSpan.textContent = days;
            if (hoursSpan) hoursSpan.textContent = hours;
            if (minutesSpan) minutesSpan.textContent = minutes;
            if (secondsSpan) secondsSpan.textContent = seconds;
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    }
    
    document.querySelectorAll('[id^="countdown-"]').forEach(element => {
        const endDate = element.getAttribute('data-end-date');
        if (endDate) {
            startCountdown(element.id, parseInt(endDate));
        }
    });
    
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const target = this.getAttribute('data-target');
            tabContents.forEach(c => {
                c.classList.remove('active');
                c.style.display = 'none';
            });
            
            const targetContent = document.getElementById(target);
            if (targetContent) {
                targetContent.classList.add('active');
                targetContent.style.display = 'block';
            }
        });
    });
    
    if (tabBtns.length > 0 && !document.querySelector('.tab-btn.active')) {
        tabBtns[0].click();
    }
    
    document.querySelectorAll('.write-review-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            new bootstrap.Modal(document.getElementById('writeReviewModal')).show();
        });
    });
    
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitReviewBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';
            
            fetch('{{ route("review.submit") }}', {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (data.success) {
                    alert(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('writeReviewModal')).hide();
                    reviewForm.reset();
                    document.querySelectorAll('input[name="rating"]').forEach(r => r.checked = false);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    alert(data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Error'));
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert('Network error. Please try again.');
            });
        });
    }
    
    setupQuantityControls();
    validateQuantityInput();
    
    document.querySelectorAll('.color-swatch').forEach(swatch => {
        const originalBg = swatch.style.backgroundColor;
        swatch.style.borderColor = '#dee2e6';
        swatch.style.borderWidth = '2px';
        if (originalBg && originalBg !== 'rgba(0, 0, 0, 0)' && originalBg !== 'transparent') {
            swatch.style.backgroundColor = originalBg;
        }
    });
    
    document.getElementById('selectedVariantId').value = '';
    
    setupThumbnailClickHandlers();
});

let currentSelections = {};
let variantData = {};

try {
    const variantDataEl = document.getElementById('variantData');
    if (variantDataEl) {
        variantData = JSON.parse(variantDataEl.value);
    }
} catch(e) {
    console.error('Error parsing variant data', e);
}

function setupThumbnailClickHandlers() {
    const container = document.getElementById('thumbnailContainer');
    if (!container) return;
    
    container.removeEventListener('click', thumbnailContainerClickHandler);
    container.addEventListener('click', thumbnailContainerClickHandler);
}

function thumbnailContainerClickHandler(e) {
    const thumb = e.target.closest('.thumbnail-item');
    if (!thumb) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const img = thumb.querySelector('img');
    if (img && img.src) {
        changeMainImage(img.src);
    }
}

function selectAttribute(button, attribute) {
    if (button.disabled) return;
    
    const value = button.getAttribute('data-value');
    
    currentSelections[attribute] = value;
    
    document.querySelectorAll(`[data-attribute="${attribute}"] .color-swatch, [data-attribute="${attribute}"] .size-btn, [data-attribute="${attribute}"] .attribute-btn`).forEach(btn => {
        btn.classList.remove('active', 'border-main-600');
        btn.style.borderColor = '#dee2e6';
        btn.style.backgroundColor = btn.classList.contains('color-swatch') ? btn.style.backgroundColor : 'transparent';
        btn.style.color = 'inherit';
        
        if (btn.classList.contains('color-swatch')) {
            btn.style.borderWidth = '2px';
        }
    });
    
    button.classList.add('active', 'border-main-600');
    button.style.borderColor = '#0d6efd';
    
    if (attribute === 'color') {
        button.style.borderWidth = '3px';
    } else {
        button.style.backgroundColor = '#e7f1ff';
        button.style.color = '#0d6efd';
    }
    
    const selectedSpan = document.getElementById(`selected-${attribute}`);
    if (selectedSpan) {
        selectedSpan.textContent = value;
    }
    
    findMatchingVariant();
}

function findMatchingVariant() {
    if (Object.keys(currentSelections).length === 0) return;
    
    let matchedVariantId = null;
    
    for (let vid in variantData) {
        const variant = variantData[vid];
        let match = true;
        
        for (let attr in currentSelections) {
            if (!variant.attributes || variant.attributes[attr] !== currentSelections[attr]) {
                match = false;
                break;
            }
        }
        
        if (match) {
            matchedVariantId = vid;
            break;
        }
    }
    
    if (matchedVariantId) {
        document.getElementById('selectedVariantId').value = matchedVariantId;
        updateVariantDetails(matchedVariantId);
        const msg = document.getElementById('noVariantMessage');
        if (msg) msg.style.display = 'none';
    } else {
        document.getElementById('selectedVariantId').value = '';
    }
}

function updateVariantDetails(variantId) {
    const variant = variantData[variantId];
    if (!variant) return;
    
    if (variant.images && variant.images.length > 0) {
        updateMainSlider(variant.images);
        updateThumbnails(variant.images);
    }
    
    updatePrice(variant.price, variant.sale_price, variant.discount);
    updateStock(variant.stock_text, variant.stock);
    updateSku(variant.sku);
    updateButtons(variant.stock_text, variant.stock);
    updateQuantityMax(variant.stock);
}

function updateMainSlider(images) {
    if (!images || images.length === 0 || typeof $ === 'undefined') return;
    
    const mainSlider = $('.product-details__thumb-slider');
    if (!mainSlider.length) return;
    
    if (mainSlider.hasClass('slick-initialized')) {
        mainSlider.slick('unslick');
    }
    
    const mainContainer = document.querySelector('.product-details__thumb-slider');
    mainContainer.innerHTML = '';
    
    images.forEach(imageUrl => {
        const slide = document.createElement('div');
        const div = document.createElement('div');
        div.className = 'product-details__thumb flex-center h-100';
        
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = 'Product Image';
        img.className = 'main-product-image';
        
        div.appendChild(img);
        slide.appendChild(div);
        mainContainer.appendChild(slide);
    });
    
    setTimeout(() => {
        mainSlider.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.product-details__images-slider'
        });
    }, 50);
}

function changeMainImage(imageUrl) {
    if (!imageUrl) return;
    
    const mainImages = document.querySelectorAll('.product-details__thumb-slider .slick-slide img');
    let targetIndex = 0;
    let found = false;
    
    mainImages.forEach((img, index) => {
        if (img.src === imageUrl) {
            targetIndex = index;
            found = true;
        }
    });
    
    if (!found) {
        const imageFileName = imageUrl.split('/').pop();
        mainImages.forEach((img, index) => {
            if (img.src.includes(imageFileName)) {
                targetIndex = index;
                found = true;
            }
        });
    }
    
    if (found && typeof $ !== 'undefined' && $('.product-details__thumb-slider').length) {
        $('.product-details__thumb-slider').slick('slickGoTo', targetIndex);
    }
}

function updateThumbnails(variantImages) {
    const container = document.getElementById('thumbnailContainer');
    if (!container) return;
    
    try { 
        if (typeof $ !== 'undefined' && $('.product-details__images-slider').hasClass('slick-initialized')) {
            $('.product-details__images-slider').slick('unslick'); 
        }
    } catch(e) {}
    
    container.innerHTML = '';
    
    if (!variantImages || variantImages.length === 0) return;
    
    variantImages.forEach(imageUrl => {
        const slide = document.createElement('div');
        const thumb = document.createElement('div');
        thumb.className = 'max-w-120 max-h-120 h-100 flex-center border border-gray-100 rounded-16 p-8 cursor-pointer thumbnail-item';
        
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = 'Thumbnail';
        img.style.maxHeight = '100px';
        img.style.objectFit = 'contain';
        
        thumb.appendChild(img);
        slide.appendChild(thumb);
        container.appendChild(slide);
    });
    
    setTimeout(() => {
        if (typeof $ !== 'undefined') {
            $('.product-details__images-slider').slick({
                slidesToShow: 4, 
                slidesToScroll: 1, 
                dots: false, 
                arrows: false, 
                focusOnSelect: true,
                responsive: [
                    { breakpoint: 768, settings: { slidesToShow: 3 } }, 
                    { breakpoint: 480, settings: { slidesToShow: 2 } }
                ]
            });
            
            setupThumbnailClickHandlers();
        }
    }, 100);
}

// ========== FIXED PRICE FUNCTION ==========
function updatePrice(price, salePrice, discount) {
    const current = document.getElementById('currentPrice');
    const original = document.getElementById('originalPrice');
    const badge = document.getElementById('discountBadge');
    const sidebar = document.getElementById('sidebarPrice');
    const saleBadge = document.getElementById('saleBadge');
    const currency = '{{ $genralsetting->currency }}';
    
    if (!current) return;
    
    // Remove commas and convert to numbers
    let priceNum = parseFloat(price.toString().replace(/,/g, ''));
    let salePriceNum = salePrice ? parseFloat(salePrice.toString().replace(/,/g, '')) : null;
    
    // Check if sale price exists and is valid
    if (salePriceNum && salePriceNum > 0 && salePriceNum < priceNum) {
        // Show sale price with strike-through
        if (original) {
            original.textContent = currency + ' ' + priceNum.toFixed(2);
            original.style.display = 'inline';
            original.parentElement.style.display = 'flex';
        }
        current.textContent = currency + ' ' + salePriceNum.toFixed(2);
        
        // Show discount badge
        if (badge && discount > 0) {
            badge.textContent = discount + '% OFF';
            badge.style.display = 'inline-block';
            badge.parentElement.style.display = 'flex';
        }
        
        // Show sale badge
        if (saleBadge) {
            saleBadge.style.display = 'inline-block';
            saleBadge.parentElement.style.display = 'flex';
        }
    } else {
        // No sale, show regular price
        if (original) {
            original.style.display = 'none';
            original.parentElement.style.display = 'none';
        }
        current.textContent = currency + ' ' + priceNum.toFixed(2);
        
        // Hide sale badges
        if (badge) {
            badge.style.display = 'none';
            if (badge.parentElement) badge.parentElement.style.display = 'none';
        }
        if (saleBadge) {
            saleBadge.style.display = 'none';
            if (saleBadge.parentElement) saleBadge.parentElement.style.display = 'none';
        }
    }
    
    if (sidebar) sidebar.textContent = current.textContent;
}

function updateStock(stockText, stockQuantity) {
    const stockEl = document.getElementById('stockText');
    const quantityEl = document.getElementById('stockQuantity');
    
    if (stockEl) {
        stockEl.textContent = stockText;
        stockEl.className = stockText === 'Out of Stock' ? 'text-danger' : 'text-success';
    }
    if (quantityEl) quantityEl.textContent = `(${stockQuantity} available)`;
}

function updateSku(sku) {
    const skuEl = document.getElementById('productSku');
    const skuMeta = document.getElementById('productSkuMeta');
    const sidebarSku = document.getElementById('sidebarSku');
    if (skuEl && sku) skuEl.textContent = sku;
    if (skuMeta && sku) skuMeta.textContent = sku;
    if (sidebarSku && sku) sidebarSku.textContent = sku;
}

function updateButtons(stockText, stockQuantity) {
    const cartBtn = document.querySelector('.add_to_cart');
    const buyBtn = document.getElementById('buyNowBtn');
    const qtyInput = document.getElementById('quantityInput');
    const isOut = (stockText === 'Out of Stock' || stockQuantity === 0);
    
    if (cartBtn) {
        if (isOut) {
            cartBtn.classList.add('opacity-50', 'disabled');
            cartBtn.innerHTML = '<i class="ph ph-shopping-cart-simple text-lg"></i> Out of Stock';
            cartBtn.disabled = true;
        } else {
            cartBtn.classList.remove('opacity-50', 'disabled');
            cartBtn.innerHTML = '<i class="ph ph-shopping-cart-simple text-lg"></i> Add To Cart';
            cartBtn.disabled = false;
            cartBtn.setAttribute('data-max-quantity', stockQuantity);
        }
    }
    
    if (buyBtn) {
        if (isOut) {
            buyBtn.classList.add('opacity-50', 'disabled');
            buyBtn.disabled = true;
        } else {
            buyBtn.classList.remove('opacity-50', 'disabled');
            buyBtn.disabled = false;
        }
    }
    
    if (qtyInput) {
        if (isOut) {
            qtyInput.disabled = true; 
            qtyInput.value = 0; 
            qtyInput.setAttribute('max', 0); 
            qtyInput.setAttribute('data-stock', 0);
        } else {
            qtyInput.disabled = false; 
            qtyInput.value = 1; 
            qtyInput.setAttribute('min', 1); 
            qtyInput.setAttribute('max', stockQuantity); 
            qtyInput.setAttribute('data-stock', stockQuantity);
        }
    }
}

function updateQuantityMax(stock) {
    const qtyInput = document.getElementById('quantityInput');
    if (qtyInput) {
        qtyInput.setAttribute('max', stock);
        qtyInput.setAttribute('data-stock', stock);
        if (parseInt(qtyInput.value) > stock) {
            qtyInput.value = stock > 0 ? 1 : 0;
        }
    }
}

function validateQuantityInput() {
    const qty = document.getElementById('quantityInput');
    const cartBtn = document.querySelector('.add_to_cart');
    if (!qty || !cartBtn) return;
    
    let val = parseInt(qty.value) || 1;
    let stock = parseInt(qty.getAttribute('data-stock')) || parseInt(cartBtn.getAttribute('data-max-quantity')) || {{ $product->stock }};
    
    if (stock === 0) { 
        qty.value = 0; 
        qty.disabled = true; 
        return; 
    }
    if (val < 1) { 
        qty.value = 1; 
        val = 1; 
    }
    if (val > stock) { 
        qty.value = stock; 
        val = stock; 
        alert(`Only ${stock} items available`); 
    }
}

function setupQuantityControls() {
    const plus = document.querySelector('.shop-up');
    const minus = document.querySelector('.shop-down');
    const qty = document.getElementById('quantityInput');
    
    if (plus) plus.addEventListener('click', e => {
        e.preventDefault();
        if (!qty || qty.disabled) return;
        let val = parseInt(qty.value) || 1;
        let stock = parseInt(qty.getAttribute('data-stock')) || {{ $product->stock }};
        if (val < stock) { 
            qty.value = val + 1; 
            validateQuantityInput(); 
        } else alert(`Maximum ${stock} items available`);
    });
    
    if (minus) minus.addEventListener('click', e => {
        e.preventDefault();
        if (!qty || qty.disabled) return;
        let val = parseInt(qty.value) || 1;
        if (val > 1) { 
            qty.value = val - 1; 
            validateQuantityInput(); 
        }
    });
    
    if (qty) {
        qty.addEventListener('input', validateQuantityInput);
        qty.addEventListener('change', validateQuantityInput);
    }
}
function addToCartWithVariant(event) {
    event.preventDefault();
    
    const btn = event.currentTarget;
    const selectedVariantId = document.getElementById('selectedVariantId')?.value || '';
    const quantity = document.getElementById('quantityInput')?.value || 1;
    const productId = '{{ $product->id }}';
    
    // ✅ VARIANT VALIDATION
    @if($product->productVariants && $product->productVariants->isNotEmpty())
    if (!selectedVariantId || selectedVariantId === '') {
        alert('Please select a variant first');
        return;
    }
    @endif
    
    let isValid = true;
    
    // ✅ PRESCRIPTION & IMAGE VALIDATION
    @if(isset($isGlasses) && $isGlasses && ($product->product_type ?? 'normal') === 'normal')
    // Prescription fields validation
    if (typeof validatePrescription === 'function') {
        isValid = validatePrescription();
    }
    
   
    @endif
    
    // ✅ Agar koi validation fail hui to yahi return ho
    if (!isValid) {
        return false;
    }
    
    // ✅ Sab validation pass hone ke baad hi button disable karo
    btn.disabled = true;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Adding...';
    
    // ✅ GET PRESCRIPTION DATA
    let prescriptionData = null;
    @if(isset($isGlasses) && $isGlasses && ($product->product_type ?? 'normal') === 'normal')
    prescriptionData = typeof getPrescriptionData === 'function' ? getPrescriptionData() : null;
    @endif
    
    // ✅ FormData for file upload
    let formData = new FormData();
    formData.append('product_id', productId);
    formData.append('variant_id', selectedVariantId || '');
    formData.append('quantity', quantity);
    formData.append('_token', '{{ csrf_token() }}');
    
    @if(isset($isGlasses) && $isGlasses && ($product->product_type ?? 'normal') === 'normal')
    if (prescriptionData) {
        formData.append('prescription', JSON.stringify(prescriptionData));
    }
    // Add image
    const imageFileForUpload = document.getElementById('prescriptionImage')?.files[0];
    if (imageFileForUpload) {
        formData.append('prescription_image', imageFileForUpload);
    }
    @endif
    
    $.ajax({
        url: '{{ route("cart.add") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Add to cart response:', response);
            
            if (response.success) {
                if (window.cartSystem) {
                    window.cartSystem.updateNavbarCartCount(response.count);
                    window.cartSystem.updateAllCartSections(response.items, response.total);
                }
                
                try {
                    var offcanvas = new bootstrap.Offcanvas('#shoppingCart');
                    offcanvas.show();
                } catch(e) {}
                
                btn.innerHTML = '<i class="ph ph-check"></i> Added!';
                
                @if(isset($isGlasses) && $isGlasses && ($product->product_type ?? 'normal') === 'normal')
                if (typeof resetPrescriptionForm === 'function') {
                    resetPrescriptionForm();
                }
                // Clear image input
                const imageClear = document.getElementById('prescriptionImage');
                if (imageClear) imageClear.value = '';
                // Remove error class from image
                imageClear.classList.remove('is-invalid');
                @endif
                
                setTimeout(function() {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 1000);
            } else {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                alert(response.message || 'Failed to add to cart');
            }
        },
        error: function(xhr, status, error) {
            console.error('Add to cart error:', xhr);
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
            let errorMessage = 'Error adding to cart. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
}
/**
 * Clear all prescription field errors
 */
function clearPrescriptionErrors() {
    const fields = ['rightAxis', 'rightSpherical', 'rightCylindrical', 'leftAxis', 'leftSpherical', 'leftCylindrical'];
    fields.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (input) input.classList.remove('is-invalid');
    });
    const imageInput = document.getElementById('prescriptionImage');
    if (imageInput) imageInput.classList.remove('is-invalid');
}

// Yeh code aapke product detail page ke <script> tag mein dalna hai

/**
 * Validate prescription fields
 */
/**
 * Validate prescription fields - NEW LOGIC
 * Sirf image → Allow
 * Sab text fields fill → Allow
 * Partial text → Block
 */
function validatePrescription() {
    const prescriptionSection = document.getElementById('prescriptionSection');
    if (!prescriptionSection) return true;
    
    const hasImage = document.getElementById('prescriptionImage')?.files.length > 0;
    
    // Check karo kitne text fields fill hain
    const fields = ['rightAxis', 'rightSpherical', 'rightCylindrical', 'leftAxis', 'leftSpherical', 'leftCylindrical'];
    let filledCount = 0;
    let fieldValues = {};
    
    fields.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (input && input.value && input.value.trim() !== '') {
            filledCount++;
            fieldValues[fieldId] = input.value;
        }
    });
    
    // Case 1: Sirf Image (koi text nahi) → Allow
    if (hasImage && filledCount === 0) {
        clearPrescriptionErrors();
        return true;
    }
    
    // Case 2: Sab text fields fill hain (6/6) → Allow
    if (filledCount === 6) {
        // Validate range bhi karo
        let isValid = true;
        const rangeFields = [
            { id: 'rightAxis', min: 0, max: 180 },
            { id: 'rightSpherical', min: -20, max: 20 },
            { id: 'rightCylindrical', min: -10, max: 10 },
            { id: 'leftAxis', min: 0, max: 180 },
            { id: 'leftSpherical', min: -20, max: 20 },
            { id: 'leftCylindrical', min: -10, max: 10 }
        ];
        
        rangeFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (input) {
                const value = parseFloat(input.value);
                if (isNaN(value) || value < field.min || value > field.max) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            }
        });
        
        if (!isValid) {
            alert('Please check prescription values. They must be within valid ranges.');
        }
        return isValid;
    }
    
    // Case 3: Partial text (1 to 5 fields filled) → Block
    if (filledCount > 0 && filledCount < 6) {
        // Jo fields empty hain unhe red karo
        fields.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (input && (!input.value || input.value.trim() === '')) {
                input.classList.add('is-invalid');
            } else if (input) {
                input.classList.remove('is-invalid');
            }
        });
        
        alert('Please either:\n• Upload prescription image only\n• OR fill ALL prescription fields completely\nPartial entries are not allowed.');
        return false;
    }
    
    // Case 4: Kuch bhi nahi (no image, no text) → Block
    if (!hasImage && filledCount === 0) {
        alert('Prescription is required. Please either upload prescription image OR fill all prescription fields.');
        return false;
    }
    
    return true;
}

/**
 * Get prescription data as JSON
 */
/**
 * Get prescription data - Sirf tab data bhejo jab saare text fields fill hon
 */
function getPrescriptionData() {
    const prescriptionSection = document.getElementById('prescriptionSection');
    if (!prescriptionSection) return null;
    
    // Check karo saare fields fill hain ya nahi
    const fields = ['rightAxis', 'rightSpherical', 'rightCylindrical', 'leftAxis', 'leftSpherical', 'leftCylindrical'];
    let allFilled = true;
    
    for (let fieldId of fields) {
        const input = document.getElementById(fieldId);
        if (!input || !input.value || input.value.trim() === '') {
            allFilled = false;
            break;
        }
    }
    
    // Agar saare nahi bhare to null return karo (sirf image jayega)
    if (!allFilled) {
        return null;
    }
    
    // Saare bhare hain to data return karo
    return {
        right_axis: document.getElementById('rightAxis')?.value,
        right_spherical: document.getElementById('rightSpherical')?.value,
        right_cylindrical: document.getElementById('rightCylindrical')?.value,
        left_axis: document.getElementById('leftAxis')?.value,
        left_spherical: document.getElementById('leftSpherical')?.value,
        left_cylindrical: document.getElementById('leftCylindrical')?.value
    };
}

/**
 * Reset prescription form
 */
function resetPrescriptionForm() {
    const prescriptionSection = document.getElementById('prescriptionSection');
    if (!prescriptionSection) return;
    
    document.querySelectorAll('.prescription-field').forEach(input => {
        input.value = '';
        input.classList.remove('is-invalid');
    });
}

</script>

<!-- CSS - FIXED -->
<style>
.tab-btn { position: relative; color: #6c757d; cursor: pointer; transition: all 0.3s; }
.tab-btn.active { color: #000; font-weight: 600; }
.tab-btn.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background-color: #000; }
.tab-content { display: none; }
.tab-content.active { display: block; animation: fadeIn 0.5s; }
.star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-start; }
.star-rating input { display: none; }
.star-rating label { font-size: 2rem; color: #ddd; cursor: pointer; margin-right: 5px; }
.star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }
.quantity__input::-webkit-inner-spin-button, .quantity__input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.quantity__input { -moz-appearance: textfield; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* Color swatch styles */
.color-swatch {
    border: 2px solid #dee2e6;
    transition: all 0.2s ease;
    opacity: 1;
    filter: none;
    display: inline-block;
    overflow: hidden;
}

.color-swatch:hover:not([disabled]) {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.color-swatch.border-main-600 {
    border-color: #0d6efd !important;
    border-width: 3px !important;
    transform: scale(1.05);
    box-shadow: 0 0 0 2px rgba(13,110,253,0.25);
    opacity: 1 !important;
    filter: none !important;
}

.color-swatch:not(.border-main-600) {
    opacity: 0.9;
    filter: brightness(0.95);
}

/* Size button styles */
.size-btn, .attribute-btn {
    border: 2px solid #dee2e6;
    background: transparent;
    transition: all 0.2s ease;
    font-weight: 500;
    cursor: pointer;
}

.size-btn:hover:not([disabled]), .attribute-btn:hover:not([disabled]) {
    border-color: #0d6efd !important;
    background-color: #f0f7ff;
}

.size-btn.active, .attribute-btn.active,
.size-btn.border-main-600, .attribute-btn.border-main-600 {
    border-color: #0d6efd !important;
    background-color: #e7f1ff !important;
    color: #0d6efd !important;
    font-weight: 600;
    box-shadow: 0 0 0 2px rgba(13,110,253,0.25);
}

.size-btn[disabled], .attribute-btn[disabled] {
    cursor: not-allowed;
    opacity: 0.5;
}

/* Attribute group styling */
.attribute-group {
    border-bottom: 1px dashed #e9ecef;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.attribute-group:last-child {
    border-bottom: none;
}

.selected-attribute-value {
    font-size: 0.9rem;
    color: #0d6efd;
    font-weight: 500;
}

/* Thumbnail styles */
.thumbnail-item {
    cursor: pointer;
    transition: all 0.2s ease;
}

.thumbnail-item:hover {
    border-color: #0d6efd !important;
    transform: scale(1.05);
}

/* No variant message */
#noVariantMessage {
    font-size: 0.85rem;
    color: #6c757d;
    padding: 8px 0;
}

/* Main product image */
.main-product-image {
    max-height: 400px;
    object-fit: contain;
    width: 100%;
}
</style>

@endsection