<!DOCTYPE html>
<html lang="en" class="color-two font-outfit header-border-0 header-style-two">

<!-- Mirrored from wowtheme7.com/tf/marketpro/index-three.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 20 Jan 2026 14:15:44 GMT -->

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @yield('seo')
    
    @php $integrations = \App\Models\Integration::first(); @endphp
    @if($integrations)
        {!! $integrations->google_console !!}
        {!! $integrations->google_analytics !!}
        {!! $integrations->google_tag_manager !!}
        {!! $integrations->meta_tags !!}
        {!! $integrations->schema_markup !!}
        {!! $integrations->on_page_scripts !!}
        {!! $integrations->facebook_pixel !!}
        {!! $integrations->remarketing_tags !!}
    @endif
    
    <!-- Dynamic SEO -->
    @if(isset($dynamicSeo))
        <title>{{ $dynamicSeo->meta_title ?? $dynamicSeo->page_name }}</title>
        <meta name="description" content="{{ $dynamicSeo->meta_description }}">
        <meta name="keywords" content="{{ $dynamicSeo->meta_keywords }}">
        <meta name="author" content="{{ $dynamicSeo->meta_author }}">
        <meta name="robots" content="{{ $dynamicSeo->meta_robots ?? 'index, follow' }}">
        <meta property="og:title" content="{{ $dynamicSeo->og_title ?? $dynamicSeo->meta_title }}">
        <meta property="og:description" content="{{ $dynamicSeo->og_description ?? $dynamicSeo->meta_description }}">
        <meta property="og:type" content="{{ $dynamicSeo->og_type ?? 'website' }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @if($dynamicSeo->meta_image)
            <meta property="og:image" content="{{ asset('storage/' . $dynamicSeo->meta_image) }}">
        @endif
    @else
        <title>{{ ucfirst(Request::segment(1)) ?: 'Home' }}</title>
    @endif
    
    <!-- Favicon -->
    @if ($siteSetting->favicon)
        <link rel="shortcut icon" href="{{ asset('storage/' . $siteSetting->favicon) }}">
    @else
        <link rel="shortcut icon" href="{{ asset('frontend/template/assets/images/logo/favicon.png')}}">
    @endif
    
    <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/main.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/template.css')}}">
    
    <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/select2.min.css')}}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/slick.css')}}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/jquery-ui.css')}}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/animate.css')}}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/aos.css')}}" media="print" onload="this.media='all'">
    
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" media="print" onload="this.media='all'">
    
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/select2.min.css')}}">
        <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/slick.css')}}">
        <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/jquery-ui.css')}}">
        <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/animate.css')}}">
        <link rel="stylesheet" href="{{ asset('frontend/template/assets/css/aos.css')}}">
    </noscript>

    @php
        $integration = \App\Models\Integration::first();
        $hasLiveChat = $integration && $integration->live_chat;
        $hasWhatsApp = $integration && $integration->whatsapp_on && $integration->phone_number;
        $scrollBottom = 20;
        $whatsappBottom = 80;
        if($hasLiveChat && $hasWhatsApp) {
            $scrollBottom = 90;
            $whatsappBottom = 170;
        } elseif($hasLiveChat || $hasWhatsApp) {
            $scrollBottom = 60;
            $whatsappBottom = 130;
        }
    @endphp
</head>

