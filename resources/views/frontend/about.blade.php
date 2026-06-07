
@extends('frontend.layouts.app')

@section('content')
	<!-- Breadcrumb -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    
                    <a href="{{ route('home') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600"> Home</a>
                </li>
                <li class="flex-align">—</li>
             
                <li class="text-sm">
                    <span class="text-main-600">About</span>
                </li>
            </ul>
        </div>
    </div>
</div>

	<div class="container container-xxl mt-16 mb-24 py-80">
    <div class="about-wrapper overflow-hidden">
        @php
            $about = \App\Models\AboutContent::first();
            $imageUrl = ($about && $about->image && Storage::disk('public')->exists($about->image)) 
                        ? asset('storage/' . $about->image) 
                        : asset('template/assets/images/background/bg-about-02.jpg');
        @endphp

        <div class="about-image-wrap">
            <div class="card border-0 hover-zoom-in">
                <div class="image-box-4">
                    <img class="lazy-image img-fluid rounded-4 shadow-sm" 
                         src="{{ $imageUrl }}" 
                         data-src="{{ $imageUrl }}" 
                         width="960" 
                         height="640" 
                         alt="{{ $about->title ?? 'About Us' }}">
                </div>
            </div>
        </div>

        <h2 class="mb-8" style="font-size: 3rem !important;">
			{{ $about->title ?? 'About Glowing' }}
		</h2>

        <div class="about-description mb-xl-16" style="font-size: 1rem; line-height: 1.5;">
			{!! $about->content ?? 'Default description goes here if database is empty.' !!}
		</div>
    </div>
</div>

<style>
    /* Desktop Wrap Logic */
    @media (min-width: 992px) {
        .about-image-wrap {
            float: right; /* This makes it stay on the right */
            width: 45%;   /* Takes up a bit less than half */
            margin-left: 4rem; /* Gap between text and image */
            margin-bottom: 2rem; /* Space before text flows underneath */
        }
        
        .about-description {
            text-align: justify; /* Looks professional in magazine layouts */
        }
    }

    /* Mobile Logic: Go back to normal stacked view */
    @media (max-width: 991px) {
        .about-image-wrap {
            width: 100%;
            margin-bottom: 2rem;
            float: none;
        }
    }

    /* Clearfix to prevent container collapse */
    .about-wrapper::after {
        content: "";
        clear: both;
        display: table;
    }
</style>
</section>

@endsection