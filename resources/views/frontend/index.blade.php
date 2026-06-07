@extends('frontend.layouts.app')

@section('content')

<!-- ==================================== Banner Three Start =================================== -->

@if($herobaner->count() > 1)
    <div id="heroCarousel" class="carousel slide position-relative" data-bs-ride="carousel" data-bs-interval="3000"
        style="height: 88vh;">

        <!-- Carousel Inner -->
        <div class="carousel-inner h-100">
            @foreach($herobaner as $index => $banner)
                <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                    <!-- Aapka existing banner content same rahega -->
                    <div class="position-relative h-100">
                        @if($banner->banner_video && $banner->banner_video_status)
                            <video class="w-100 h-100" autoplay muted loop playsinline style="object-fit: cover;">
                                <source src="{{ asset('storage/' . $banner->banner_video) }}" type="video/mp4">
                            </video>
                        @else
                            <img src="{{ asset('storage/' . $banner->image) }}" class="d-none d-md-block w-100 h-100"
                                style="object-fit: cover;" alt="{{ $banner->alt ?? '' }}">
                            @if($banner->mob_banner_image)
                                <img src="{{ asset('storage/' . $banner->mob_banner_image) }}" class="d-block d-md-none w-100 h-100 banner-mobile-img"
                                    style="object-fit: cover;" alt="{{ $banner->alt ?? '' }}">
                            @else
                                <img src="{{ asset('storage/' . $banner->image) }}" class="d-block d-md-none w-100 h-100 banner-mobile-img"
                                    style="object-fit: cover;" alt="{{ $banner->alt ?? '' }}">
                            @endif
                        @endif

                        @if($banner->title || $banner->tagline || $banner->description)
                            <div class="position-absolute top-0 start-10 w-50 h-100 d-flex align-items-center p-5" >
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-10 col-12 text-white px-4">
                                            @if($banner->title)
                                                <h6 class="text-uppercase fw-semibold mb-2">{{ $banner->title }}</h6>
                                            @endif
                                            @if($banner->tagline)
                                                <h2 class="fw-bold display-5 mb-3">{{ $banner->tagline }}</h2>
                                            @endif
                                            @if($banner->description)
                                                <p class="lead mb-4">{{ $banner->description }}</p>
                                            @endif
                                            <a href="shop" class="btn btn-lg btn-dark mt-3">Shop Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Arrows - Category wali ki tarah -->
        <div class="position-absolute top-50 start-0 translate-middle-y ms-4 z-3">
            <button
                class="carousel-control-prev-custom flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1"
                type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"
                style="width: 48px; height: 48px; border: none;">
                <i class="ph ph-caret-left"></i>
            </button>
        </div>

        <div class="position-absolute top-50 end-0 translate-middle-y me-4 z-3">
            <button
                class="carousel-control-next-custom flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1"
                type="button" data-bs-target="#heroCarousel" data-bs-slide="next"
                style="width: 48px; height: 48px; border: none;">
                <i class="ph ph-caret-right"></i>
            </button>
        </div>
    </div>

@else
    <!-- Single banner code same rahega -->
    @foreach($herobaner as $banner)
        <div class="position-relative banner-single" style="height: 88vh;">
            @if($banner->banner_video && $banner->banner_video_status)
                <video class="w-100 h-100" autoplay muted loop playsinline style="object-fit: cover;">
                    <source src="{{ asset('storage/' . $banner->banner_video) }}" type="video/mp4">
                </video>
            @else
                <img src="{{ asset('storage/' . $banner->image) }}" class="d-none d-md-block w-100 h-100" style="object-fit: cover;"
                    alt="{{ $banner->alt ?? '' }}">

                @if($banner->mob_banner_image)
                    <img src="{{ asset('storage/' . $banner->mob_banner_image) }}" class="d-block d-md-none w-100 h-100 banner-mobile-img"
                        style="object-fit: cover;" alt="{{ $banner->alt ?? '' }}">
                @else
                    <img src="{{ asset('storage/' . $banner->image) }}" class="d-block d-md-none w-100 h-100 banner-mobile-img"
                        style="object-fit: cover;" alt="{{ $banner->alt ?? '' }}">
                @endif
            @endif

            @if($banner->title || $banner->tagline || $banner->description)
                <div class="position-absolute top-0 start-0 w-50 h-100 d-flex align-items-center p-5">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-8 col-md-10 col-12 text-white px-4">
                                @if($banner->title)
                                    <h6 class="text-uppercase fw-semibold mb-2">{{ $banner->title }}</h6>
                                @endif
                                @if($banner->tagline)
                                    <h2 class="fw-bold display-5 mb-3">{{ $banner->tagline }}</h2>
                                @endif
                                @if($banner->description)
                                    <p class="lead mb-4">{{ $banner->description }}</p>
                                @endif
                                <a href="shop" class="btn btn-bg btn-dark mt-3">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    @endforeach
@endif