<body>



    <!--==================== Overlay Start ====================-->
    <div class="overlay"></div>
    <!--==================== Overlay End ====================-->

    <!--==================== Sidebar Overlay End ====================-->
    <div class="side-overlay"></div>
    <!--==================== Sidebar Overlay End ====================-->

   <!-- ==================== Scroll to Top End Here ==================== -->
    <div class="progress-wrap" style="margin-bottom: 10px !important; width: 48px !important; height: 48px !important;">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102" style="text-align: center !important;">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- ==================== Scroll to Top End Here ==================== -->

    <!-- ==================== Search Box Start Here ==================== -->
    <form action="#" class="search-box">
        <button type="button"
            class="search-box__close position-absolute inset-block-start-0 inset-inline-end-0 m-16 w-48 h-48 border border-gray-100 rounded-circle flex-center text-white hover-text-gray-800 hover-bg-white text-2xl transition-1">
            <i class="ph ph-x"></i>
        </button>
        <div class="container">
            <div class="position-relative">
                <input type="text" class="form-control py-16 px-24 text-xl rounded-pill pe-64"
                    placeholder="Search for a product or brand">
                <button type="submit"
                    class="w-48 h-48 bg-main-600 rounded-circle flex-center text-xl text-white position-absolute top-50 translate-middle-y inset-inline-end-0 me-8">
                    <i class="ph ph-magnifying-glass"></i>
                </button>
            </div>
        </div>
    </form>
    <!-- ==================== Search Box End Here ==================== -->

    <!-- ==================== Mobile Menu Start Here ==================== -->
    <div class="mobile-menu scroll-sm d-lg-none d-block">
        <button type="button" class="close-button"> <i class="ph ph-x"></i> </button>
        <div class="mobile-menu__inner">
            <a href="{{ route('home') }}" class="mobile-menu__logo">
                <img src="{{ asset('storage/' . $siteSetting->website_logo) }}" alt="Logo">
            </a>
            <div class="mobile-menu__menu">
                <!-- Nav Menu Start -->
                <ul class="nav-menu flex-align nav-menu--mobile">
                    <li class="nav-menu__item">
                        <a href="/" class="nav-menu__link text-heading-two">Home</a>

                    </li>
                    <li class="nav-menu__item">
                        <a href="{{ route('about') }}" class="nav-menu__link text-heading-two">About</a>

                    </li>

                    <li class="nav-menu__item">
                        <a href="{{route('vendor.index')}}" class="nav-menu__link text-heading-two">Vendors</a>

                    </li>
                    <li class="nav-menu__item">
                        <a href="{{route('contact')}}" class="nav-menu__link text-heading-two">Contact Us</a>
                    </li>

                </ul>
                <!-- Nav Menu End -->
            </div>
        </div>
    </div>
    <!-- ==================== Mobile Menu End Here ==================== -->


    <!-- ======================= Middle Header Two Start ========================= -->
    <header class="header-middle border-bottom border-neutral-40 py-4"
        style="height:60px; display:flex; align-items:center;">

        <div class="container container-lg">
            <nav class="header-inner flex-between gap-8" style="height:100%; display:flex; align-items:center;">

                <!-- Logo Start -->
                <div class="logo" style="display:flex; align-items:center;">
                    <a href="{{ route('home') }}" class="link">
                        <img src="{{ asset('storage/' . $siteSetting->website_logo) }}" alt="Logo"
                            style="max-height:60px; width:auto; object-fit:contain;">
                    </a>
                </div>
                <!-- Logo End  -->

                <!-- Menu Start  -->
                <div class="header-menu d-lg-block d-none">
                    <ul class="nav-menu flex-align">
                        <li class="nav-menu__item">
                            <a href="/" class="nav-menu__link text-heading-two">Home</a>
                        </li>

                        <li class="nav-menu__item">
                            <a href="{{ route('about') }}" class="nav-menu__link text-heading-two">About</a>
                        </li>



                        <li class="nav-menu__item">
                            <a href="{{ route('vendor.index') }}" class="nav-menu__link text-heading-two">Vendors</a>
                        </li>

                        <li class="nav-menu__item">
                            <a href="{{ route('contact') }}" class="nav-menu__link text-heading-two">Contact Us</a>
                        </li>
                    </ul>
                </div>
                <!-- Menu End  -->

                <!-- Middle Header Right start -->
                <!-- Middle Header Right start -->
                <div class="header-right flex-align">
                    <ul class="header-top__right style-two style-three flex-align flex-wrap gap-8">



                        @auth
                            @if(auth()->user()->user_type == 'webuser')
                                <div class="dropdown dropdown-hover me-2">
                                    <a href="javascript:void(0)"
                                        class="d-flex align-items-center gap-10 fw-medium text-main-600 py-12 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white"
                                        role="button">
                                        <span class="d-sm-flex d-none line-height-1">
                                            <i class="ph-bold ph-user"></i>
                                        </span>
                                        <span class="fw-medium">{{ Str::limit(auth()->user()->name, 15) }}</span>
                                        <i class="ph-bold ph-caret-down ms-1 fs-6"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-hover mt-2 py-2"
                                        style="right: 0; left: auto; ">
                                        <li>
                                            <hr class="dropdown-divider mx-3">
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 px-4" href="{{ route('user.dashboard') }}">
                                                <i class="ph-bold ph-gauge me-2"></i> Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 px-4" href="{{ route('user.orders') }}">
                                                <i class="ph-bold ph-shopping-bag me-2"></i> My Orders
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 px-4" href="{{ route('user.profile') }}">
                                                <i class="ph-bold ph-user-circle me-2"></i> Profile
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider mx-3">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('user.logout') }}" class="m-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 px-4 text-danger">
                                                    <i class="ph-bold ph-sign-out me-2"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <a href="{{ route('admin.dashboard') }}"
                                    class="d-flex align-items-center gap-10 fw-medium text-main-600 py-12 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white me-2">
                                    <span class="d-sm-flex d-none line-height-1">
                                        <i class="ph-bold ph-shield"></i>
                                    </span>
                                    <span>Admin</span>
                                </a>
                            @endif
                        @else
                            <div class="dropdown dropdown-hover me-2">
                                <a href="javascript:void(0)"
                                    class="d-flex align-items-center gap-10 fw-medium text-main-600 py-12 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white"
                                    role="button">
                                    <span class="d-sm-flex d-none line-height-1">
                                        <i class="ph-bold ph-user"></i>
                                    </span>
                                    <span>Account</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-hover mt-2 py-2"
                                    style="right: 0; left: auto;">
                                    <li>
                                        <a class="dropdown-item py-2 px-4" href="{{ route('user.login') }}">
                                            <i class="ph-bold ph-sign-in me-2"></i> Coustomer Login
                                        </a>
                                    </li>

                                    <li>
                                        <hr class="dropdown-divider mx-3">
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 px-4" href="{{ route('vendor.login') }}">
                                            <i class="ph-bold ph-storefront me-2"></i> Vendor Login
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        @endauth


