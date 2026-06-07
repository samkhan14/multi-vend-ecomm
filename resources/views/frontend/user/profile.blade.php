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
                    <span class="text-main-600">Profile</span>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- ================================ Profile Section Start ================================ -->
<section class="profile py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <!-- Sidebar -->
            <div class="col-xl-3 col-lg-4">
                <div class="card border border-gray-100 rounded-8 px-24 py-32 shadow-sm">
                    <div class="text-center mb-24">
                        <div class="position-relative d-inline-block mb-16">
                            @if(auth()->user()->image)
                                <img id="sidebarProfileImage" src="{{ asset('storage/' . auth()->user()->image) }}" 
                                     class="rounded-circle border border-gray-200" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div id="sidebarProfileIcon" class="bg-main-100 rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 100px; height: 100px;">
                                    <i class="ph-bold ph-user text-main-600 fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <h5 id="sidebarProfileName" class="mb-4">{{ auth()->user()->name }}</h5>
                        <p id="sidebarProfileEmail" class="text-gray-600 small">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <div class="nav flex-column nav-pills">
                        <a class="nav-link d-flex align-items-center gap-10 py-12 px-16 rounded-8 mb-8" 
                           href="{{ route('user.dashboard') }}">
                            <i class="ph-bold ph-gauge"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link d-flex align-items-center gap-10 py-12 px-16 rounded-8 mb-8" 
                           href="{{ route('user.orders') }}">
                            <i class="ph-bold ph-shopping-bag"></i>
                            <span>My Orders</span>
                        </a>
                        <a class="nav-link active d-flex align-items-center gap-10 py-12 px-16 rounded-8 mb-8" 
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
                <!-- Profile Update Form -->
                <div class="card border border-gray-100 rounded-8 px-40 py-48 shadow-sm mb-32">
                    <div class="mb-32">
                        <h4 class="mb-8">Update Profile</h4>
                        <p class="text-gray-600">Manage your personal information</p>
                    </div>
                    
                    <form id="profileUpdateForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Profile Image Upload -->
                            <div class="col-md-4 text-center mb-4">
                                <div class="mb-3">
                                    <div class="position-relative d-inline-block">
                                        @if(auth()->user()->image)
                                            <img id="profileImagePreview" src="{{ asset('storage/' . auth()->user()->image) }}" 
                                                 class="rounded-circle border border-gray-200" 
                                                 style="width: 150px; height: 150px; object-fit: cover;">
                                        @else
                                            <div id="defaultProfileIcon" class="bg-main-50 rounded-circle border border-gray-200 d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 150px; height: 150px;">
                                                <i class="ph-bold ph-user text-main-600 fs-1"></i>
                                            </div>
                                        @endif
                                        <button type="button" 
                                                class="position-absolute bottom-0 end-0 bg-main-600 text-white rounded-circle p-2 border-0 shadow-sm"
                                                style="width: 40px; height: 40px; cursor: pointer;" 
                                                onclick="document.getElementById('imageUpload').click()">
                                            <i class="ph-bold ph-camera"></i>
                                        </button>
                                    </div>
                                    <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*">
                                    <small class="text-gray-500 d-block mt-2">Click camera icon to change photo</small>
                                    <div id="imageError" class="text-danger small mt-2 d-none"></div>
                                </div>
                            </div>
                            
                            <!-- Form Fields -->
                            <div class="col-md-8">
                                <div class="row g-16">
                                    <div class="col-12 mb-20">
                                        <label for="name" class="form-label fw-semibold mb-8">Full Name *</label>
                                        <input type="text" class="form-control common-input" id="name" name="name" 
                                               value="{{ auth()->user()->name }}" required>
                                        <div class="invalid-feedback" id="name-error"></div>
                                    </div>
                                    
                                    <div class="col-12 mb-20">
                                        <label for="email" class="form-label fw-semibold mb-8">Email Address *</label>
                                        <input type="email" class="form-control common-input" id="email" name="email" 
                                               value="{{ auth()->user()->email }}" required>
                                        <div class="invalid-feedback" id="email-error"></div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-20">
                                        <label for="dob" class="form-label fw-semibold mb-8">Date of Birth</label>
                                        <input type="date" class="form-control common-input" id="dob" name="dob" 
                                               @if(auth()->user()->dob) 
                                                   value="{{ \Carbon\Carbon::parse(auth()->user()->dob)->format('Y-m-d') }}"
                                               @endif>
                                    </div>
                                    
                                    <div class="col-md-6 mb-20">
                                        <label for="address" class="form-label fw-semibold mb-8">Address</label>
                                        <input type="text" class="form-control common-input" id="address" name="address" 
                                               value="{{ auth()->user()->address ?? '' }}">
                                    </div>
                                    
                                    <div class="col-12 mt-16">
                                        <button type="submit" class="btn btn-main py-18 px-40 rounded-8" id="profileSubmitBtn">
                                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                            <span class="btn-text">Update Profile</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Password Change Form -->
                <div class="card border border-gray-100 rounded-8 px-40 py-48 shadow-sm">
                    <div class="mb-32">
                        <h4 class="mb-8">Change Password</h4>
                        <p class="text-gray-600">Update your password to keep your account secure</p>
                    </div>
                    
                    <form id="passwordChangeForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <div class="mb-20">
                                    <label for="current_password" class="form-label fw-semibold mb-8">Current Password *</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control common-input" id="current_password" name="current_password" required>
                                        <button type="button" class="position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-16 toggle-password">
                                            <i class="ph ph-eye-slash"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="current_password-error"></div>
                                </div>
                                
                                <div class="mb-20">
                                    <label for="new_password" class="form-label fw-semibold mb-8">New Password *</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control common-input" id="new_password" name="new_password" required>
                                        <button type="button" class="position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-16 toggle-password">
                                            <i class="ph ph-eye-slash"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="new_password-error"></div>
                                    <small class="text-gray-500">Minimum 6 characters</small>
                                </div>
                                
                                <div class="mb-20">
                                    <label for="new_password_confirmation" class="form-label fw-semibold mb-8">Confirm New Password *</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control common-input" id="new_password_confirmation" name="new_password_confirmation" required>
                                        <button type="button" class="position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-16 toggle-password">
                                            <i class="ph ph-eye-slash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mt-24">
                                    <button type="submit" class="btn btn-main py-18 px-40 rounded-8" id="passwordChangeBtn">
                                        <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                        <span class="btn-text">Change Password</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Alert Container -->
                <div id="alertContainer" class="mt-24"></div>
            </div>
        </div>
    </div>
</section>
<!-- ================================ Profile Section End ================================ -->
@endsection