<!-- ==================================== Banner Three End =================================== -->

        <!-- ============================== category Banner Start ========================== -->
        <section class="promo-three pt-120 wow bounce overflow-hidden">
            <div class="container container-lg">
                <div class="row gy-4">
                    @forelse($bannerCategories as $index => $category)
                        <div class="col-sm-6" data-aos="zoom-in" data-aos-duration="800">
                            <div class="promo-three-item bg-img rounded-16 overflow-hidden"
                                data-background-image="{{ asset('storage/' . $category->category_banner) }}" style="background-image: url('{{ asset('storage/' . $category->category_banner) }}'); 
                                                background-size: cover; 
                                                background-position: center; 
                                                background-repeat: no-repeat;
                                                min-height: 400px;
                                                width: 100%;
                                                position: relative;">

                                <div class="text-start"
                                    style="position: absolute; top: 50%; left: 40px; transform: translateY(-50%); z-index: 2;">

                                    <h2 class="text-white fw-medium mb-0 max-w-375" style="font-size: 2.5rem; line-height: 1.2;">
                                        @php
    $words = explode(' ', $category->category_name);
    $firstWord = $words[0] ?? '';
    $restWords = array_slice($words, 1);
                                        @endphp

                                        {{ $firstWord }}
                                        @if(!empty($restWords))
                                            <span class="fw-normal text-white font-heading-four wow bounceInDown" data-wow-duration="1s"
                                                data-wow-delay="{{ $loop->first ? '.5s' : '.7s' }}">
                                                {{ implode(' ', $restWords) }}
                                            </span>
                                        @endif
                                    </h2>

                                    @if($category->category_discount > 0)
                                        <p class="text-white mt-3 mb-2" style="font-size: 1.2rem; opacity: 0.9;">
                                            {{ $category->category_discount }}% Off
                                        </p>
                                    @endif

                                    @if($category->description)
                                        <p class="text-white mt-2 mb-4" style="font-size: 1rem; max-width: 350px; opacity: 0.8;">
                                            {{ $category->description }}
                                        </p>
                                    @endif

                                    <a href="{{ url('/products/' . $category->url) }}"
                                        class="btn btn-outline-white d-inline-flex align-items-center rounded-pill gap-2 mt-4 px-4 py-2"
                                        style="border-width: 2px; font-weight: 500; transition: all 0.3s ease;">
                                        Explore Now
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                                stroke-linecap="round" />
                                        </svg>
                                    </a>
                                </div>

                                <div
                                    style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.1) 50%, transparent 100%);">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-sm-6" data-aos="zoom-in" data-aos-duration="800">
                            <div class="promo-three-item bg-img rounded-16 overflow-hidden"
                                data-background-image="{{ asset('frontend/template/assets/images/thumbs/promo-three-img-1.png') }}"
                                style="background-image: url('{{ asset('frontend/template/assets/images/thumbs/promo-three-img-1.png') }}'); 
                                                background-size: cover; 
                                                background-position: center; 
                                                min-height: 400px;
                                                position: relative;">
                                <div class="text-start"
                                    style="position: absolute; top: 50%; left: 40px; transform: translateY(-50%);">
                                    <span class="text-white mb-2 d-block" style="font-size: 1rem; opacity: 0.9;">Free Shipping Over
                                        Order $150</span>
                                    <h2 class="text-white fw-medium mb-0 max-w-375" style="font-size: 2.5rem;">Woman <span
                                            class="fw-normal text-white font-heading-four wow bounceInDown" data-wow-duration="1s"
                                            data-wow-delay=".5s">Spring</span> Collection</h2>
                                    <a href="shop.html"
                                        class="btn btn-outline-white d-inline-flex align-items-center rounded-pill gap-2 mt-4 px-4 py-2">
                                        Explore Now
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                                stroke-linecap="round" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6" data-aos="zoom-in" data-aos-duration="800">
                            <div class="promo-three-item bg-img rounded-16 overflow-hidden"
                                data-background-image="{{ asset('frontend/template/assets/images/thumbs/promo-three-img-2.png') }}"
                                style="background-image: url('{{ asset('frontend/template/assets/images/thumbs/promo-three-img-2.png') }}'); 
                                                background-size: cover; 
                                                background-position: center; 
                                                min-height: 400px;
                                                position: relative;">
                                <div class="text-start"
                                    style="position: absolute; top: 50%; left: 40px; transform: translateY(-50%);">
                                    <span class="text-white mb-2 d-block" style="font-size: 1rem; opacity: 0.9;">Men Fashion
                                        Discover</span>
                                    <h2 class="text-white fw-medium mb-0 max-w-375" style="font-size: 2.5rem;">New <span
                                            class="fw-normal text-white font-heading-four wow bounceInDown" data-wow-duration="1s"
                                            data-wow-delay=".7s">Style</span> Sale 35% Off</h2>
                                    <a href="shop.html"
                                        class="btn btn-outline-white d-inline-flex align-items-center rounded-pill gap-2 mt-4 px-4 py-2">
                                        Explore Now
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                                stroke-linecap="round" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
        <!-- ============================ main category Section start =============================== -->
        <div class="feature feature-three mt-0 py-120 overflow-hidden" id="featureSection">
            <div class="container container-lg">
                <div class="section-heading text-center">
                    <h5 class="mb-0 wow bounceIn text-uppercase">Popular Categories</h5>
                </div>
                <div class="position-relative arrow-center">
                    <div class="flex-align">
                        <button type="button" id="feature-item-wrapper-prev"
                            class="slick-prev slick-arrow flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                            <i class="ph ph-caret-left"></i>
                        </button>
                        <button type="button" id="feature-item-wrapper-next"
                            class="slick-next slick-arrow flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                            <i class="ph ph-caret-right"></i>
                        </button>
                    </div>
                    <div class="feature-three-item-wrapper">
                        @forelse($navbarCategories as $category)
                            <div class="feature-item text-center" data-aos="zoom-in" data-aos-duration="800">
                                <div
                                    class="feature-item__thumb bg-{{ ['yellow', 'danger', 'purple', 'warning', 'success'][$loop->index % 5] }}-light max-w-260 max-h-260 rounded-circle w-100 h-100">
                                    <a href="{{ url('/products/' . $category->url) }}" class="w-100 h-100 flex-center">
                                        @if($category->category_image)
                                            <img src="{{ asset('storage/' . $category->category_image) }}"
                                                alt="{{ $category->category_name }}">
                                        @else
                                            <img src="{{ asset('frontend/template/assets/images/thumbs/features-three-img' . ($loop->index + 1) . '.png') }}"
                                                alt="{{ $category->category_name }}">
                                        @endif
                                    </a>
                                </div>
                                <div class="feature-item__content mt-20">
                                    <h6 class="text-lg mb-8">
                                        <a href="{{ url('/products/' . $category->url) }}" class="text-inherit">
                                            {{ $category->category_name }}
                                        </a>
                                    </h6>

                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p>No categories found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================ main category Section End =============================== -->



        <!-- text slider -->
        @if($announcements && $announcements->count() > 0)
            <div class="text-slider-section overflow-hidden bg-neutral-600 py-28" data-aos="fade-up">
                <div class="text-slider d-flex align-items-center gap-4">

                    @php
    $timeBased = $announcements->filter(function ($ann) {
        return $ann->start_at && $ann->end_at;
    });

    $displayAnnouncements = $timeBased->count() > 0 ? $timeBased : $announcements;

    $duplicateCount = 5;
                    @endphp

                    @for($i = 0; $i < $duplicateCount; $i++)
                        @foreach($displayAnnouncements as $announcement)
                            <div class="d-flex flex-nowrap flex-shrink-0 flx-align gap-32">
                                <span class="flex-shrink-0">
                                    <img src="{{ asset('frontend/template/assets/images/icon/star-color.png') }}" alt="">
                                </span>
                                <h4 class="text-white flex-grow-1 mb-0 fw-medium">
                                    {{ strip_tags($announcement->message) }}
                                </h4>
                            </div>
                        @endforeach
                    @endfor

                </div>
            </div>
        @endif
        <!-- text slider End -->




    <!-- ========================= Trending Products Start ================================ -->
    <section class="trending-products-three py-120 overflow-hidden">
        <div class="container container-lg">
            <div class="section-heading mb-24">
                <div class="flex-between flex-wrap gap-8">
                    <h5 class="mb-0 text-uppercase">Trending Products</h5>
                    <ul class="nav common-tab style-two nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">All Products</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="pills-sale-tab" data-bs-toggle="pill" data-bs-target="#pills-sale" type="button" role="tab" aria-controls="pills-sale" aria-selected="false">On Sale</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-featured-tab" data-bs-toggle="pill" data-bs-target="#pills-featured" type="button" role="tab" aria-controls="pills-featured" aria-selected="false">Featured Products</button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content" id="pills-tabContent">

                <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
                    <div class="row g-12" id="trending-products-container">
                        @if($products->count() > 0)
                            @include('frontend.component.product-card', [
        'products' => $products,
        'productRatings' => $productRatings ?? []
    ])
                        @else
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No products available</p>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="tab-pane fade" id="pills-sale" role="tabpanel" aria-labelledby="pills-sale-tab" tabindex="0">
                    <div class="row g-12">
                        @if(isset($onSaleProducts) && $onSaleProducts->count() > 0)
                            @include('frontend.component.product-card', [
        'products' => $onSaleProducts->take(8),
        'productRatings' => $productRatings ?? []
    ])
                        @else
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No active sale products available</p>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="tab-pane fade" id="pills-featured" role="tabpanel" aria-labelledby="pills-featured-tab" tabindex="0">
                    <div class="row g-12">
                        @if($featuredProducts->count() > 0)
                            @include('frontend.component.product-card', [
        'products' => $featuredProducts->take(8),
        'productRatings' => $productRatings ?? []
    ])
                        @else
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No featured products available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <div class="text-center mt-48" id="trending-button-container">
                @if(isset($hasMoreTrending) && $hasMoreTrending)
                    <button class="fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white" id="view-more-trending">
                        View More <i class="ph ph-arrow-right ms-2"></i>
                    </button>
                @else
                    <a href="{{ route('products') }}" class="fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white">
                        Shop Now <i class="ph ph-arrow-right ms-2"></i>
                    </a>
                @endif
            </div>
        </div>
    </section>


    </div>
    <!-- ========================= Trending Products End ================================ -->



        <!-- ========================= Discount Three Start ================================ -->
 <section class="discount-three overflow-hidden">
            <div class="container container-lg">
                <div class="row gy-4">  
                @foreach ($middlebaner as $secbanner)

                    <div class="col-xl-4 col-sm-6" data-aos="zoom-in" data-aos-duration="800">
                                        <div class="discount-three-item bg-img rounded-16 overflow-hidden"
                                                data-background-image="{{ asset('storage/' . $secbanner->image) }}">
                                                    <div class="text-start">
                                                            <span class="fw-medium text-neutral-600 mb-4 text-uppercase">{{ $secbanner->tagline ?? ''}}</span>
                                                                <h6 class="fw-semibold mb-0 max-w-375"> {{ $secbanner->title ?? ''}}</h6>
                                                                <a href="{{  $secbanner->link }}" class="btn btn-black rounded-pill gap-8 mt-32 flex-align d-inline-flex"
                                                                        tabindex="0">
                                                                           {{ $secbanner->title ?? ''}}
                                                                            <span class="text-xl d-flex"><i class="ph ph-shopping-cart-simple"></i></span>
                                                                                </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                @endforeach
                                        </div>
                                    </div>
                                </section>
        <!-- ========================= Discount Three End ================================ -->