@php
    $general = getGeneralSetting();
    $baseCurrency = $general->currency ?? 'PKR';
    $currentCurrency = session('user_currency', $baseCurrency);
@endphp

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
        {{ $currentCurrency }}
    </a>
    <ul class="dropdown-menu">
        <li>
            <form method="POST" action="{{ route('switch.currency') }}">
                @csrf
                <input type="hidden" name="currency" value="{{ $baseCurrency }}">
                <button type="submit" class="dropdown-item">{{ $baseCurrency }}</button>
            </form>
        </li>
        <li>
            <form method="POST" action="{{ route('switch.currency') }}">
                @csrf
                <input type="hidden" name="currency" value="USD">
                <button type="submit" class="dropdown-item">USD</button>
            </form>
        </li>
    </ul>
</li>

                    </ul>

                    <button type="button" class="toggle-mobileMenu d-lg-none ms-3 text-gray-800 text-4xl d-flex">
                        <i class="ph ph-list"></i>
                    </button>
                </div>
                <!-- Middle Header Right End -->

            </nav>
        </div>
    </header>
    <!-- ======================= Middle Header Two End ========================= -->

  <!-- ==================== Header Two Start Here ==================== -->
    <header class="header bg-white  pt-24">
        <div class="container container-lg">
            <nav class="header-inner d-flex justify-content-between gap-16">

                <div class="d-flex w-100">

                                     <div class="category d-block on-hover-item text-white flex-shrink-0 w-310">
                        <button type="button"
                            class="category__button flex-align gap-8 fw-medium p-16 bg-main-600 text-white rounded-top h-100 w-100">
                            <span class="icon text-2xl d-md-flex d-none"><i class="ph ph-squares-four"></i></span>
                            <span class="d-sm-flex d-none">All</span> Categories
                            <span class="arrow-icon text-xl d-flex ms-auto"><i class="ph ph-caret-down"></i></span>
                        </button>

                        <div
                            class="responsive-dropdown on-hover-dropdown common-dropdown nav-submenu p-0 submenus-submenu-wrapper">
                            <button type="button"
                                class="close-responsive-dropdown rounded-circle text-xl position-absolute inset-inline-end-0 inset-block-start-0 mt-4 me-8 d-lg-none d-flex">
                                <i class="ph ph-x"></i>
                            </button>

                            <div class="logo px-16 d-lg-none d-block">
                                <a href="{{ route('home') }}" class="link">
                                    <img src="{{ asset('frontend/template/assets/images/logo/logo.png') }}" alt="Logo">
                                </a>
                            </div>

                            <div class="d-flex flex-lg-row flex-column" id="categoryDropdown">
                                <ul class="scroll-sm p-0 py-8 overflow-y-auto border-end m-0"
                                    style="width: 250px; min-width: 250px; max-height: 500px;" id="mainCategoriesList">
                                    @forelse($navbarCategories as $index => $mainCategory)
                                        <li class="main-category-item {{ $index === 0 && $mainCategory->children->count() > 0 ? 'active' : '' }}"
                                            data-id="{{ $mainCategory->id }}"
                                            data-has-children="{{ $mainCategory->children->count() > 0 ? 'true' : 'false' }}">
                                            <a href="/products/{{ $mainCategory->url }}"
                                                class="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0 text-decoration-none">
                                                <span>{{ $mainCategory->category_name }}</span>
                                                @if($mainCategory->children->count() > 0)
                                                    <span class="icon text-md d-flex ms-auto"><i
                                                            class="ph ph-caret-right"></i></span>
                                                @endif
                                            </a>
                                        </li>
                                    @empty
                                        <li class="text-center py-3"><span class="text-gray-500">No categories found</span>
                                        </li>
                                    @endforelse
                                </ul>

                                <div class="p-4 bg-white overflow-y-auto" style="max-height: 500px;"
                                    id="categoriesContent">
                                    @foreach($navbarCategories as $index => $mainCategory)
                                        @if($mainCategory->children->count() > 0)
                                            <div id="content-{{ $mainCategory->id }}"
                                                class="category-content {{ $index === 0 ? '' : 'd-none' }}">
                                                <div class="categories-grid">
                                                    @foreach($mainCategory->children as $subCategory)
                                                        <div class="category-column">
                                                            <a href="/products/{{ $mainCategory->url }}/{{ $subCategory->url }}"
                                                                class="category-title text-decoration-none">
                                                                {{ $subCategory->category_name }}
                                                            </a>
                                                            @if($subCategory->children->count() > 0)
                                                                @foreach($subCategory->children as $subSubCategory)
                                                                    <a href="/products/{{ $mainCategory->url }}/{{ $subCategory->url }}/{{ $subSubCategory->url }}"
                                                                        class="subcategory-link text-decoration-none">
                                                                        {{ $subSubCategory->category_name }}
                                                                    </a>
                                                                @endforeach
                                                            @else
                                                                <p class="text-muted small mb-0 ps-3">No sub-categories</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                 

              
                                    <!-- Search Start  -->
