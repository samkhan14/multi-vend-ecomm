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
                    <span class="text-main-600">Register</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- ================================ Register Section Start ================================ -->
<section class="register py-80">
    <div class="container container-lg">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card border border-gray-100 rounded-8 px-40 py-48 shadow-sm">
                    <div class="text-center mb-32">
                        <h4 class="mb-8">Create an Account</h4>
                        <p class="text-gray-600">Join us today!</p>
                    </div>

                    <!-- Register Form -->
                    <form id="registerForm" method="POST">
                        @csrf
                        
                        <!-- First Name & Last Name Row -->
                        <div class="row g-16 mb-24">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold mb-8">First Name</label>
                                <input type="text" 
                                       class="form-control common-input" 
                                       id="first_name" 
                                       name="first_name" 
                                       placeholder="First name"
                                       required>
                                <div class="invalid-feedback" id="first_name-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-semibold mb-8">Last Name</label>
                                <input type="text" 
                                       class="form-control common-input" 
                                       id="last_name" 
                                       name="last_name" 
                                       placeholder="Last name"
                                       required>
                                <div class="invalid-feedback" id="last_name-error"></div>
                            </div>
                        </div>

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
                                       placeholder="Create a password"
                                       required>
                                <button type="button" 
                                        class="position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-16 toggle-password">
                                    <i class="ph ph-eye-slash"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="password-error"></div>
                            <small class="text-gray-500">Minimum 6 characters</small>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="mb-32">
                            <div class="flex-align gap-8">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="agree_terms" 
                                       name="agree_policy_terms"
                                       required>
                                <label for="agree_terms" class="form-check-label text-gray-600">
                                    I agree to the <a href="#" class="text-main-600">Terms & Conditions</a>
                                </label>
                            </div>
                            <div class="invalid-feedback" id="agree_policy_terms-error"></div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-main py-18 w-100 rounded-8" id="registerSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            <span class="btn-text">Create Account</span>
                        </button>

                        <!-- Login Link -->
                        <p class="text-center mt-24 mb-0 text-gray-600">
                            Already have an account? 
                            <a href="{{ route('user.login') }}" class="text-main-600 fw-semibold">Login</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================================ Register Section End ================================ -->
@endsection