<div>
    <div class="row g-0 registration-container">
        <!-- Left Side - Form -->
        <div class="col-md-12 col-lg-12 bg-white p-5 ">
            <div class="mx-auto" style="max-width: 700px;">
                <!-- Header -->
                <div class="text-center mb-5">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3">
                        <div class="bg-gradient rounded-3 d-inline-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-store text-white" style="font-size: 24px;"></i>
                        </div>
                    </div>
                    <h1 class="fw-bold mb-2" style="color: #1f2937; font-size: 28px;">
                        Vendor Registration
                    </h1>
                    <p class="text-muted" style="font-size: 16px;">
                        Join thousands of vendors selling on Diginotive
                    </p>
                </div>

                <!-- Progress Indicator -->
                <div class="position-relative mb-5" style="padding: 0 40px;">
                    <div class="d-flex justify-content-between align-items-center" style="min-height: 60px;">
                        <!-- Step 1 -->
                        <div class="d-flex flex-column align-items-center" style="z-index: 2; min-width: 80px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 transition-all duration-300 {{ $currentStep >= 1 ? 'bg-success text-white' : ($currentStep == 1 ? 'bg-primary text-white' : 'bg-light text-muted') }}"
                                style="width: 40px; height: 40px; font-size: 14px; font-weight: 600;">
                                {{ $currentStep > 1 ? '<i class="fas fa-check"></i>' : '1' }}
                            </div>
                            <div class="text-center"
                                style="font-size: 12px; font-weight: 600; {{ $currentStep == 1 ? 'color: #0d6efd;' : ($currentStep > 1 ? 'color: #28a745;' : 'color: #6c757d;') }}">
                                Personal Info
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="d-flex flex-column align-items-center" style="z-index: 2; min-width: 80px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 transition-all duration-300 {{ $currentStep >= 2 ? 'bg-success text-white' : ($currentStep == 2 ? 'bg-primary text-white' : 'bg-light text-muted') }}"
                                style="width: 40px; height: 40px; font-size: 14px; font-weight: 600;">
                                {{ $currentStep > 2 ? '<i class="fas fa-check"></i>' : '2' }}
                            </div>
                            <div class="text-center"
                                style="font-size: 12px; font-weight: 600; {{ $currentStep == 2 ? 'color: #0d6efd;' : ($currentStep > 2 ? 'color: #28a745;' : 'color: #6c757d;') }}">
                                Store Info
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="d-flex flex-column align-items-center" style="z-index: 2; min-width: 80px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 transition-all duration-300 {{ $currentStep >= 3 ? 'bg-success text-white' : ($currentStep == 3 ? 'bg-primary text-white' : 'bg-light text-muted') }}"
                                style="width: 40px; height: 40px; font-size: 14px; font-weight: 600;">
                                {{ $currentStep > 3 ? '<i class="fas fa-check"></i>' : '3' }}
                            </div>
                            <div class="text-center"
                                style="font-size: 12px; font-weight: 600; {{ $currentStep == 3 ? 'color: #0d6efd;' : ($currentStep > 3 ? 'color: #28a745;' : 'color: #6c757d;') }}">
                                Documents
                            </div>
                        </div>
                    </div>

                    <!-- Line between step 1 and 2 -->
                    <div class="position-absolute"
                        style="top: 20px; left: 80px; right: 50%; height: 2px; background: {{ $currentStep > 1 ? '#28a745' : '#e9ecef' }}; z-index: 1;">
                    </div>

                    <!-- Line between step 2 and 3 -->
                    <div class="position-absolute"
                        style="top: 20px; left: 50%; right: 80px; height: 2px; background: {{ $currentStep > 2 ? '#28a745' : '#e9ecef' }}; z-index: 1;">
                    </div>
                </div>

                <form wire:submit.prevent="register">
                    <!-- Step 1: Personal Information -->
                    @if ($currentStep == 1)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="section-title mb-4">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    Personal Information
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-user text-muted"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control border-start-0 @error('name') is-invalid @enderror"
                                                wire:model.blur="name" placeholder="Enter your full name">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </span>
                                            <input type="email"
                                                class="form-control border-start-0 @error('email') is-invalid @enderror"
                                                wire:model.blur="email" placeholder="Enter your email">
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                            <input type="password"
                                                class="form-control border-start-0 @error('password') is-invalid @enderror"
                                                wire:model.blur="password" placeholder="Create password">
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm Password *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                            <input type="password"
                                                class="form-control border-start-0 @error('password_confirmation') is-invalid @enderror"
                                                wire:model.blur="password_confirmation" placeholder="Confirm password">
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div></div>
                            <button type="button" class="btn btn-primary btn-lg px-4" wire:click="nextStep">
                                Next Step <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    @endif

                    <!-- Step 2: Store Information -->
                    @if ($currentStep == 2)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="section-title mb-4">
                                    <i class="fas fa-store me-2 text-success"></i>
                                    Store Information
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Store Name *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-store text-muted"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control border-start-0 @error('store_name') is-invalid @enderror"
                                                wire:model.blur="store_name" placeholder="Enter store name">
                                        </div>
                                        @error('store_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Store URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-link text-muted"></i>
                                            </span>
                                                <input type="text" class="form-control border-start-0 @error('store_slug') is-invalid @enderror"
                                                wire:model="store_slug" placeholder="store-url" readonly>
                                        </div>
                                        @error('store_slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Business Type *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-briefcase text-muted"></i>
                                            </span>
                                            <select
                                                class="form-select border-start-0 @error('business_type') is-invalid @enderror"
                                                wire:model.blur="business_type">
                                                <option value="">Select Business Type</option>
                                                <option value="retail">🛍️ Retail</option>
                                                <option value="wholesale">📦 Wholesale</option>
                                                <option value="manufacturer">🏭 Manufacturer</option>
                                                <option value="service">💼 Service Provider</option>
                                                <option value="other">📌 Other</option>
                                            </select>
                                        </div>
                                        @error('business_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-phone text-muted"></i>
                                            </span>
                                            <input type="tel"
                                                class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                                wire:model.blur="phone" placeholder="+92 XXX XXX XX">
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Address *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"
                                                style="align-items: flex-start; padding-top: 12px;">
                                                <i class="fas fa-map-marker-alt text-muted"></i>
                                            </span>
                                            <textarea class="form-control border-start-0 @error('address') is-invalid @enderror" wire:model.blur="address"
                                                rows="2" placeholder="Enter complete address"></textarea>
                                        </div>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">City *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-city text-muted"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control border-start-0 @error('city') is-invalid @enderror"
                                                wire:model.blur="city" placeholder="Enter city">
                                        </div>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Country *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-globe text-muted"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control border-start-0 @error('country') is-invalid @enderror"
                                                wire:model.blur="country" placeholder="Enter country">
                                        </div>
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary px-4" wire:click="previousStep">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="nextStep">
                                Next Step <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    @endif

                    <!-- Step 3: Documents -->
                    @if ($currentStep == 3)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h5 class="section-title mb-4">
                                    <i class="fas fa-file-alt me-2 text-info"></i>
                                    Document Information
                                </h5>
                                <div class="row g-3">
                                    <!-- CNIC Front -->
                                    <div class="col-md-6">
                                        <div class="document-card card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                                                        <i class="fas fa-id-card text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="card-title mb-1">CNIC Front</h6>
                                                        <small class="text-muted">Upload front side</small>
                                                    </div>
                                                </div>
                                                <label class="form-label small">Upload CNIC Front *</label>
                                                <input type="file"
                                                    class="form-control form-control-sm @error('cnic_front') is-invalid @enderror"
                                                    wire:model="cnic_front" accept=".pdf,.jpg,.jpeg,.png">
                                                @error('cnic_front')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">PDF, JPG, PNG (Max: 2MB)</small>
                                                <div wire:loading wire:target="cnic_front" class="mt-2">
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-spinner fa-spin me-1"></i> Uploading...
                                                    </span>
                                                </div>
                                                @if ($cnic_front)
                                                    <div class="mt-2">
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>
                                                            {{ $cnic_front->getClientOriginalName() }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CNIC Back -->
                                    <div class="col-md-6">
                                        <div class="document-card card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                                                        <i class="fas fa-id-card text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="card-title mb-1">CNIC Back</h6>
                                                        <small class="text-muted">Upload back side</small>
                                                    </div>
                                                </div>
                                                <label class="form-label small">Upload CNIC Back *</label>
                                                <input type="file"
                                                    class="form-control form-control-sm @error('cnic_back') is-invalid @enderror"
                                                    wire:model="cnic_back" accept=".pdf,.jpg,.jpeg,.png">
                                                @error('cnic_back')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">PDF, JPG, PNG (Max: 2MB)</small>
                                                <div wire:loading wire:target="cnic_back" class="mt-2">
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-spinner fa-spin me-1"></i> Uploading...
                                                    </span>
                                                </div>
                                                @if ($cnic_back)
                                                    <div class="mt-2">
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>
                                                            {{ $cnic_back->getClientOriginalName() }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- NTN Number -->
                                    <div class="col-md-6">
                                        <div class="document-card card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-success bg-opacity-10 rounded-2 p-2 me-3">
                                                        <i class="fas fa-hashtag text-success"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="card-title mb-1">NTN Number</h6>
                                                        <small class="text-muted">Tax registration</small>
                                                    </div>
                                                </div>
                                                <label class="form-label small">Enter NTN Number *</label>
                                                <input type="text"
                                                    class="form-control @error('ntn_number') is-invalid @enderror"
                                                    wire:model.blur="ntn_number" placeholder="Enter your NTN number">
                                                @error('ntn_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">National Tax Number</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- NTN Certificate -->
                                    <div class="col-md-6">
                                        <div class="document-card card h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-success bg-opacity-10 rounded-2 p-2 me-3">
                                                        <i class="fas fa-certificate text-success"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="card-title mb-1">NTN Certificate</h6>
                                                        <small class="text-muted">Upload certificate</small>
                                                    </div>
                                                </div>
                                                <label class="form-label small">Upload NTN Certificate *</label>
                                                <input type="file"
                                                    class="form-control form-control-sm @error('ntn_certificate') is-invalid @enderror"
                                                    wire:model="ntn_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                                @error('ntn_certificate')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">PDF, JPG, PNG (Max: 2MB)</small>
                                                <div wire:loading wire:target="ntn_certificate" class="mt-2">
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-spinner fa-spin me-1"></i> Uploading...
                                                    </span>
                                                </div>
                                                @if ($ntn_certificate)
                                                    <div class="mt-2">
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>
                                                            {{ $ntn_certificate->getClientOriginalName() }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-lg px-4" wire:click="previousStep">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button type="submit" class="btn btn-success btn-lg px-4" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="register">
                                    <i class="fas fa-rocket me-2"></i>
                                    Launch Your Vendor Account
                                </span>
                                <span wire:loading wire:target="register">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    @endif
                </form>

                <!-- Footer -->
                <div class="text-center mt-5">
                    <p class="text-muted small mb-2">
                        By registering, you agree to our
                        <a href="#" class="text-decoration-none">Terms of Service</a> and
                        <a href="#" class="text-decoration-none">Privacy Policy</a>
                    </p>
                    <p class="text-muted small">
                        2025 Diginotive. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