<div class="position-relative ms-20 max-w-870 w-100 d-md-block d-none web-search-container">
    <input type="text"
        id="web-search-input"
        class="form-control fw-medium placeholder-italic shadow-none bg-neutral-30 placeholder-fw-medium placeholder-light py-16 ps-30 pe-60"
        placeholder="Search for products..."
        autocomplete="off">
    <button type="button"
        class="position-absolute top-50 translate-middle-y text-main-600 end-0 me-36 text-xl line-height-1">
        <i class="ph-bold ph-magnifying-glass"></i>
    </button>
    
    <!-- Search Results -->
    <div id="web-search-results" class="web-search-dropdown" style="display: none;">
        <div id="web-search-loading" class="text-center py-3" style="display: none;">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <span class="ms-2">Searching...</span>
        </div>
        <div id="web-search-content"></div>
    </div>
</div>
<!-- Search End  -->
                </div>

                <!-- Header Middle Right start -->
                <div class="d-flex align-items-center gap-20-px flex-shrink-0">
                    <a href="{{ route('compare') }}" class="flex-align gap-6 item-hover">
                        <span class="text-2xl text-heading d-flex position-relative me-6 mt-6 item-hover__text">
                            <i class="ph-bold ph-recycle"></i>

                            <span
                                class="compare-count w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs position-absolute top-n6 end-n4"
                                style="display: none;"></span>
                        </span>
                        <span
                            class="text-md text-neutral-500 item-hover__text fw-medium d-none d-lg-flex">Compare</span>
                    </a>

                    <!-- Cart (existing) -->
                    <a href="javascript:void(0)" class="flex-align gap-6 item-hover" data-bs-toggle="offcanvas"
                        data-bs-target="#shoppingCart">
                        <span class="text-2xl text-heading d-flex position-relative me-6 mt-6 item-hover__text">
                            <i class="ph-bold ph-shopping-cart"></i>
                            <span
                                class="cart-count w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs position-absolute top-n6 end-n4"
                                style="display: none;">0</span>
                        </span>
                        <span class="text-md text-neutral-500 item-hover__text fw-medium d-none d-lg-flex">Cart</span>
                    </a>

                    <!-- Wishlist - NEW: Same style as Compare & Cart -->
                    <a href="{{ route('wishlist') }}" class="flex-align gap-6 item-hover">
                        <span class="text-2xl text-heading d-flex position-relative me-6 mt-6 item-hover__text">
                            <i class="ph-bold ph-heart"></i>
                            <span
                                class="wishlist-count w-18 h-18 flex-center rounded-circle bg-success-600 text-white text-xs position-absolute top-n6 end-n4"
                                style="display: none;">0</span>
                        </span>
                        <span
                            class="text-md text-neutral-500 item-hover__text fw-medium d-none d-lg-flex">Wishlist</span>
                    </a>

                    <!-- Account (commented as per your request) -->
                    <!-- <a href="javascript:void(0)" class="d-flex align-content-around gap-10 fw-medium text-main-600 py-14 px-24 bg-main-50 rounded-pill line-height-1 hover-bg-main-600 hover-text-white">
        <span class="d-sm-flex d-none line-height-1"><i class="ph-bold ph-user"></i></span>
        Account
    </a> -->
                </div>
                <!-- Header Middle Right End  -->

            </nav>
        </div>
    </header>
    <!-- ==================== Header End Here ==================== -->


    @yield('content')

    <!-- =============================== Newsletter-two Section Start ============================ -->
    <div class="newsletter-two bg-neutral-600 py-32 overflow-hidden" style="margin-top:3px !important;" data-aos="fade-up" data-aos-duration="600">
        <div class="container container-lg">
            <div class="flex-between gap-20 flex-wrap">
                <div class="flex-align gap-22">
                    <span class="d-flex"><img src="{{ asset('frontend/template/assets/images/icon/envelop.png') }}" alt=""></span>
                    <div>
                        <h5 class="text-white mb-12 fw-medium">Join Our Newsletter, Get 10% Off</h5>
                        <p class="text-white fw-light">Get all latest information on events, sales and offer</p>
                    </div>
                </div>
                <form id="newsletterForm" class="newsletter-two__form w-50">
                    @csrf
                    <div class="flex-align gap-16">
                        <input type="email" class="common-input style-two rounded-8 flex-grow-1 py-14"
                            id="newsletter_email" name="email" placeholder="Enter your email address" required>
                        <button type="submit" class="btn btn-main-two flex-shrink-0 rounded-8 py-16" id="subscribeBtn">
                            <span class="btn-text">Subscribe</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="text-white mt-2" id="newsletter-error" style="font-size: 14px;"></div>
                </form>
            </div>
        </div>
    </div>
    <!-- =============================== Newsletter-two Section End ============================ -->
