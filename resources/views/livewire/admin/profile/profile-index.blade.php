<div>
    <div class="dashboard-page-content">

        <!-- FORM START -->
        <form wire:submit.prevent="saveProfile">

            <div class="row mb-9 align-items-center">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-sm-6 mb-8 mb-sm-0">
                            <h2 class="fs-4 mb-0">Profile settings</h2>
                        </div>

                        <div class="col-sm-6 d-flex flex-wrap justify-content-sm-end">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveProfile">
                                    <i class="fas fa-save"></i> Save changes
                                </span>
                                <span wire:loading wire:target="saveProfile">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Left Side -->
                        <div class="col-lg-8">
                            <div class="card mb-8 rounded-4">
                                <div class="card-header p-7 bg-transparent">
                                    <h4 class="fs-18 mb-0 font-weight-500">Personal Information</h4>
                                </div>
                                <div class="card-body p-7">
                                    <div class="row">

                                        <!-- Full Name -->
                                        <div class="col-lg-12">
                                            <div class="mb-8">
                                                <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Full
                                                    Name</label>
                                                <input type="text" wire:model="name" class="form-control"
                                                    placeholder="Type here">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-lg-6">
                                            <div class="mb-8">
                                                <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Email</label>
                                                <input type="email" wire:model="email" class="form-control"
                                                    placeholder="example@mail.com">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Date of Birth -->
                                        <div class="col-lg-6">
                                            <div class="mb-8">
                                                <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Date of
                                                    Birth</label>
                                                <input type="date" wire:model="dob" class="form-control">
                                                @error('dob')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="col-lg-12">
                                            <div class="mb-8">
                                                <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Address</label>
                                                <textarea wire:model="address" class="form-control" rows="3"
                                                    placeholder="Type here"></textarea>
                                                @error('address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="card mb-8 rounded-4">
                                <div class="card-header p-7 bg-transparent">
                                    <h4 class="fs-18 mb-0 font-weight-500">
                                        <i class="fas fa-university me-2"></i> Bank Account Details
                                    </h4>
                                </div>

                                <div class="card-body p-7">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <label class="mb-2 fs-13px ls-1 fw-bold text-uppercase">
                                                Account Title
                                            </label>
                                            <input type="text" wire:model="account_title"
                                                class="form-control @error('account_title') is-invalid @enderror"
                                                placeholder="Enter account holder name">
                                            @error('account_title')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- resources/views/livewire/admin/profile/profile-index.blade.php --}}

                                        <div class="col-12 mb-4">
                                            <label class="mb-2 fs-13px ls-1 fw-bold text-uppercase">
                                                IBAN Number <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" wire:model.live.debounce.500ms="iban_number"
                                                class="form-control @error('iban_number') is-invalid @enderror"
                                                placeholder="e.g., GB29NWBK60161331926819 or PK77ABPA0010129017170014">

                                            {{-- Real-time validation status --}}
                                            @if($isValidatingIBAN)
                                                <div class="mt-2">
                                                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                                    <span class="text-muted">Verifying IBAN worldwide...</span>
                                                </div>
                                            @elseif($ibanValid === true)
                                                <div class="mt-2">
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle"></i> ✓ Valid IBAN
                                                    </span>
                                                    @if($ibanCountry)
                                                        <span class="text-muted ms-2">
                                                            @php
                                                                $countries = [
                                                                    'PK' => 'Pakistan',
                                                                    'AE' => 'United Arab Emirates',
                                                                    'GB' => 'United Kingdom',
                                                                    'DE' => 'Germany',
                                                                    'FR' => 'France',
                                                                    'ES' => 'Spain',
                                                                    'IT' => 'Italy',
                                                                    'SA' => 'Saudi Arabia',
                                                                    'KW' => 'Kuwait',
                                                                    'QA' => 'Qatar',
                                                                    'US' => 'United States',
                                                                    'CA' => 'Canada',
                                                                    'AU' => 'Australia',
                                                                    'IN' => 'India',
                                                                    'BD' => 'Bangladesh',
                                                                    'LK' => 'Sri Lanka',
                                                                    'NG' => 'Nigeria',
                                                                    'ZA' => 'South Africa',
                                                                    'TR' => 'Turkey',
                                                                    'EG' => 'Egypt',
                                                                    'MA' => 'Morocco',
                                                                    'JO' => 'Jordan',
                                                                    'LB' => 'Lebanon',
                                                                    'OM' => 'Oman',
                                                                    'BH' => 'Bahrain',
                                                                    'CH' => 'Switzerland',
                                                                    'NL' => 'Netherlands',
                                                                    'BE' => 'Belgium',
                                                                    'AT' => 'Austria',
                                                                    'SE' => 'Sweden',
                                                                    'NO' => 'Norway',
                                                                    'DK' => 'Denmark',
                                                                    'PL' => 'Poland',
                                                                    'CZ' => 'Czech Republic',
                                                                    'HU' => 'Hungary',
                                                                    'RO' => 'Romania',
                                                                    'BG' => 'Bulgaria',
                                                                    'GR' => 'Greece',
                                                                    'PT' => 'Portugal',
                                                                    'IE' => 'Ireland',
                                                                    'IL' => 'Israel',
                                                                ];
                                                            @endphp
                                                            ({{ $countries[$ibanCountry] ?? $ibanCountry }})
                                                        </span>
                                                    @endif

                                                </div>
                                            @elseif($ibanValid === false)
                                                <div class="mt-2">
                                                    <span class="text-danger">
                                                        <i class="fas fa-times-circle"></i> ✗ Invalid IBAN
                                                    </span>
                                                </div>
                                            @endif

                                            @error('iban_number')
                                                <span class="text-danger small d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-12 mb-4">
                                            <label class="mb-2 fs-13px ls-1 fw-bold text-uppercase">
                                                Bank Name
                                            </label>
                                            <input type="text" wire:model="bank_name"
                                                class="form-control @error('bank_name') is-invalid @enderror"
                                                placeholder="Enter bank name">
                                            @error('bank_name')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="button" wire:click="saveBankDetails" class="btn btn-primary"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="saveBankDetails">
                                                <i class="fas fa-save"></i> Save Bank Details
                                            </span>
                                            <span wire:loading wire:target="saveBankDetails">
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                                Saving...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>




                            <!-- Password & Account Actions -->
                            <div class="row">
                                <div class="col-md-6">
                                    <article class="d-flex p-6 mb-6 bg-body-tertiary border rounded align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="fs-14px mb-2 font-weight-500">Password</h6>
                                            <small class="text-muted">You can reset or change your password by clicking
                                                here</small>
                                        </div>
                                        <div class="ms-3">
                                            <button type="button"
                                                class="btn border btn-hover-text-light btn-hover-bg-primary btn-hover-border-primary btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#passwordModal">
                                                <i class="fas fa-key"></i> Change
                                            </button>
                                        </div>
                                    </article>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="col-lg-4">
                            <!-- Profile Image -->
                            <div class="card mb-8 rounded-4">
                                <div class="card-header p-7 bg-transparent">
                                    <h4 class="fs-18px mb-0 font-weight-500">Profile Image</h4>
                                </div>
                                <div class="card-body p-7">
                                    <div class="input-upload text-center position-relative">

                                        @if ($image)
                                            <!-- New Image Preview (temporary upload) -->
                                            <div class="position-relative d-inline-block mb-4 mx-auto"
                                                style="width: 196px; height: 196px; border-radius: 50%; overflow: hidden;">
                                                <img src="{{ $image->temporaryUrl() }}"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                <button type="button" wire:click="removeImage"
                                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">&times;</button>
                                            </div>
                                        @elseif ($current_image)
                                            <!-- Existing Saved Profile Image -->
                                            <div class="d-inline-block mb-4 mx-auto"
                                                style="width: 196px; height: 196px; border-radius: 50%; overflow: hidden;">
                                                <img src="{{ asset('storage/' . $current_image) }}"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-4"
                                                style="width: 196px; height: 196px;">
                                                <i class="fas fa-user fa-5x text-white"></i>
                                            </div>
                                        @endif

                                        <input type="file" wire:model="image"
                                            class="form-control @error('image') is-invalid @enderror"
                                            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                        <small class="text-muted d-block mt-2">Max size: 2MB</small>
                                        @error('image')
                                            <span class="text-danger d-block mt-2">{{ $message }}</span>
                                        @enderror

                                        <div wire:loading wire:target="image" class="mt-3">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Processing...</span>
                                            </div>
                                            <span class="ms-2 text-muted">Processing...</span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </form>
        <!-- FORM END -->

    </div>

    <!-- Password Change Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-lock"></i> Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="changePassword">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" wire:model="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                placeholder="Enter current password">
                            @error('current_password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" wire:model="new_password"
                                class="form-control @error('new_password') is-invalid @enderror"
                                placeholder="Enter new password">
                            @error('new_password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" wire:model="new_password_confirmation" class="form-control"
                                placeholder="Confirm new password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                            wire:target="changePassword">
                            <span wire:loading.remove wire:target="changePassword">
                                <i class="fas fa-save"></i> Update Password
                            </span>
                            <span wire:loading wire:target="changePassword">
                                <span class="spinner-border spinner-border-sm"></span> Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Deactivate Account Modal -->
    <div class="modal fade" id="deactivateModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Deactivate Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning!</strong> This action cannot be undone.
                    </div>
                    <p class="mb-3">Are you sure you want to deactivate your account?</p>
                    <ul class="text-muted mb-0">
                        <li>You will be logged out immediately</li>
                        <li>Your account will be marked as inactive</li>
                        <li>Contact support to reactivate your account</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" wire:click="deactivateAccount" class="btn btn-danger"
                        wire:loading.attr="disabled" wire:target="deactivateAccount">
                        <span wire:loading.remove wire:target="deactivateAccount">
                            <i class="fas fa-user-times"></i> Deactivate Account
                        </span>
                        <span wire:loading wire:target="deactivateAccount">
                            <span class="spinner-border spinner-border-sm"></span> Deactivating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('close-password-modal', () => {
                    var modalEl = document.getElementById('passwordModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                });
            });
        </script>
    @endpush
</div>