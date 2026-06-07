@extends('frontend.layouts.app')

@section('content')
<script>
window.vendorData = {
    slug: "{{ $vendor->store_slug }}"
};
</script>

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
                    <span class="text-main-600">Vendors</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<section class="vendors-list py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <!-- Sidebar Start -->
            <div class="col-xxl-2 col-xl-3 col-lg-4">
                <div class="shop-sidebar">
                    <button type="button" class="shop-sidebar__close d-lg-none d-flex w-32 h-32 flex-center border border-gray-100 rounded-circle hover-bg-main-600 position-absolute inset-inline-end-0 me-10 mt-8 hover-text-white hover-border-main-600">
                        <i class="ph ph-x"></i>
                    </button>
                    
                    <div class="d-flex flex-column gap-12 px-lg-0 px-16 py-lg-0 py-24">
                        <!-- Vendor Info Card -->
                        <div class="vendor-card style-two text-center px-16 pb-24 bg-main-50">
                            @php
                                $profileImage = $vendor->user->image ?? null;
                                $vendorName = $vendor->store_name ?? $vendor->user->name ?? 'Vendor';
                            @endphp
                            
                            @if($profileImage)
                                <img src="{{ asset('storage/' . $profileImage) }}" alt="{{ $vendorName }}" class="vendor-card__logo m-12 rounded-circle" style="width: 70px; height: 70px; object-fit: cover;">
                            @else
                                <div class="vendor-card__logo m-12 bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 70px; height: 70px; font-size: 24px; font-weight: 600; color: #0aad0a;">
                                    {{ substr($vendorName, 0, 2) }}
                                </div>
                            @endif
                            
                            <h6 class="title mt-32">{{ $vendorName }}</h6>
                            
                            <span class="text-neutral-600 text-sm d-block fw-semibold">
                                <i class="ph ph-map-pin me-1"></i> {{ $vendor->city ?? '' }} {{ $vendor->country ?? 'Pakistan' }}
                            </span>
                            
                            <span class="bg-white text-neutral-900 rounded-pill py-6 px-16 text-12 mt-8">
                                <i class="ph ph-calendar me-1"></i> Since {{ $vendor->created_at->format('Y') }}
                            </span>
                            
                            <p class="text-neutral-600 my-24">
                                {{ $vendor->description ?? 'Welcome to ' . $vendorName . ' store. We provide quality products with best prices.' }}
                            </p>
                            
                       
                            
                            <a href="javascript:void(0)" class="btn btn-main rounded-pill py-16 px-32 mt-28 w-100">
                                <i class="ph ph-envelope me-2"></i> Contact Seller
                            </a>
                        </div>

                        @if($categories->count() > 0)
                        <div class="border border-gray-50 rounded-8 p-24">
                            <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Product Category</h6>
                            <ul class="max-h-326 overflow-y-auto scroll-sm">
                                @foreach($categories as $category)
                                <li class="mb-24">
                                    <a href="javascript:void(0)" 
                                       class="category-filter text-gray-900 hover-text-main-600 {{ request('category') == $category->id ? 'text-main-600 fw-semibold' : '' }}"
                                       data-category="{{ $category->id }}">
                                        {{ $category->category_name }} ({{ $category->products_count }})
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                     <div class="border border-gray-50 rounded-8 p-24">
    <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Filter by Price</h6>
    
    <!-- 🔥 PURE CUSTOM SLIDER - No template classes -->
    <div class="custom-price-slider-wrapper">
        <!-- Slider container -->
        <div id="custom-price-range" class="custom-price-range"></div>
        
        <!-- Controls -->
        <div class="flex-between flex-wrap-reverse gap-8 mt-24">
            <button type="button" id="custom-price-filter-btn" class="btn btn-main h-40 flex-align">Filter</button>
            <div class="flex-align gap-8">
                <span class="text-gray-500 text-md flex-shrink-0">Price:</span>
                <input type="text" class="custom-price-amount text-neutral-600 text-start text-md fw-medium border-0 bg-transparent" id="custom-amount" readonly style="width: 180px;">
            </div>
        </div>
    </div>
</div>




                    </div>
                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Products Section Start -->
            <div class="col-xxl-10 col-xl-9 col-lg-8">
                <div class="flex-between flex-wrap gap-8 mb-40">
                    <form action="javascript:void(0)" method="GET" id="search-form" class="search-form__wrapper position-relative d-block">
                        <input type="text" name="search" value="{{ request('search') }}" class="search-form__input common-input py-13 ps-16 pe-18 rounded-pill pe-44" placeholder="Search products in this store..." id="search-input">
                        <button type="submit" class="w-32 h-32 bg-main-600 rounded-circle flex-center text-xl text-white position-absolute top-50 translate-middle-y inset-inline-end-0 me-8">
                            <i class="ph ph-magnifying-glass"></i>
                        </button>
                    </form>
                    
                    <div class="flex-align gap-16">
                        <span class="text-neutral-600 fw-medium px-40 py-12 rounded-pill border border-neutral-100 d-md-block d-none" id="result-stats">
                            Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
                        </span>
                        
                        <div class="flex-align gap-8">
                            <span class="text-gray-900 flex-shrink-0">Sort by:</span>
                            <select class="common-input form-select rounded-pill border border-gray-100 d-inline-block ps-20 pe-36 h-48 py-0 fw-medium" id="sort-select">
                                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                            </select>
                        </div>
                        
                        <button type="button" class="w-44 h-44 d-lg-none d-flex flex-center border border-gray-100 rounded-6 text-2xl sidebar-btn">
                            <i class="ph-bold ph-funnel"></i>
                        </button>
                    </div>
                </div>

                <!-- Products Container -->
                <div id="products-container">
                    @if($products->count() > 0)
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-12">
                            @include('frontend.component.product-card', [
                                'products' => $products,
                                'productRatings' => $ratings,
                                'genralsetting' => $genralsetting ?? (object)['currency' => '$']
                            ])
                        </div>
                    @else
                        <div class="text-center py-60">
                            <img src="{{ asset('frontend/template/assets/images/bg/no-products.png') }}" alt="No Products" class="img-fluid mb-24" style="max-width: 200px;">
                            <h4 class="mb-16">No Products Found</h4>
                            <p class="text-gray-600">This vendor has no products yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Pagination Container -->
<div id="pagination-container" class="mt-48">
    @if($products->hasPages())
        @php
            $current = $products->currentPage();
            $last = $products->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
        @endphp

        <ul class="pagination flex-center flex-wrap gap-16 mt-48">
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
</div>
            </div>
            <!-- Products Section End -->
        </div>
    </div>
</section>
@endsection
