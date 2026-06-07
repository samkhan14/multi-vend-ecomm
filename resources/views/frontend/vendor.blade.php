@extends('frontend.layouts.app')

@section('content')


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
<!-- =================================== Vendors List section start ===================================== -->
<section class="vendors-list py-80">
    <div class="container container-lg">

        <!-- Top Bar -->
        <div class="flex-between flex-wrap gap-8 mb-40">
            <span class="text-neutral-600 fw-medium px-40 py-12 rounded-pill border border-neutral-100" id="result-stats">
                Showing {{ $vendors->firstItem() ?? 0 }}-{{ $vendors->lastItem() ?? 0 }} of {{ $vendors->total() }} results
            </span>

            <div class="flex-align gap-16">
                <!-- Search Form -->
                <form action="javascript:void(0)" method="GET" id="search-form" class="search-form__wrapper position-relative d-block">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           class="search-form__input common-input py-13 ps-16 pe-18 rounded-pill pe-44" 
                           placeholder="Search vendors by name..."
                           id="search-input">
                    <button type="submit" class="w-32 h-32 bg-main-600 rounded-circle flex-center text-xl text-white position-absolute top-50 translate-middle-y inset-inline-end-0 me-8">
                        <i class="ph ph-magnifying-glass"></i>
                    </button>
                </form>
                
                <!-- Sort Dropdown -->
                <div class="flex-align gap-8">
                    <span class="text-gray-900 flex-shrink-0">Sort by:</span>
                    <select class="common-input form-select rounded-pill border border-gray-100 d-inline-block ps-20 pe-36 h-48 py-0 fw-medium" id="sort-select">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Vendors Container -->
        <div id="vendors-container">
            @if($vendors->count() > 0)
                <div class="row gy-4">
                    @include('frontend.component.vendor-card', ['vendors' => $vendors])
                </div>
            @else
                <div class="col-12 text-center py-5">
                    <p>No vendors found</p>
                </div>
            @endif
        </div>

        <!-- Pagination Container - Shop Page Style -->
        <div id="pagination-container" class="mt-48">
            @if($vendors->hasPages())
                @php
                    $current = $vendors->currentPage();
                    $last = $vendors->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp

                <ul class="pagination flex-center flex-wrap gap-16 mt-48">
                    @if($start > 1)
                        <li class="page-item">
                            <a class="page-link h-64 w-64 flex-center text-md rounded-8 fw-medium text-neutral-600 border border-gray-100" 
                               href="{{ $vendors->url(1) }}">01</a>
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
                                   href="{{ $vendors->url($page) }}">
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
                               href="{{ $vendors->url($last) }}">{{ str_pad($last, 2, '0', STR_PAD_LEFT) }}</a>
                        </li>
                    @endif
                </ul>
            @endif
        </div>

    </div>
</section>
<!-- =================================== Vendors List section End ===================================== -->

@endsection