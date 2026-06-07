@extends('frontend.layouts.app')

@section('content')

@section('seo')
    @if(isset($currentCategory) && $currentCategory)
        <title>{{ $currentCategory->meta_title ?? $currentCategory->category_name }}</title>
        <meta name="description" content="{{ $currentCategory->meta_description }}">
        <meta name="keywords" content="{{ $currentCategory->meta_keywords }}">
    @elseif(isset($dynamicSeo) && $dynamicSeo)
        <title>{{ $dynamicSeo->meta_title ?? $dynamicSeo->page_name }}</title>
        <meta name="description" content="{{ $dynamicSeo->meta_description }}">
        <meta name="keywords" content="{{ $dynamicSeo->meta_keywords }}">
    @else
        <title>{{ $genralsetting->site_name ?? 'Shop' }}</title>
        <meta name="description" content="Browse our collection of products">
    @endif
@endsection

<!-- Breadcrumb Start -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <ul class="flex-align gap-8 flex-wrap">
                @foreach($breadcrumbs ?? [['name' => 'Home', 'url' => route('home'), 'active' => false], ['name' => 'Shop', 'active' => true]] as $index => $crumb)
                    <li class="text-sm">
                        @if(!$crumb['active'] && isset($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                                {{ $crumb['name'] }}
                            </a>
                        @else
                            <span class="text-sm {{ $index == 0 ? 'text-gray-900' : 'text-main-600' }}">
                                {{ $crumb['name'] }}
                            </span>
                        @endif
                    </li>
                    @if(!$loop->last)
                        <li class="flex-align">—</li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Shop Section Start -->
<section class="shop py-80">
    <div class="container container-lg">
        <div class="row">

            <!-- Sidebar Start -->
            <div class="col-lg-3">
                <div class="shop-sidebar">
                    <button type="button" class="shop-sidebar__close d-lg-none d-flex w-32 h-32 flex-center border border-gray-100 rounded-circle hover-bg-main-600 position-absolute inset-inline-end-0 me-10 mt-8 hover-text-white hover-border-main-600">
                        <i class="ph ph-x"></i>
                    </button>
                    
                    <form id="filterForm" method="GET" action="{{ url()->current() }}">
                        
                        @if(request('view'))
                            <input type="hidden" name="view" value="{{ request('view') }}">
                        @endif
                        
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        
                        <!-- Category Filter -->
                        <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                            <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Product Category</h6>
                            <ul class="max-h-540 overflow-y-auto scroll-sm">
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input category-filter" type="radio" name="category" value="" id="cat_all" 
                                               {{ !$selectedCategoryId ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat_all">
                                            <strong>All Categories</strong>
                                        </label>
                                    </div>
                                </li>
                                
                                @foreach($mainCategories as $category)
                                    @php
                                        $isSelected = false;
                                        $selectedSubCategoryId = null;
                                        
                                        if($selectedCategoryId) {
                                            $selectedCat = \App\Models\Category::find($selectedCategoryId);
                                            
                                            if($selectedCat) {
                                                if($selectedCat->id == $category->id) {
                                                    $isSelected = true;
                                                }
                                                elseif($selectedCat->parent_id == $category->id) {
                                                    $selectedSubCategoryId = $selectedCat->id;
                                                }
                                                elseif($selectedCat->level == 2) {
                                                    $parentCategory = $selectedCat->parent;
                                                    if($parentCategory && $parentCategory->parent_id == $category->id) {
                                                        $selectedSubCategoryId = $parentCategory->id;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input category-filter" type="radio" 
                                                   name="category" value="{{ $category->id }}" 
                                                   id="cat_{{ $category->id }}"
                                                   {{ $isSelected ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cat_{{ $category->id }}">
                                                <strong>{{ $category->category_name }}</strong>
                                            </label>
                                        </div>
                                        
                                        @php
                                            $categorySubCats = $subCategories->where('parent_id', $category->id);
                                        @endphp
                                        
                                        @if($categorySubCats->count() > 0)
                                            <div class="ps-4 mt-2">
                                                @foreach($categorySubCats as $subCat)
                                                    <div class="form-check common-check common-radio mb-2">
                                                        <input class="form-check-input category-filter" type="radio" 
                                                               name="category" value="{{ $subCat->id }}" 
                                                               id="cat_{{ $subCat->id }}"
                                                               {{ $selectedCategoryId == $subCat->id || $selectedSubCategoryId == $subCat->id ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="cat_{{ $subCat->id }}">
                                                            {{ $subCat->category_name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Brand Filter -->
                        @if($brands->count() > 0)
                        <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                            <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Filter by Brand</h6>
                            <ul class="max-h-540 overflow-y-auto scroll-sm">
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input brand-filter" type="radio" name="brand" value="" id="brand_all"
                                               {{ !request('brand') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="brand_all">
                                            All Brands
                                        </label>
                                    </div>
                                </li>
                                @foreach($brands as $brand)
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input brand-filter" type="radio" 
                                                   name="brand" value="{{ $brand->id }}" 
                                                   id="brand_{{ $brand->id }}"
                                                   {{ request('brand') == $brand->id ? 'checked' : '' }}>
                                            <label class="form-check-label" for="brand_{{ $brand->id }}">
                                                {{ $brand->name }}
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Price Range Filter -->
                        <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                            <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Filter by Price</h6>
                            <ul class="max-h-540 overflow-y-auto scroll-sm">
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input price-filter" type="radio" name="price" value="" id="price_all"
                                               {{ !request('price') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="price_all">
                                            All Prices
                                        </label>
                                    </div>
                                </li>
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input price-filter" type="radio" name="price" value="0-50" id="price_0_50"
                                               {{ request('price') == '0-50' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="price_0_50">
                                            {{ $genralsetting->currency }}0 - {{ $genralsetting->currency }}50
                                        </label>
                                    </div>
                                </li>
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input price-filter" type="radio" name="price" value="50-100" id="price_50_100"
                                               {{ request('price') == '50-100' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="price_50_100">
                                            {{ $genralsetting->currency }}50 - {{ $genralsetting->currency }}100
                                        </label>
                                    </div>
                                </li>
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input price-filter" type="radio" name="price" value="100-200" id="price_100_200"
                                               {{ request('price') == '100-200' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="price_100_200">
                                            {{ $genralsetting->currency }}100 - {{ $genralsetting->currency }}200
                                        </label>
                                    </div>
                                </li>
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input price-filter" type="radio" name="price" value="200-500" id="price_200_500"
                                               {{ request('price') == '200-500' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="price_200_500">
                                            {{ $genralsetting->currency }}200 - {{ $genralsetting->currency }}500
                                        </label>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Variant Filters -->
                        @foreach($allVariants as $variant)
                            @if($variant->variantValues->count() > 0)
                            <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                                <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Filter by {{ $variant->name }}</h6>
                                <ul class="max-h-540 overflow-y-auto scroll-sm">
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input variant-filter" type="radio" 
                                                   name="variant_value_id" value="" 
                                                   id="{{ Str::slug($variant->name) }}_all"
                                                   {{ !request('variant_value_id') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ Str::slug($variant->name) }}_all">
                                                All {{ $variant->name }}
                                            </label>
                                        </div>
                                    </li>
                                    @foreach($variant->variantValues as $value)
                                        <li class="mb-24">
                                            <div class="form-check common-check common-radio">
                                                <input class="form-check-input variant-filter" type="radio" 
                                                       name="variant_value_id" value="{{ $value->id }}" 
                                                       id="variant_value_{{ $value->id }}"
                                                       {{ request('variant_value_id') == $value->id ? 'checked' : '' }}>
                                                <label class="form-check-label" for="variant_value_{{ $value->id }}">
                                                    {{ $value->value }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        @endforeach

                        <!-- Discount Filter -->
                        <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                            <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Filter by Discount</h6>
                            <ul class="max-h-540 overflow-y-auto scroll-sm">
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input discount-filter" type="radio" name="discount" value="" id="discount_all"
                                               {{ !request('discount') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_all">
                                            All Products
                                        </label>
                                    </div>
                                </li>
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input discount-filter" type="radio" name="discount" value="10" id="discount_10"
                                               {{ request('discount') == '10' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_10">
                                            10% & above
                                        </label>
                                    </div>
                                </li>
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input discount-filter" type="radio" name="discount" value="20" id="discount_20"
                                               {{ request('discount') == '20' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_20">
                                            20% & above
                                        </label>
                                    </div>
                                </li>
                                <li class="mb-24">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input discount-filter" type="radio" name="discount" value="30" id="discount_30"
                                               {{ request('discount') == '30' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_30">
                                            30% & above
                                        </label>
                                    </div>
                                </li>
                                <li class="mb-0">
                                    <div class="form-check common-check common-radio">
                                        <input class="form-check-input discount-filter" type="radio" name="discount" value="40" id="discount_40"
                                               {{ request('discount') == '40' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discount_40">
                                            40% & above
                                        </label>
                                    </div>
                                </li>
                            </ul>
                                  <!-- Apply & Reset Buttons -->
                        <div class="d-grid gap-2 mt-5" >
                            <button type="button" id="resetFiltersBtn" class="btn btn-outline-main w-100 p-5 rounded-8">
                                <i class="ph ph-arrow-counter-clockwise me-2"></i> Reset All Filters
                            </button>
                        </div>
                        </div>

                     
                    </form>
                 
                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Content Start -->
            <div class="col-lg-9">
                <!-- Top Start -->
                <div class="flex-between gap-16 flex-wrap mb-40">
                    <span class="text-gray-900" id="productCount">
                        @if($products->total() > 0)
                            Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
                        @else
                            No products found
                        @endif
                    </span>
                    <div class="position-relative flex-align gap-16 flex-wrap">
                        <div class="list-grid-btns flex-align gap-16">
                            @php
                                $currentUrl = url()->current();
                                $queryParams = request()->query();
                                $queryParams['view'] = 'list';
                                $queryParams['page'] = 1;
                            @endphp
                            <a href="javascript:void(0);" 
                               data-view="list"
                               class="w-44 h-44 flex-center border border-gray-100 rounded-6 text-2xl view-btn {{ $view === 'list' ? 'bg-main-600 text-white border-main-600' : '' }}">
                                <i class="ph-bold ph-list-dashes"></i>
                            </a>
                            
                            @php
                                $queryParams['view'] = 'grid';
                            @endphp
                            <a href="javascript:void(0);" 
                               data-view="grid"
                               class="w-44 h-44 flex-center border border-gray-100 rounded-6 text-2xl view-btn {{ $view === 'grid' ? 'bg-main-600 text-white border-main-600' : '' }}">
                                <i class="ph ph-squares-four"></i>
                            </a>
                        </div>  
                        <div class="position-relative text-gray-500 flex-align gap-4 text-14">
                            <label for="sortSelect" class="text-inherit flex-shrink-0">Sort by: </label>
                            <select class="form-control common-input px-14 py-14 text-inherit rounded-6 w-auto" id="sortSelect">
                                <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Default sorting</option>
                                <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Sort by popularity</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Sort by rating</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Sort by latest</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Sort by price: low to high</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Sort by price: high to low</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Sort by name: A to Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Sort by name: Z to A</option>
                            </select>
                        </div>
                        <button type="button" class="w-44 h-44 d-lg-none d-flex flex-center border border-gray-100 rounded-6 text-2xl sidebar-btn">
                            <i class="ph-bold ph-funnel"></i>
                        </button>
                    </div>
                </div>
                <!-- Top End -->

                <!-- Products Container -->
                <div id="ajax-products-container">
                    @if($products->count() > 0)
                        @if($view === 'grid')
                            <div class="row gy-4">
                                @include('frontend.component.product-card-shop', [
                                    'products' => $products,
                                    'productRatings' => $ratings,
                                    'genralsetting' => $genralsetting
                                ])
                            </div>
                        @else
                            <div class="row gy-4">
                                @foreach($products as $product)
                                    <div class="col-lg-6 col-md-12">
                                        @include('frontend.component.product-card-list', ['product' => $product, 'ratings' => $ratings])
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($products->hasPages())
                            <ul class="pagination flex-center flex-wrap gap-16 mt-48">
                                @php
                                    $current = $products->currentPage();
                                    $last = $products->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                @endphp

                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100" 
                                           href="{{ $products->url(1) }}">01</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100">...</span>
                                        </li>
                                    @endif
                                @endif

                                @for($page = $start; $page <= $end; $page++)
                                    @if($page == $current)
                                        <li class="page-item active">
                                            <span class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-white border border-main-600 bg-main-600">
                                                {{ str_pad($page, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100" 
                                               href="{{ $products->url($page) }}">
                                                {{ str_pad($page, 2, '0', STR_PAD_LEFT) }}
                                            </a>
                                        </li>
                                    @endif
                                @endfor

                                @if($end < $last)
                                    @if($end < $last - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100" 
                                           href="{{ $products->url($last) }}">{{ str_pad($last, 2, '0', STR_PAD_LEFT) }}</a>
                                    </li>
                                @endif
                            </ul>
                        @endif
                    @else
                        <!-- No Products Found -->
                        <div class="text-center py-60">
                            <div class="mb-32">
                                <img src="{{ asset('frontend/template/assets/images/bg/no-products.png') }}" 
                                     alt="No Products Found" 
                                     class="img-fluid mb-24"
                                     style="max-width: 200px; opacity: 0.7;">
                                
                                <h4 class="mb-16">No Products Found</h4>
                                
                                <p class="text-gray-600 mb-32">
                                    Sorry, no products match your selected filters.<br>
                                    Please try adjusting your filters or browse other categories.
                                </p>
                                
                                <div class="d-flex justify-content-center gap-16 flex-wrap">
                                    <a href="{{ url()->current() }}" class="btn btn-outline-main rounded-pill px-32 py-12">
                                        <i class="ph ph-arrow-counter-clockwise me-2"></i> Clear All Filters
                                    </a>
                                    <a href="{{ route('products') }}" class="btn btn-main rounded-pill px-32 py-12">
                                        <i class="ph ph-squares-four me-2"></i> Browse All Products
                                    </a>
                                </div>
                            </div>
                            
                            @if(request()->anyFilled(['category', 'brand', 'price', 'variant_value_id', 'discount', 'stock']))
                                <div class="mt-32">
                                    <h6 class="mb-16">Active Filters:</h6>
                                    <div class="d-flex flex-wrap gap-12 justify-content-center">
                                        @if(request('category'))
                                            @php
                                                $cat = \App\Models\Category::find(request('category'));
                                            @endphp
                                            @if($cat)
                                                <span class="badge bg-light text-dark p-12 rounded-pill">
                                                    Category: {{ $cat->category_name }}
                                                    <a href="{{ request()->fullUrlWithoutQuery(['category']) }}" class="ms-2 text-danger">
                                                        <i class="ph ph-x"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        @endif
                                        
                                        @if(request('brand'))
                                            @php
                                                $brand = \App\Models\Brand::find(request('brand'));
                                            @endphp
                                            @if($brand)
                                                <span class="badge bg-light text-dark p-12 rounded-pill">
                                                    Brand: {{ $brand->name }}
                                                    <a href="{{ request()->fullUrlWithoutQuery(['brand']) }}" class="ms-2 text-danger">
                                                        <i class="ph ph-x"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        @endif
                                        
                                        @if(request('price'))
                                            <span class="badge bg-light text-dark p-12 rounded-pill">
                                                Price: {{ request('price') }}
                                                <a href="{{ request()->fullUrlWithoutQuery(['price']) }}" class="ms-2 text-danger">
                                                    <i class="ph ph-x"></i>
                                                </a>
                                            </span>
                                        @endif
                                        
                                        @if(request('variant_value_id'))
                                            @php
                                                $variant = \App\Models\VariantValue::find(request('variant_value_id'));
                                            @endphp
                                            @if($variant)
                                                <span class="badge bg-light text-dark p-12 rounded-pill">
                                                    {{ $variant->value }}
                                                    <a href="{{ request()->fullUrlWithoutQuery(['variant_value_id']) }}" class="ms-2 text-danger">
                                                        <i class="ph ph-x"></i>
                                                    </a>
                                                </span>
                                            @endif
                                        @endif
                                        
                                        @if(request('discount'))
                                            <span class="badge bg-light text-dark p-12 rounded-pill">
                                                {{ request('discount') }}% Off or more
                                                <a href="{{ request()->fullUrlWithoutQuery(['discount']) }}" class="ms-2 text-danger">
                                                    <i class="ph ph-x"></i>
                                                </a>
                                            </span>
                                        @endif
                                        
                                        @if(request('stock'))
                                            <span class="badge bg-light text-dark p-12 rounded-pill">
                                                In Stock Only
                                                <a href="{{ request()->fullUrlWithoutQuery(['stock']) }}" class="ms-2 text-danger">
                                                    <i class="ph ph-x"></i>
                                                </a>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <!-- Content End -->

        </div>
    </div>
</section>
<!-- Shop Section End -->

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== EXISTING CODE (Keep as is) ==========
    let filterTimeout;
    
    function startAllCountdowns() {
        document.querySelectorAll('[id^="countdown-"]').forEach(function(countdown) {
            const endDate = countdown.getAttribute('data-end-date');
            
            if (endDate) {
                const endTimestamp = parseInt(endDate) * 1000;
                
                if (countdown.intervalId) {
                    clearInterval(countdown.intervalId);
                }
                
                function updateCountdownDisplay() {
                    const now = new Date().getTime();
                    const distance = endTimestamp - now;
                    
                    const daysSpan = countdown.querySelector('.days');
                    const hoursSpan = countdown.querySelector('.hours');
                    const minutesSpan = countdown.querySelector('.minutes');
                    const secondsSpan = countdown.querySelector('.seconds');
                    
                    if (distance <= 0) {
                        if (daysSpan) daysSpan.innerHTML = '0';
                        if (hoursSpan) hoursSpan.innerHTML = '0';
                        if (minutesSpan) minutesSpan.innerHTML = '0';
                        if (secondsSpan) secondsSpan.innerHTML = '0';
                        return;
                    }
                    
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    
                    if (daysSpan) daysSpan.innerHTML = days;
                    if (hoursSpan) hoursSpan.innerHTML = hours < 10 ? '0' + hours : hours;
                    if (minutesSpan) minutesSpan.innerHTML = minutes < 10 ? '0' + minutes : minutes;
                    if (secondsSpan) secondsSpan.innerHTML = seconds < 10 ? '0' + seconds : seconds;
                }
                
                updateCountdownDisplay();
                countdown.intervalId = setInterval(updateCountdownDisplay, 1000);
            }
        });
    }
    
    // ========== EXISTING applyFilters function (Modified slightly to add loading class) ==========
    function applyFilters(params) {
        const container = document.getElementById('ajax-products-container');
        if (container) {
            // Add loading class for better UX
            container.classList.add('loading-products');
            container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-main-600"></div><p class="mt-2">Loading products...</p></div>';
        }
        
        const currentPath = window.location.pathname;
        const queryParams = new URLSearchParams(window.location.search);
        
        queryParams.delete('page');
        
        Object.keys(params).forEach(key => {
            if (params[key] && params[key] !== '') {
                queryParams.set(key, params[key]);
            } else {
                queryParams.delete(key);
            }
        });
        
        queryParams.set('ajax', '1');
        const queryString = queryParams.toString();
        
        fetch(`${currentPath}?${queryString}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (container) {
                    container.innerHTML = data.html;
                    container.classList.remove('loading-products');
                    
                    setTimeout(function() {
                        startAllCountdowns();
                    }, 200);
                }
                
                const productCount = document.getElementById('productCount');
                if (productCount) {
                    if (data.total > 0) {
                        productCount.textContent = `Showing ${data.stats?.from || 0}-${data.stats?.to || 0} of ${data.total} results`;
                    } else {
                        productCount.textContent = 'No products found';
                    }
                }
                
                const newUrl = `${currentPath}${queryString ? '?' + queryString.replace('&ajax=1', '') : ''}`;
                window.history.replaceState({}, '', newUrl);
            } else {
                window.location.href = `${currentPath}?${queryString}`;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            window.location.href = `${currentPath}?${queryString}`;
        });
    }
    
    // ========== NEW FUNCTION: Auto collect form data ==========
    function collectFormData() {
        const formData = new FormData(filterForm);
        const params = {};
        
        for (let [key, value] of formData.entries()) {
            if (value && value !== '') {
                // For radio buttons, check if they are checked
                const input = filterForm.querySelector(`[name="${key}"][value="${value}"]`);
                if (input && input.type === 'radio') {
                    if (input.checked) {
                        params[key] = value;
                    }
                } else {
                    params[key] = value;
                }
            }
        }
        
        return params;
    }
    
    // ========== NEW FUNCTION: Auto apply filters with debounce ==========
    function autoApplyFilters() {
        const params = collectFormData();
        
        // Preserve view if exists in URL but not in form
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('view') && !params.view) {
            params.view = urlParams.get('view');
        }
        
        applyFilters(params);
    }
    
    function autoApplyFiltersWithDebounce() {
        if (filterTimeout) {
            clearTimeout(filterTimeout);
        }
        
        // Show applying indicator
        let indicator = document.getElementById('applying-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'applying-indicator';
            indicator.innerHTML = '<small class="text-muted ms-2"><i class="ph ph-spinner ph-spin"></i> Applying...</small>';
            const productCount = document.getElementById('productCount');
            if (productCount) productCount.after(indicator);
        }
        indicator.style.display = 'inline-block';
        
        filterTimeout = setTimeout(function() {
            autoApplyFilters();
            if (indicator) indicator.style.display = 'none';
        }, 500);
    }
    
    // ========== NEW CODE: Auto-filter event listeners ==========
    const filterForm = document.getElementById('filterForm');
    
    if (filterForm) {
        // 1. All radio filters - auto trigger on change
        const allRadioFilters = document.querySelectorAll(
            '.category-filter, .brand-filter, .price-filter, .discount-filter, .variant-filter'
        );
        
        allRadioFilters.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    autoApplyFiltersWithDebounce();
                }
            });
        });
        
        // 2. Sort select - auto trigger on change
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            // Remove existing listeners by cloning
            const newSortSelect = sortSelect.cloneNode(true);
            sortSelect.parentNode.replaceChild(newSortSelect, sortSelect);
            
            newSortSelect.addEventListener('change', function() {
                const params = collectFormData();
                params.sort = this.value;
                applyFilters(params);
            });
        }
        
        // 3. View buttons (grid/list) - AJAX instead of link
        const viewBtns = document.querySelectorAll('.view-btn');
        viewBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const view = this.getAttribute('data-view');
                const params = collectFormData();
                params.view = view;
                params.page = 1;
                applyFilters(params);
            });
        });
        
        // 4. Reset button
    // 4. Reset button - Clear all filters and reload page
const resetBtn = document.getElementById('resetFiltersBtn');
if (resetBtn) {
    resetBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get current URL without any query parameters
        const currentUrl = window.location.pathname;
        
        // Simply redirect to the same page without any filters
        window.location.href = currentUrl;
    });
}
        
        // 5. Keep form submit for backward compatibility (but prevent default)
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            autoApplyFilters();
        });
    }
    
    // Initialize countdowns
    setTimeout(startAllCountdowns, 500);
});
</script>

<style>
/* Existing styles plus new loading style */
.product-card {
    display: flex !important;
    flex-direction: column !important;
    height: 550px !important;
    min-height: 550px !important;
    max-height: 550px !important;
    overflow: hidden !important;
}

.product-card__thumb {
    height: 280px !important;
    min-height: 280px !important;
    max-height: 280px !important;
    flex-shrink: 0 !important;
    width: 100% !important;
    overflow: hidden !important;
}

.product-card__thumb img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    max-width: 100% !important;
    max-height: 100% !important;
}

.product-card__content {
    flex: 1 1 auto !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    padding: 16px !important;
}

.product-card .title {
    height: 56px !important;
    min-height: 56px !important;
    max-height: 56px !important;
    overflow: hidden !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    line-height: 1.4 !important;
    margin: 12px 0 !important;
}

.product-card .d-flex.align-items-center.gap-6 {
    min-height: 24px !important;
    margin: 8px 0 !important;
}

.product-card .rounded-pill.text-main-two-600 {
    min-height: 32px !important;
    display: inline-flex !important;
    align-items: center !important;
    margin: 12px 0 !important;
}

.product-card .product-card__price {
    margin-top: auto !important;
    margin-bottom: 12px !important;
    min-height: 40px !important;
}

.product-card .product-card__cart {
    width: 100% !important;
    margin-top: 8px !important;
    flex-shrink: 0 !important;
}

.row > [class*="col-"] {
    display: flex !important;
    flex-direction: column !important;
    margin-bottom: 24px !important;
}

.countdown {
    position: absolute !important;
    bottom: 20px !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 10 !important;
}

.countdown-list {
    background: rgba(0,0,0,0.6) !important;
    border-radius: 8px !important;
    padding: 5px !important;
    margin: 0 10px !important;
}

.badge {
    padding: 8px 16px;
    background: #f8f9fa;
    border-radius: 50px;
    font-size: 14px;
    font-weight: normal;
}

.badge a {
    color: #dc3545;
    text-decoration: none;
}

.badge a:hover {
    color: #bb2d3b;
}

/* NEW: Loading animation style */
.loading-products {
    opacity: 0.6;
    transition: opacity 0.3s ease;
}

#applying-indicator {
    font-size: 12px;
    color: #6c757d;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.ph-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>