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
                    <a href="{{ route('user.dashboard') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                        Dashboard
                    </a>
                </li>
                <li class="flex-align">></li>
                <li class="text-sm">
                    <span class="text-main-600">My Orders</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- ================================ Orders Section Start ================================ -->
<section class="orders py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <!-- Sidebar -->
            <div class="col-xl-3 col-lg-4">
                <div class="card border border-gray-100 rounded-8 px-24 py-32 shadow-sm">
                    <div class="text-center mb-24">
                        <div class="position-relative d-inline-block mb-16">
                            @if($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" 
                                     class="rounded-circle border border-gray-200" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-main-100 rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <i class="ph-bold ph-user text-main-600 fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="mb-4">{{ $user->name }}</h5>
                        <p class="text-gray-600 small">{{ $user->email }}</p>
                    </div>
                    
                    <div class="nav flex-column nav-pills">
                        <a class="nav-link d-flex align-items-center gap-10 py-12 px-16 rounded-8 mb-8" 
                           href="{{ route('user.dashboard') }}">
                            <i class="ph-bold ph-gauge"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link active d-flex align-items-center gap-10 py-12 px-16 rounded-8 mb-8" 
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
                    <h4 class="mb-32">My Orders</h4>
                    
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table style-three">
                                <thead class="bg-light border-bottom">
                                    <tr>
                                        <th class="h6 mb-0 text-lg fw-bold text-start">Order #</th>
                                        <th class="h6 mb-0 text-lg fw-bold text-start">Date</th>
                                        <th class="h6 mb-0 text-lg fw-bold text-start">Items</th>
                                        <th class="h6 mb-0 text-lg fw-bold text-start">Total</th>
                                        <th class="h6 mb-0 text-lg fw-bold text-start">Status</th>
                                        <th class="h6 mb-0 text-lg fw-bold text-start">Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">#{{ $order->order_number }}</span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}<br>
                                                <small class="text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                {{ $order->items->count() }} item(s)
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $genralsetting->currency }} {{ number_format($order->grand_total, 2) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = match($order->status) {
                                                        'pending' => 'bg-warning',
                                                        'processing' => 'bg-info',
                                                        'shipped' => 'bg-primary',
                                                        'delivered' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }} text-white px-3 py-2">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} text-white">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-60">
                            <i class="ph-bold ph-shopping-bag text-gray-300" style="font-size: 80px;"></i>
                            <h5 class="mt-24 mb-16">No orders yet</h5>
                            <p class="text-gray-600 mb-32">You haven't placed any orders yet.</p>
                            <a href="{{ route('home') }}" class="btn btn-main px-5 py-3">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================================ Orders Section End ================================ -->
@endsection