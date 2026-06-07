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
                    <span class="text-main-600">Login</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- ================================ Login Section Start ================================ -->
<section class="login py-80">
    <div class="container container-lg">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card border border-gray-100 rounded-8 px-40 py-48 shadow-sm">
                    <div class="text-center mb-32">
                        <h4 class="mb-8">Login </h4>
                        <p class="text-gray-600">Please login to your account</p>
                    </div>

                    <!-- Login Form -->
                    <form id="loginForm" method="POST">
                        @csrf
                        
                        <!-- Email -->
                        <div class="mb-24">
                            <label for="email" class="form-label fw-semibold mb-8">Email Address</label>
                            <input type="email" 
                                   class="form-control common-input" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter your email"
                                   required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>

                        <!-- Password -->
                        <div class="mb-24">
                            <label for="password" class="form-label fw-semibold mb-8">Password</label>
                            <div class="position-relative">
                                <input type="password" 
                                       class="form-control common-input" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter your password"
                                       required>
                                <button type="button" 
                                        class="position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-16 toggle-password">
                                    <i class="ph ph-eye-slash"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex-between gap-8 mb-32">
                            <div class="flex-align gap-8">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="stay_signed_in" 
                                       name="stay_signed_in">
                                <label for="stay_signed_in" class="form-check-label text-gray-600">Remember me</label>
                            </div>
                            <a href="javascript:void(0)" class="text-main-600 hover-text-decoration-underline forgot-password-link">
                                Forgot Password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-main py-18 w-100 rounded-8" id="loginSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            <span class="btn-text">Login</span>
                        </button>

                        <!-- Register Link -->
                        <p class="text-center mt-24 mb-0 text-gray-600">
                            Don't have an account? 
                            <a href="{{ route('user.register') }}" class="text-main-600 fw-semibold">Register</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================================ Login Section End ================================ -->
@endsection