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
                <li class="flex-align">—</li>
                <li class="text-sm">
                    <span class="text-main-600">Contact Us</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- ============================ Contact Section Start ================================== -->
<section class="contact py-80">
    <div class="container container-lg">
        <div class="row gy-5">
            <div class="col-lg-8">
                <div class="contact-box border border-gray-100 rounded-16 px-24 py-40">
                    <form id="contactForm" method="POST" action="{{ route('contact.submit') }}">
                        @csrf
                        <h6 class="mb-32">Make Custom Request</h6>
                        <div class="row gy-4">
                            <div class="col-sm-6 col-xs-6">
                                <label for="name" class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">
                                    Full Name <span class="text-danger text-xl line-height-1">*</span>
                                </label>
                                <input type="text" class="common-input px-16" id="name" name="name" placeholder="Full name" required>
                                <div class="text-danger mt-1" id="name-error"></div>
                            </div>
                            
                            <div class="col-sm-6 col-xs-6">
                                <label for="email" class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">
                                    Email Address <span class="text-danger text-xl line-height-1">*</span>
                                </label>
                                <input type="email" class="common-input px-16" id="email" name="email" placeholder="Email address" required>
                                <div class="text-danger mt-1" id="email-error"></div>
                            </div>
                            
                            <div class="col-sm-6 col-xs-6">
                                <label for="phone" class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">
                                    Phone Number <span class="text-danger text-xl line-height-1">*</span>
                                </label>
                                <input type="tel" class="common-input px-16" id="phone" name="phone" placeholder="Phone Number" required>
                                <div class="text-danger mt-1" id="phone-error"></div>
                            </div>
                            
                            <div class="col-sm-6 col-xs-6">
                                <label for="subject" class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">
                                    Subject <span class="text-danger text-xl line-height-1">*</span>
                                </label>
                                <input type="text" class="common-input px-16" id="subject" name="subject" placeholder="Subject" required>
                                <div class="text-danger mt-1" id="subject-error"></div>
                            </div>
                            
                            <div class="col-sm-12">
                                <label for="message" class="flex-align gap-4 text-sm font-heading-two text-gray-900 fw-semibold mb-4">
                                    Message <span class="text-danger text-xl line-height-1">*</span>
                                </label>
                                <textarea class="common-input px-16" id="message" name="message" placeholder="Type your message" rows="5" required></textarea>
                                <div class="text-danger mt-1" id="message-error"></div>
                            </div>
                            
                            <div class="col-sm-12 mt-32">
                                <button type="submit" class="btn btn-main py-18 px-32 rounded-8" id="submitBtn">
                                    <span class="btn-text">Send Message</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="contact-box border border-gray-100 rounded-16 px-24 py-40">
                    <h6 class="mb-48">Get In Touch</h6>
                    <div class="flex-align gap-16 mb-16">
                        <span class="w-40 h-40 flex-center rounded-circle border border-gray-100 text-main-two-600 text-2xl flex-shrink-0">
                            <i class="ph-fill ph-phone-call"></i>
                        </span>
                        <a href="tel:{{ $genralsetting->country_code ?? '' }}{{ $genralsetting->phone ?? '' }}" class="text-md text-gray-900 hover-text-main-600">
                            {{ $genralsetting->country_code ?? '' }} {{ $genralsetting->phone ?? '' }}
                        </a>
                    </div>
                    <div class="flex-align gap-16 mb-16">
                        <span class="w-40 h-40 flex-center rounded-circle border border-gray-100 text-main-two-600 text-2xl flex-shrink-0">
                            <i class="ph-fill ph-envelope"></i>
                        </span>
                        <a href="mailto:{{ $genralsetting->email ?? '' }}" class="text-md text-gray-900 hover-text-main-600">
                            {{ $genralsetting->email ?? '' }}
                        </a>
                    </div>
                    <div class="flex-align gap-16 mb-0">
                        <span class="w-40 h-40 flex-center rounded-circle border border-gray-100 text-main-two-600 text-2xl flex-shrink-0">
                            <i class="ph-fill ph-map-pin"></i>
                        </span>
                        <span class="text-md text-gray-900">{{ $genralsetting->address ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ============================ Contact Section End ================================== -->



@endsection