<!-- ========================== New Arrival Three Section Start ===================================== -->
@php
    use App\Models\Category;
    
    // Get main categories that have at least one subcategory with products
    $arrivalMainCategories = Category::where('level', 0)
        ->where('status', 1)
        ->whereHas('children', function($q) {
            $q->where('status', 1)
              ->whereHas('products', function($pq) {
                  $pq->where('status', 1)->where('stock', '>', 0);
              });
        })
        ->orderBy('category_name')
        ->limit(8)
        ->get();
    
    // Default banner images for variation
    $defaultBannerImages = [
        asset('frontend/template/assets/images/thumbs/new-arrival-promo-img1.png'),
        asset('frontend/template/assets/images/thumbs/new-arrival-promo-img2.png')
    ];
@endphp

<section class="new-arrival-three" style="padding: 60px 0; overflow: hidden;">
    <div class="container container-lg">
        <div class="section-heading text-center wow bounceIn">
            <h5 class="mb-0 text-uppercase wow bounceIn" style="font-size: 24px;">New Arrivals</h5>
        </div>
        
        <!-- Tabs Navigation - Only categories with products will show -->
        @if($arrivalMainCategories->count() > 0)
        <ul class="nav common-tab style-two nav-pills justify-content-center mb-30 wow bounceIn" style="margin-bottom: 30px;" id="arrivalPillsTab" role="tablist">
            @foreach($arrivalMainCategories as $arrivalIndex => $arrivalCategory)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $arrivalIndex == 0 ? 'active' : '' }}" 
                            style="padding: 6px 20px; font-size: 14px;"
                            id="arrivalPills-{{ $arrivalCategory->id }}-tab" 
                            data-bs-toggle="pill" 
                            data-bs-target="#arrivalPills-{{ $arrivalCategory->id }}" 
                            type="button" 
                            role="tab">
                        {{ $arrivalCategory->category_name }}
                    </button>
                </li>
            @endforeach
        </ul>
        @endif

        <!-- Tabs Content -->
        <div class="tab-content" id="arrivalPillsTabContent">
            @foreach($arrivalMainCategories as $arrivalMainIndex => $arrivalMainCategory)
                @php
                    // Get subcategories that have products
                    $arrivalSubcategories = $arrivalMainCategory->children()
                        ->where('status', 1)
                        ->whereHas('products', function($q) {
                            $q->where('status', 1)->where('stock', '>', 0);
                        })
                        ->with(['products' => function($q) {
                            $q->where('status', 1)
                              ->where('stock', '>', 0)
                              ->orderBy('created_at', 'desc')
                              ->limit(3);
                        }])
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                @endphp
                
                <div class="tab-pane fade {{ $arrivalMainIndex == 0 ? 'show active' : '' }}" 
                     id="arrivalPills-{{ $arrivalMainCategory->id }}" 
                     role="tabpanel">
                    
                    @if($arrivalSubcategories->count() > 0)
                        <div class="new-arrival-three-wrapper">
                            @foreach($arrivalSubcategories as $subIndex => $arrivalSub)
                                @php
                                    $allProducts = $arrivalSub->products;
                                    $productChunks = $allProducts->chunk(3);
                                    $isOddRow = (($subIndex + 1) % 2 != 0);
                                    
                                    // Banner check - if no banner, use default image with variation
                                    $hasCustomBanner = false;
                                    $bannerUrl = '';
                                    
                                    if(!empty($arrivalSub->category_banner) && $arrivalSub->banner_status == 1) {
                                        $hasCustomBanner = true;
                                        $bannerUrl = asset('storage/' . $arrivalSub->category_banner);
                                    } else {
                                        // Use default banner with variation based on subcategory ID
                                        $bannerIndex = ($arrivalSub->id % 2 == 0) ? 0 : 1;
                                        $bannerUrl = $defaultBannerImages[$bannerIndex];
                                    }
                                @endphp
                                
                                @foreach($productChunks as $chunkIndex => $productChunk)
                                    @php
                                        $isFirstChunk = ($chunkIndex == 0);
                                        $rowClass = ($subIndex > 0 || $chunkIndex > 0) ? 'mt-20' : '';
                                    @endphp
                                    
                                    @if($isOddRow)
                                        <!-- Odd Row: Banner Left, Products Right -->
                                        <div class="row gy-3 {{ $rowClass }}" style="margin-bottom: 0;">
                                            <!-- Banner Column - Full Height, Text at Top -->
                                            <div class="col-xl-4" style="display: flex;">
                                                <div class="arrival-banner-wrapper" style="border-radius: 20px; overflow: hidden; border: 1px solid #eee; padding: 12px; background: #fafafa; width: 100%; display: flex;">
                                                    <div class="arrival-banner-img" style="background-image: url('{{ $bannerUrl }}'); background-size: cover; background-position: center; background-repeat: no-repeat; width: 100%; min-height: 380px; height: 100%; border-radius: 20px; overflow: hidden; position: relative;">
                                                        <div class="arrival-banner-content" style="position: absolute; top: 0; left: 0; right: 0; padding: 32px 32px 0 0; text-align: right;">
                                                            <span class="arrival-banner-label" style="text-transform: uppercase; font-weight: 500; font-size: 12px; color: #fff; display: block;">
                                                                {{ $hasCustomBanner ? 'Special Offer' : 'New Collection' }}
                                                            </span>
                                                            <h5 class="arrival-banner-title" style="margin: 5px 0 0 0; font-size: 20px; color: #fff;">
                                                                {{ $arrivalSub->category_name }} Collection
                                                            </h5>
                                                            <a href="{{ url('/products/' . $arrivalSub->url) }}"
                                                                class="arrival-banner-btn" style="display: inline-flex; align-items: center; gap: 8px; background: #000; color: #fff; padding: 10px 28px; border-radius: 50px; margin-top: 20px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                                                                Shop Now
                                                                <span><i class="ph ph-shopping-cart-simple"></i></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Products Column -->
                                            <div class="col-xl-8">
                                                <div class="row gy-3">
                                                    @foreach($productChunk as $product)
                                                        @include('frontend.component.product-card-newar', ['products' => collect([$product])])
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Even Row: Products Left, Banner Right -->
                                        <div class="row gy-3 mt-20" style="margin-bottom: 0;">
                                            <!-- Products Column -->
                                            <div class="col-xl-8">
                                                <div class="row gy-3">
                                                    @foreach($productChunk as $product)
                                                        @include('frontend.component.product-card-newar', ['products' => collect([$product])])
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!-- Banner Column - Full Height, Text at Top -->
                                            <div class="col-xl-4" style="display: flex;">
                                                <div class="arrival-banner-wrapper" style="border-radius: 20px; overflow: hidden; border: 1px solid #eee; padding: 12px; background: #fafafa; width: 100%; display: flex;">
                                                    <div class="arrival-banner-img" style="background-image: url('{{ $bannerUrl }}'); background-size: cover; background-position: center; background-repeat: no-repeat; width: 100%; min-height: 380px; height: 100%; border-radius: 20px; overflow: hidden; position: relative;">
                                                        <div class="arrival-banner-content" style="position: absolute; top: 0; left: 0; right: 0; padding: 32px 32px 0 0; text-align: right;">
                                                            <span class="arrival-banner-label" style="text-transform: uppercase; font-weight: 500; font-size: 11px; color: #fff; display: block;">
                                                                {{ $hasCustomBanner ? 'Get extra discount' : 'Limited Time' }}
                                                            </span>
                                                            <h5 class="arrival-banner-title" style="margin: 5px 0 0 0; font-size: 20px; color: #fff;">
                                                                {{ $arrivalSub->category_name }} Collection
                                                            </h5>
                                                            <a href="{{ url('/products/' . $arrivalSub->url) }}"
                                                                class="arrival-banner-btn" style="display: inline-flex; align-items: center; gap: 8px; background: #000; color: #fff; padding: 10px 28px; border-radius: 50px; margin-top: 20px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                                                                Shop Now
                                                                <span><i class="ph ph-shopping-cart-simple"></i></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- ========================== New Arrival Three Section End ===================================== -->







    