<!-- ==================== Footer Two Start Here ==================== -->
<footer class="footer py-80 overflow-hidden">
    <div class="container container-lg">
        <div class="row">
            
            <!-- Column 1: Logo, About, Contact, Social -->
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 wow fadeInUp" data-wow-delay="0.1s">
                <div class="footer-item">
                    <div class="footer-item__logo">
                        <a href="{{ route('home') }}"> 
                            <img src="{{ asset('storage/' . $siteSetting->footer_logo) }}" alt="logo" style="max-width: 100%; height: auto;">
                        </a>
                    </div>
                    <p class="mb-24" style="max-width: 400px;">
                        {{ $aboutExcerpt }}
                    </p>
                    <div class="flex-align gap-16 mb-16">
                        <span class="w-32 h-32 flex-center rounded-circle border border-gray-100 text-main-two-600 text-md flex-shrink-0">
                            <i class="ph-fill ph-phone-call"></i>
                        </span>
                        <a href="tel:{{ $genralsetting->country_code }}{{ $genralsetting->phone }}"
                            class="text-md text-gray-900 hover-text-main-600"> 
                            {{ $genralsetting->country_code }} {{ $genralsetting->phone }}
                        </a>
                    </div>
                    <div class="flex-align gap-16 mb-16">
                        <span class="w-32 h-32 flex-center rounded-circle border border-gray-100 text-main-two-600 text-md flex-shrink-0">
                            <i class="ph-fill ph-envelope"></i>
                        </span>
                        <a href="mailto:{{ $genralsetting->email }}"
                            class="text-md text-gray-900 hover-text-main-600">{{ $genralsetting->email }}</a>
                    </div>
                    <div class="flex-align gap-16 mb-16">
                        <span class="w-32 h-32 flex-center rounded-circle border border-gray-100 text-main-two-600 text-md flex-shrink-0">
                            <i class="ph-fill ph-map-pin"></i>
                        </span>
                        <span class="text-md text-gray-900">{{ $genralsetting->address }}</span>
                    </div>
                    @php 
                        $socials = \App\Models\SocialLink::where('is_active', 1)->get(); 
                    @endphp
                    @if($socials->isNotEmpty())
                        <ul class="d-flex align-items-center gap-16 mt-16 p-0 m-0 list-unstyled">
                            @foreach($socials as $social)
                                <li>
                                    <a href="{{ $social->url }}" target="_blank"
                                       class="w-44 h-44 d-flex justify-content-center align-items-center bg-main-two-50 text-main-two-600 text-xl rounded-8 hover-bg-main-two-600 hover-text-white transition-3">
                                        <i class="{{ $social->icon_class }}"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Column 2: Quicklinks -->
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="footer-item">
                    <h6 class="footer-item__title">Quicklinks</h6>
                    <ul class="footer-menu list-unstyled p-0">
                        <li class="nav-menu__item mb-2">
                            <a href="/" class="nav-menu__link text-heading-two">Home</a>
                        </li>
                        <li class="nav-menu__item mb-2">
                            <a href="{{ route('about') }}" class="nav-menu__link text-heading-two">About</a>
                        </li>
                        <li class="nav-menu__item mb-2">
                            <a href="{{ route('vendor.index') }}" class="nav-menu__link text-heading-two">Vendors</a>
                        </li>
                        <li class="nav-menu__item mb-2">
                            <a href="contact" class="nav-menu__link text-heading-two">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Column 3: Categories -->
            @if(!empty($navbarCategories) && $navbarCategories->isNotEmpty())
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="footer-item">
                        <h6 class="footer-item__title">Categories</h6>
                        <ul class="footer-menu list-unstyled p-0">
                            @foreach($navbarCategories->take(4) as $category)
                                <li class="nav-menu__item mb-2">
                                    <a href="/products/{{ $category->url }}" class="nav-menu__link text-heading-two">
                                        {{ $category->category_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Column 4: Our Policies -->
            @php 
                $footerPolicies = \App\Models\PageContent::where('status', 1)
                    ->select('policy_name', 'slug')
                    ->get(); 
            @endphp
            @if($footerPolicies->isNotEmpty())
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="footer-item">
                        <h6 class="footer-item__title">Our Policies</h6>
                        <ul class="footer-menu list-unstyled p-0">
                            @foreach($footerPolicies as $policy)
                                <li class="nav-menu__item mb-2">
                                    <a href="{{ route('policy.show', $policy->slug) }}" class="nav-menu__link text-heading-two">
                                        {{ $policy->policy_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </div>
</footer>



    <!-- bottom Footer -->
    <div class="bottom-footer bg-color-three py-8">
        <div class="container container-lg">
            <div class="bottom-footer__inner flex-between flex-wrap gap-16 py-16">
              <p class="copyright-text">
    Copyright © {{ now()->year }}. All rights reserved. {{ $footerName }} | Powered by <a href="https://www.diginotive.com/" target="_blank" class="text-main-600 hover-text-main-800">Diginotive</a>
</p>
                </p>
                <div class="flex-align gap-8 flex-wrap wow fadeInRightBig">
                    <span class="text-heading text-sm">We Are Accepting</span>
                    <img src="{{ asset('frontend/template/assets/images/thumbs/payment-method.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- ==================== Footer Two End Here ==================== -->

    {{-- Offcanvas Cart --}}
    <div id="shoppingCart" class="offcanvas offcanvas-end" data-bs-scroll="false">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title fw-semibold">Shopping Bag</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas">
                <i class="ph-bold ph-x"></i> 
            </button>
        </div>

        <div class="offcanvas-body">
            <div class="shopping-cart">
                <table class="table table-borderless">
                    <tbody id="offcanvasCartItemsBody">
                        <tr id="offcanvasEmptyState">
                            <td colspan="3" class="text-center py-10">
                                <div class="empty-cart">
                                    <i class="ph-bold ph-shopping-bag" style="font-size: 80px; color: #ccc;"></i>
                                    <h4 class="mt-4 mb-3">Your cart is empty</h4>
                                    <p class="text-muted mb-6">You haven't added any products to your cart yet.</p>
                                    <a href="{{ route('products') }}" class="btn btn-dark">Start Shopping</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="offcanvas-footer p-3 border-top" id="offcanvasCartFooter" style="display: none;">
            <div class="d-flex justify-content-between w-100 mb-3">
               <span class="fw-bold">Total Price:</span>
<span class="offcanvas-cart-total fw-bold">{{ getUserCurrency() }} 0.00</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn btn-dark w-100 mb-2">Check Out</a>
            <a href="{{ route('cart.page') }}" class="btn btn-dark w-100 mb-2">View Cart</a>
        </div>
    </div>
    @include('frontend.component.quick-view-modal')

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-40">
                    <!-- Step 1: Email -->
                    <div id="step1">
                        <p class="text-gray-600 mb-24">Enter your email address to receive a reset code.</p>
                        <input type="email" class="form-control common-input mb-24" id="resetEmail"
                            placeholder="Your email">
                        <button type="button" class="btn btn-main w-100 rounded-8" id="sendResetCodeBtn">Send Reset
                            Code</button>
                    </div>

                    <!-- Step 2: Code & New Password -->
                    <div id="step2" style="display: none;">
                        <p class="text-gray-600 mb-16">Enter the 6-digit code sent to <span id="displayEmail"
                                class="fw-semibold"></span></p>

                        <div class="mb-20">
                            <label class="form-label fw-semibold mb-8">Reset Code</label>
                            <input type="text" class="form-control common-input" id="resetCode" placeholder="123456"
                                maxlength="6">
                        </div>

                        <div class="mb-20">
                            <label class="form-label fw-semibold mb-8">New Password</label>
                            <input type="password" class="form-control common-input" id="newPassword"
                                placeholder="Enter new password">
                        </div>

                        <div class="mb-24">
                            <label class="form-label fw-semibold mb-8">Confirm Password</label>
                            <input type="password" class="form-control common-input" id="confirmPassword"
                                placeholder="Confirm new password">
                        </div>

                        <button type="button" class="btn btn-main w-100 rounded-8" id="resetPasswordBtn">Reset
                            Password</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
    <script src="{{ asset('frontend/template/assets/js/jquery-3.7.1.min.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    
    <!-- All other JS with defer (non-blocking) -->
    <script src="{{ asset('frontend/template/assets/js/boostrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/phosphor-icon.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/select2.min.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/slick.min.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/count-down.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/jquery-ui.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/wow.min.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/aos.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/marque.min.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/vanilla-tilt.min.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/counter.min.js') }}" defer></script>
    <script src="{{ asset('frontend/template/assets/js/main.js') }}" defer></script>

{{-- Chat & WhatsApp Integration --}}
@php
    $integration = \App\Models\Integration::first();
@endphp

{{-- WhatsApp Button - Size bhi thoda kam kiya --}}
@if($integration && $integration->whatsapp_on && $integration->phone_number)
<div id="whatsapp-widget">
    <button onclick="toggleWhatsappMenu()" 
            class="btn shadow-lg rounded-circle d-flex align-items-center justify-content-center border-0" 
            style="width: 48px; height: 48px; background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); outline: none !important;">
        <i class="fab fa-whatsapp text-white" style="font-size: 1.6rem;"></i>
        <span class="position-absolute top-0 end-0 bg-danger border border-2 border-white rounded-circle" 
              style="width: 12px; height: 12px; margin-top: 4px; margin-right: 4px;"></span>
    </button>

    <div id="whatsapp-menu" class="d-none bg-white rounded-3 overflow-hidden border-0 shadow-lg" 
         style="position: absolute; bottom: 65px; right: 0; width: 300px; box-shadow: 0 10px 40px rgba(0,0,0,0.2) !important;">
        
        <div class="p-3 text-white d-flex align-items-center" style="background: #075e54;">
            <div class="position-relative me-2">
                <div class="rounded-circle bg-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-person-fill text-secondary fs-4"></i>
                </div>
                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width: 12px; height: 12px;"></span>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 text-white fw-bold" style="font-size: 0.9rem;">Customer Support</h6>
                <div class="d-flex align-items-center small opacity-75 gap-1">
                    <i class="bi bi-circle-fill text-success" style="font-size: 6px;"></i>
                    <span style="font-size: 11px;">Online</span>
                </div>
            </div>
            <button onclick="toggleWhatsappMenu()" class="btn-close btn-close-white shadow-none" style="font-size: 0.6rem;"></button>
        </div>

        <div class="p-3" style="background-color: #f0f2f5;">
            <div class="bg-white p-2 rounded-3 shadow-sm mb-3">
                <p class="small text-muted fw-bold mb-1" style="font-size: 0.7rem;">Support Agent</p>
                <p class="small text-dark mb-0" style="line-height: 1.4; font-size: 0.8rem;">Hi there! 👋<br>How can we help?</p>
            </div>
            
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $integration->country_code . $integration->phone_number) }}?text=Hello%21%20I%20need%20help" 
               target="_blank" 
               class="btn w-100 fw-bold text-white rounded-3 d-flex align-items-center justify-content-center text-decoration-none shadow-sm" 
               style="background-color: #25d366; gap: 8px; border: none; font-size: 0.85rem; padding: 8px;">
                <i class="fab fa-whatsapp"></i>
                Start Conversation
            </a>
        </div>
    </div>
</div>

<script>
    
</script>
@endif

<script>
    window.appConfig = {
        routes: {
            cartAdd: '{{ route("cart.add") }}',
            cartCount: '{{ route("cart.count") }}',
            cartItems: '{{ route("cart.items") }}',
            cartUpdate: '{{ route("cart.update") }}',
            cartRemove: '{{ route("cart.remove") }}',
            cartClear: '{{ route("cart.clear") }}',
            cartTotalCalc: '{{ route("cart.total-for-calc") }}',
            cartShippingCalc: '{{ route("cart.calculate-shipping") }}',
            cartApplyCoupon: '{{ route("cart.apply-coupon") }}',
            cartValidateCoupon: '{{ route("cart.validate-coupon") }}',
            cartRemoveCoupon: '{{ route("cart.remove-coupon-session") }}',
            cartCouponStatus: '{{ route("cart.applied-coupon-status") }}',
            wishlistToggle: '{{ route("wishlist.toggle") }}',
            wishlistCount: '{{ route("wishlist.count") }}',
            wishlistCheck: '{{ route("wishlist.check") }}',
            wishlistRemove: '{{ route("wishlist.remove") }}',
            compareToggle: '{{ route("compare.toggle") }}',
            compareCount: '{{ route("compare.count") }}',
            compareCheck: '{{ route("compare.check") }}',
            compareRemove: '{{ route("compare.remove") }}',
            userLogin: '{{ route("user.login") }}',
            userRegister: '{{ route("user.register") }}',
            userProfileUpdate: '{{ route("user.profile.update") }}',
            userPasswordChange: '{{ route("user.password.change") }}',
            passwordSendCode: '{{ route("password.send-code") }}',
            passwordReset: '{{ route("password.reset") }}',
            newsletterSubscribe: '{{ route("newsletter.subscribe") }}',
            contactSubmit: '{{ route("contact.submit") }}',
            checkoutCartData: '{{ route("checkout.cart.data") }}',
            quickView: '/quick-view/',
            trendingProducts: '/trending-products',
            products: '{{ route("products") }}',
        },
      currencySymbol: '{{ getUserCurrency() }}',
        csrfToken: '{{ csrf_token() }}',
        siteUrl: '{{ url("/") }}',
        isUserLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
        userId: '{{ auth()->id() }}',
    };
</script>

<!-- Second: Your Template JS File -->
<script src="{{ asset('frontend/template/assets/js/template.js') }}"></script>

@if($integrations)
    {!! $integrations->live_chat !!}
    {!! $integrations->chatbot_scripts !!}
    {!! $integrations->messenger_chat !!}
    {!! $integrations->whatsapp_chat !!}
    {!! $integrations->conversion_tracking !!}
@endif
</body>

<!-- Mirrored from wowtheme7.com/tf/marketpro/index-three.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 20 Jan 2026 14:16:00 GMT -->

</html>