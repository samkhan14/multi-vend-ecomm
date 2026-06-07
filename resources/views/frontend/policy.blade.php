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
                    <span class="text-main-600">{{ $policy->policy_name }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->
<div class="cart py-80">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <h1 class="display-5 fw-bold mb-10 text-body-emphasis">
                {{ $policy->policy_name }}
            </h1>

            <div class="text-gray-700 mb-24">
                {!! $policy->content !!}
            </div>
        </div>
    </div>
</div>
@endsection