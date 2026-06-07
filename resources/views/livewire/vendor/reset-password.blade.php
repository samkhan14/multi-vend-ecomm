<div>
    <div class="row g-0 registration-container">
        <div class="col-md-12 col-lg-12 bg-white p-5">
            <div class="mx-auto" style="max-width: 500px;">

                <!-- Header -->
                <div class="text-center mb-5">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3">
                        <div class="bg-gradient rounded-3 d-inline-flex align-items-center justify-content-center"
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-key text-white" style="font-size: 24px;"></i>
                        </div>
                    </div>

                    <h1 class="fw-bold mb-2" style="color: #1f2937; font-size: 28px;">
                        Reset Vendor Password
                    </h1>
                    <p class="text-muted" style="font-size: 16px;">
                        Create a new password for your vendor account
                    </p>
                </div>

                <!-- Reset Form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form wire:submit.prevent="resetPassword">

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="form-label">Email Address</label>
                                <input type="email"
                                       class="form-control"
                                       wire:model="email"
                                       readonly>
                            </div>

                            <!-- New Password -->
                            <div class="mb-4">
                                <label class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control border-start-0 @error('password') is-invalid @enderror"
                                           wire:model.defer="password"
                                           placeholder="Enter new password">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control border-start-0"
                                           wire:model.defer="password_confirmation"
                                           placeholder="Confirm password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-sync-alt me-2"></i>
                                Reset Password
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('vendor.login') }}" class="text-decoration-none">
                                Back to login
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-5">
                    <p class="text-muted small">
                        2025 Diginotive. All rights reserved.
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
