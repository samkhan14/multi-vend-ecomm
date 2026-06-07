<div>
    <div class="row g-0 registration-container">
        <!-- Left Side - Form -->
        <div class="col-md-12 col-lg-12 bg-white p-5 ">
            <div class="mx-auto" style="max-width: 500px;">
                <!-- Header -->
                <div class="text-center mb-5">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3">
                        <div class="bg-gradient rounded-3 d-inline-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-sign-in-alt text-white" style="font-size: 24px;"></i>
                        </div>
                    </div>
                    <h1 class="fw-bold mb-2" style="color: #1f2937; font-size: 28px;">
                        Vendor Login
                    </h1>
                    <p class="text-muted" style="font-size: 16px;">
                        Welcome back to your vendor dashboard
                    </p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Login Form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form wire:submit.prevent="login">
                            <div class="mb-4">
                                <label class="form-label">Email Address</label>
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

                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                        class="form-control border-start-0 @error('password') is-invalid @enderror"
                                        wire:model.blur="password" placeholder="Enter password">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot password?</a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="login">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Login as Vendor
                                </span>
                                <span wire:loading wire:target="login">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Logging in...
                                </span>
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted">
                                Don't have an account? 
                                <a href="{{ route('vendor.register') }}" class="text-decoration-none fw-semibold">
                                    Register here
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-5">
                    <p class="text-muted small mb-2">
                        By logging in, you agree to our
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