<!-- ================================ Special Offer Section Start =================================== -->
@if(isset($offerBanner) && $offerBanner)
    <section id="special_offer_banner" class="deals pb-120">
        <div class="container container-lg">
            <div class="row g-0 align-items-center">
                <!-- Left Side - Banner Image (Desktop & Mobile) -->
                <div class="col-lg-6 mb-12 mb-lg-0">
                    <!-- Mobile Image (visible only on mobile) -->
                    @if($offerBanner->mob_banner_image)
                        <img data-src="{{ Storage::url($offerBanner->mob_banner_image) }}" 
                             alt="{{ $offerBanner->alt ?? 'Special Offer Banner' }}" 
                             class="img-fluid w-100 loaded d-block d-lg-none" 
                             width="705" 
                             height="620" 
                             src="{{ Storage::url($offerBanner->mob_banner_image) }}" 
                             loading="lazy">
                    @elseif($offerBanner->image)
                        <!-- Fallback: agar mobile image nahi hai to desktop image mobile pe show karo -->
                        <img data-src="{{ Storage::url($offerBanner->image) }}" 
                             alt="{{ $offerBanner->alt ?? 'Special Offer Banner' }}" 
                             class="img-fluid w-100 loaded d-block d-lg-none" 
                             width="705" 
                             height="620" 
                             src="{{ Storage::url($offerBanner->image) }}" 
                             loading="lazy">
                    @endif
                    
                    <!-- Desktop Image (visible only on desktop) -->
                    @if($offerBanner->image)
                        <img data-src="{{ Storage::url($offerBanner->image) }}" 
                             alt="{{ $offerBanner->alt ?? 'Special Offer Banner' }}" 
                             class="img-fluid w-100 loaded d-none d-lg-block" 
                             width="705" 
                             height="620" 
                             src="{{ Storage::url($offerBanner->image) }}" 
                             loading="lazy">
                    @else
                        <img data-src="{{ asset('frontend/template/assets/images/thumbs/banner-inner-img.png') }}" 
                             alt="banner" 
                             class="img-fluid w-100 loaded" 
                             width="705" 
                             height="620" 
                             src="{{ asset('frontend/template/assets/images/thumbs/banner-inner-img.png') }}" 
                             loading="lazy">
                    @endif
                </div>
                
                <!-- Right Side - Offer Content -->
                <div class="col-xl-5 col-lg-6 offset-xl-1 ps-lg-10 pe-xl-18">
                    <!-- Tagline with Badge -->
                    <p class="text-uppercase text-body-emphasis fw-semibold ls-1 d-flex align-items-center pb-2">
                        {{ $offerBanner->tagline ?? 'Special offer' }}
                        
                        @if($offerBanner->product_discount ?? false)
                            <span class="badge bg-primary fs-15px py-3 px-5 ms-5 ls-0 fw-bold lh-12">
                                -{{ $offerBanner->product_discount }}%
                            </span>
                        @endif
                    </p>
                    
                    <!-- Title -->
                    <h2 class="mb-5">
                        {{ $offerBanner->title ?? 'Save on Sets' }}
                    </h2>
                    
                    <!-- Description -->
                    <p class="fs-18px mb-5">
                        {{ $offerBanner->description ?? 'Made using clean, non-toxic ingredients, our products are designed for everyone.' }}
                    </p>
                    
                    <!-- Countdown Timer - Only for active dated banners -->
                    @if($offerBanner->start_date && $offerBanner->end_date && $offerBanner->end_date >= now())
                        <div class="d-flex countdown ms-n4 ms-md-n7" 
                             id="countdown-offer"
                             data-end-date="{{ strtotime($offerBanner->end_date) }}">
                            <div class="countdown-item text-center px-md-7 px-4 fs-1">
                                <span class="days fw-semibold text-primary font-primary">00</span>
                                <span class="mt-8 text-neutral-600 text-xl text-uppercase fw-medium d-block text-center">Days</span>
                            </div>
                            <div class="separate fw-semibold fs-1 text-primary">:</div>
                            <div class="countdown-item text-center px-md-7 px-4 fs-1">
                                <span class="hours fw-semibold text-primary font-primary">00</span>
                                <span class="mt-8 text-neutral-600 text-xl text-uppercase fw-medium d-block text-center">Hours</span>
                            </div>
                            <div class="separate fw-semibold fs-1 text-primary">:</div>
                            <div class="countdown-item text-center px-md-7 px-4 fs-1">
                                <span class="minutes fw-semibold text-primary font-primary">00</span>
                                <span class="mt-8 text-neutral-600 text-xl text-uppercase fw-medium d-block text-center">Mins</span>
                            </div>
                            <div class="separate fw-semibold fs-1 text-primary">:</div>
                            <div class="countdown-item text-center px-md-7 px-4 fs-1">
                                <span class="seconds fw-semibold text-primary font-primary">00</span>
                                <span class="mt-8 text-neutral-600 text-xl text-uppercase fw-medium d-block text-center">Secs</span>
                            </div>
                        </div>
                    @endif
                    
                    <!-- CTA Button -->
                    @if($offerBanner->link)
                        <a href="{{ $offerBanner->link }}" class="btn btn-dark btn-hover-bg-primary btn-hover-border-primary mt-9">
                            {{ $offerBanner->alt ?? 'View Offer' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif
<!-- ================================ Special Offer Section End =================================== -->


         <!-- ============================= Popular Products Three start ============================ -->
<section class="popular-products-three pb-120 overflow-hidden">
    <div class="container container-lg">
        <div class="section-heading mb-24">
            <h5 class="mb-0 text-uppercase wow fadeInLeft">Popular Products</h5>
        </div>
        <div class="row gy-12">
            @if(isset($popularProducts) && $popularProducts->count() > 0)
                @include('frontend.component.product-card', [
                    'products' => $popularProducts,
                    'productRatings' => $productRatings ?? []
                ])
            @else
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No popular products available</p>
                </div>
            @endif
        </div>
    </div>
</section>
<!-- ============================= Popular Products Three End ============================ -->

   <!-- ================================ Brand Three Start ============================= -->
<div class="top-brand pb-80 overflow-hidden">
    <div class="container container-lg">
        <div class="border border-gray-100 p-24 rounded-16">
            <div class="section-heading mb-24">
                <div class="flex-between flex-wrap gap-8">
                    <h5 class="mb-0 text-uppercase">Top Brands</h5>
                    <div class="flex-align gap-8">
                        <button type="button" id="topBrand-prev"
                            class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-two-600 text-xl hover-bg-main-two-600 hover-text-white transition-1">
                            <i class="ph ph-caret-left"></i>
                        </button>
                        <button type="button" id="topBrand-next"
                            class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-two-600 text-xl hover-bg-main-two-600 hover-text-white transition-1">
                            <i class="ph ph-caret-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="top-brand__slider">
                @php
                    $staticImages = [
                        asset('frontend/template/assets/images/thumbs/brand-three-img1.png'),
                        asset('frontend/template/assets/images/thumbs/brand-three-img2.png'),
                        asset('frontend/template/assets/images/thumbs/brand-three-img3.png'),
                        asset('frontend/template/assets/images/thumbs/brand-three-img4.png'),
                        asset('frontend/template/assets/images/thumbs/brand-three-img5.png'),
                        asset('frontend/template/assets/images/thumbs/brand-three-img6.png'),
                        asset('frontend/template/assets/images/thumbs/brand-three-img7.png'),
                        asset('frontend/template/assets/images/thumbs/brand-three-img8.png'),
                    ];
                    
                    // Get active brands
                    $activeBrands = $brands->where('status', 1)
                                           ->whereNotNull('image')
                                           ->values();
                    
                    // Create array for brand slots
                    $brandSlots = [];
                    
                    // First fill with dynamic brands
                    foreach($activeBrands as $brand) {
                        $brandSlots[] = [
                            'image' => asset('storage/' . $brand->image),
                            'alt' => $brand->name,
                            'is_dynamic' => true
                        ];
                    }
                    
                    // Fill remaining slots with static images
                    $staticCount = count($staticImages);
                    $dynamicCount = count($brandSlots);
                    $totalSlots = max($staticCount, $dynamicCount);
                    
                    for($i = $dynamicCount; $i < $totalSlots; $i++) {
                        $brandSlots[] = [
                            'image' => $staticImages[$i % $staticCount],
                            'alt' => 'Brand',
                            'is_dynamic' => false
                        ];
                    }
                @endphp

                @foreach($brandSlots as $slot)
                    <div class="wow bounceIn">
                        <div style="width: 120px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; background: #fff; border-radius: 50%; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                            <img style="width: 100%; height: 100%; object-fit: contain; padding: 15px;" src="{{ $slot['image'] }}" alt="{{ $slot['alt'] }}">
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
<!-- ================================ Brand Three End ============================= -->

       <!-- ========================== Shipping Section Start ============================ -->
<section class="shipping mt-80 pb-120" id="shipping">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="400">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-two-50 hover-bg-main-100 transition-2">
                    <span
                        class="w-56 h-56 flex-center rounded-circle bg-main-two-600 text-white text-32 flex-shrink-0"><i
                            class="ph-fill ph-car-profile"></i></span>
                    <div class="">
                        <h6 class="mb-0">Free Shipping</h6>
                        <span class="text-sm text-heading">Free shipping across Pakistan</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="600">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-two-50 hover-bg-main-100 transition-2">
                    <span
                        class="w-56 h-56 flex-center rounded-circle bg-main-two-600 text-white text-32 flex-shrink-0"><i
                            class="ph-fill ph-hand-heart"></i></span>
                    <div class="">
                        <h6 class="mb-0">100% Satisfaction</h6>
                        <span class="text-sm text-heading">Quality assured premium products</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="800">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-two-50 hover-bg-main-100 transition-2">
                    <span
                        class="w-56 h-56 flex-center rounded-circle bg-main-two-600 text-white text-32 flex-shrink-0"><i
                            class="ph-fill ph-credit-card"></i></span>
                    <div class="">
                        <h6 class="mb-0">Secure Payments</h6>
                        <span class="text-sm text-heading">COD & online payment available</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="1000">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-two-50 hover-bg-main-100 transition-2">
                    <span
                        class="w-56 h-56 flex-center rounded-circle bg-main-two-600 text-white text-32 flex-shrink-0"><i
                            class="ph-fill ph-chats"></i></span>
                    <div class="">
                        <h6 class="mb-0">24/7 Support</h6>
                        <span class="text-sm text-heading">Dedicated customer support team</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================== Shipping Section End ============================ -->

<!-- ============================== Testimonial section start ======================= -->
        <section class="testimonials py-120 bg-neutral-600 bg-img overflow-hidden"
            data-background-image="{{ asset('frontend/template/assets/images/bg/pattern-two.png') }}">
            <div class="container container-lg">
                <div class="row gy-4 align-items-center">
                    <div class="col-xl-1">
                        <div class="section-heading mb-0 d-flex flex-column align-items-center writing-mode wow fadeInLeft">
                            <p class="text-white">Share information about your brand with your customers.</p>
                            <h5 class="text-white mb-0 text-uppercase">Customers Feedback</h5>
                        </div>
                    </div>
                    <div class="col-xl-11">
                        <div class="position-relative">
                            <div class="testimonials-slider mb-60">
                                <div class="testimonials-item">
                                    <h6 class="text-white text-uppercase mb-8 fw-medium">FARHAN AHMED</h6>
                                    <span class="text-md text-white fw-normal">Business Owner</span>
                                    <div class="flex-align gap-8 mt-24">
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                    </div>
                                    <p class="testimonials-item__desc text-white text-2xl fw-normal mt-40 max-w-990">MM-MP has completely transformed my wardrobe! The quality of their fabrics and attention to detail is exceptional. Every piece I've purchased feels premium and the customer service is top-notch. Highly recommend to anyone looking for stylish yet comfortable clothing.</p>
                                </div>
                                <div class="testimonials-item">
                                    <h6 class="text-white text-uppercase mb-8 fw-medium">SANA MALIK</h6>
                                    <span class="text-md text-white fw-normal">Fashion Blogger</span>
                                    <div class="flex-align gap-8 mt-24">
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                    </div>
                                    <p class="testimonials-item__desc text-white text-2xl fw-normal mt-40 max-w-990">As someone who reviews fashion brands regularly, MM-MP stands out for their unique designs and consistent quality. Their collections are always on-trend and the pieces are perfect for both casual and formal occasions. Definitely my go-to brand now!</p>
                                </div>
                                <div class="testimonials-item">
                                    <h6 class="text-white text-uppercase mb-8 fw-medium">BILAL RASHEED</h6>
                                    <span class="text-md text-white fw-normal">Corporate Professional</span>
                                    <div class="flex-align gap-8 mt-24">
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                    </div>
                                    <p class="testimonials-item__desc text-white text-2xl fw-normal mt-40 max-w-990">I've been shopping with MM-MP for over a year now and never been disappointed. The shipping is always on time, packaging is elegant, and the products match exactly what's shown online. Their formal wear collection is particularly impressive for office and events.</p>
                                </div>
                                <div class="testimonials-item">
                                    <h6 class="text-white text-uppercase mb-8 fw-medium">ZARA KHAN</h6>
                                    <span class="text-md text-white fw-normal">Student</span>
                                    <div class="flex-align gap-8 mt-24">
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                    </div>
                                    <p class="testimonials-item__desc text-white text-2xl fw-normal mt-40 max-w-990">MM-MP offers amazing value for money! The quality is far better than other brands in the same price range. I love how their designs are both modern and modest. Great customer support too - they helped me with sizing queries promptly.</p>
                                </div>
                                <div class="testimonials-item">
                                    <h6 class="text-white text-uppercase mb-8 fw-medium">USMAN CHAUDHRY</h6>
                                    <span class="text-md text-white fw-normal">Entrepreneur</span>
                                    <div class="flex-align gap-8 mt-24">
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                        <span class="text-xs fw-medium text-warning-600 d-flex"><i
                                                class="ph-fill ph-star"></i></span>
                                    </div>
                                    <p class="testimonials-item__desc text-white text-2xl fw-normal mt-40 max-w-990">Impressed by MM-MP's commitment to quality and customer satisfaction. The fabric feels luxurious and the stitching is flawless. Their new arrivals always catch my attention. Perfect blend of traditional aesthetics with contemporary style. Will continue shopping here!</p>
                                </div>
                            </div>
                            <div class="testimonials-thumbs-slider">
                                <div class="testimonials-thumbs d-flex position-relative align-items-end justify-content-end">
                                    <div class="testimonials-thumbs__img">
                                        <img src="{{ asset('frontend/template/assets/images/thumbs/testimonials-img1.png') }}"
                                            alt="" class="cover-img">
                                    </div>
                                    <div
                                        class="testimonials-thumbs__content position-absolute transition-2 bottom-0 start-50 translate-middle-x mb-16 text-center hidden opacity-0">
                                        <h6 class="text-white text-uppercase mb-8 fw-medium">FARHAN AHMED</h6>
                                        <span class="text-md text-white fw-normal">Business Owner</span>
                                    </div>
                                </div>
                                <div class="testimonials-thumbs d-flex position-relative align-items-end justify-content-end">
                                    <div class="testimonials-thumbs__img">
                                        <img src="{{ asset('frontend/template/assets/images/thumbs/testimonials-img2.png') }}"
                                            alt="" class="cover-img">
                                    </div>
                                    <div
                                        class="testimonials-thumbs__content position-absolute transition-2 bottom-0 start-50 translate-middle-x mb-16 text-center hidden opacity-0">
                                        <h6 class="text-white text-uppercase mb-8 fw-medium">SANA MALIK</h6>
                                        <span class="text-md text-white fw-normal">Fashion Blogger</span>
                                    </div>
                                </div>
                                <div class="testimonials-thumbs d-flex position-relative align-items-end justify-content-end">
                                    <div class="testimonials-thumbs__img">
                                        <img src="{{ asset('frontend/template/assets/images/thumbs/testimonials-img3.png') }}"
                                            alt="" class="cover-img">
                                    </div>
                                    <div
                                        class="testimonials-thumbs__content position-absolute transition-2 bottom-0 start-50 translate-middle-x mb-16 text-center hidden opacity-0">
                                        <h6 class="text-white text-uppercase mb-8 fw-medium">BILAL RASHEED</h6>
                                        <span class="text-md text-white fw-normal">Corporate Professional</span>
                                    </div>
                                </div>
                                <div class="testimonials-thumbs d-flex position-relative align-items-end justify-content-end">
                                    <div class="testimonials-thumbs__img">
                                        <img src="{{ asset('frontend/template/assets/images/thumbs/testimonials-img4.png') }}"
                                            alt="" class="cover-img">
                                    </div>
                                    <div
                                        class="testimonials-thumbs__content position-absolute transition-2 bottom-0 start-50 translate-middle-x mb-16 text-center hidden opacity-0">
                                        <h6 class="text-white text-uppercase mb-8 fw-medium">ZARA KHAN</h6>
                                        <span class="text-md text-white fw-normal">Student</span>
                                    </div>
                                </div>
                                <div class="testimonials-thumbs d-flex position-relative align-items-end justify-content-end">
                                    <div class="testimonials-thumbs__img">
                                        <img src="{{ asset('frontend/template/assets/images/thumbs/testimonials-img2.png') }}"
                                            alt="" class="cover-img">
                                    </div>
                                    <div
                                        class="testimonials-thumbs__content position-absolute transition-2 bottom-0 start-50 translate-middle-x mb-16 text-center hidden opacity-0">
                                        <h6 class="text-white text-uppercase mb-8 fw-medium">USMAN CHAUDHRY</h6>
                                        <span class="text-md text-white fw-normal">Entrepreneur</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="flex-center gap-8 mt-48">
                    <button type="button" id="testi-prev"
                        class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 text-white transition-1">
                        <i class="ph ph-caret-left"></i>
                    </button>
                    <button type="button" id="testi-next"
                        class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 text-white transition-1">
                        <i class="ph ph-caret-right"></i>
                    </button>
                </div>
            </div>
        </section>
        <!-- ============================== Testimonial section start ======================= -->



@endsection