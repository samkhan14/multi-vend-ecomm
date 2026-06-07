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
                <li class="flex-align">></li>
                <li class="text-sm">
                    <span class="text-main-600">Dashboard</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- ================================ Dashboard Section Start ================================ -->
<section class="dashboard py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <!-- Sidebar -->
            <div class="col-xl-3 col-lg-4">
                <div class="card border border-gray-100 rounded-8 px-24 py-32 shadow-sm">
                    <div class="text-center mb-24">
                        <div class="position-relative d-inline-block mb-16">
                            @if(auth()->user()->image)
                                <img src="{{ asset('storage/' . auth()->user()->image) }}" 
                                     class="rounded-circle border border-gray-200" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-main-100 rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <i class="ph-bold ph-user text-main-600 fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="mb-4">{{ auth()->user()->name }}</h5>
                        <p class="text-gray-600 small">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <div class="nav flex-column nav-pills">
                        <a class="nav-link active d-flex align-items-center gap-10 py-12 px-16 rounded-8 mb-8" 
                           href="{{ route('user.dashboard') }}">
                            <i class="ph-bold ph-gauge"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link d-flex align-items-center gap-10 py-12 px-16 rounded-8 mb-8" 
                           href="{{ route('user.orders') }}">
                            <i class="ph-bold ph-shopping-bag"></i>
                            <span>My Orders</span>
                        </a>
                        <a class="nav-link d-flex align-items-center gap-10 py-12 px-16 rounded-8 mb-8" 
                           href="{{ route('user.profile') }}">
                            <i class="ph-bold ph-user-circle"></i>
                            <span>Profile</span>
                        </a>
                        <form method="POST" action="{{ route('user.logout') }}" class="mt-16">
                            @csrf
                            <button type="submit" class="nav-link d-flex align-items-center gap-10 py-12 px-16 rounded-8 w-100 text-start text-danger hover-bg-danger-50">
                                <i class="ph-bold ph-sign-out"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-xl-9 col-lg-8">
                <div class="card border border-gray-100 rounded-8 px-40 py-48 shadow-sm">
                    <div class="mb-32">
                        <h4 class="mb-8">Dashboard Overview</h4>
                        <p class="text-gray-600">Welcome back, <span class="fw-semibold text-main-600">{{ auth()->user()->name }}</span>!</p>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="row g-16 mb-40">
                        <div class="col-sm-4">
                            <div class="bg-main-50 rounded-8 p-24 text-center">
                                <i class="ph-bold ph-shopping-bag text-main-600 fs-2 mb-16"></i>
                                <h3 class="mb-4">{{ $totalOrders ?? 0 }}</h3>
                                <p class="text-gray-600 mb-0">Total Orders</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-main-50 rounded-8 p-24 text-center">
                                <i class="ph-bold ph-heart text-main-600 fs-2 mb-16"></i>
                                <h3 class="mb-4">{{ $wishlistCount ?? 0 }}</h3>
                                <p class="text-gray-600 mb-0">Wishlist Items</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-main-50 rounded-8 p-24 text-center">
                                <i class="ph-bold ph-clock text-main-600 fs-2 mb-16"></i>
                                <h3 class="mb-4">{{ auth()->user()->created_at->format('M Y') }}</h3>
                                <p class="text-gray-600 mb-0">Member Since</p>
                            </div>
                        </div>
                    </div>
                    
                
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================================ Dashboard Section End ================================ -->
@endsection