<div>
    <div class="dashboard-page-content">
        <!-- FORM START -->
        <form wire:submit.prevent="update">
            <div class="row mb-9 align-items-center">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2 class="fs-4 mb-0">Edit User</h2>
                        </div>

                        <div class="col-sm-6 d-flex justify-content-sm-end">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>Update User</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Updating...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="row">
                <!-- LEFT -->
                <div class="col-lg-8">
                    <div class="card rounded-4 mb-8">
                        <div class="card-header p-7 bg-transparent">
                            <h4 class="fs-18 mb-0">User Information</h4>
                        </div>

                        <div class="card-body p-7">
                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-6 mb-6">
                                    <label class="fw-bold mb-2">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                           wire:model.defer="name"
                                           placeholder="Enter full name" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-6">
                                    <label class="fw-bold mb-2">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control"
                                           wire:model.defer="email"
                                           placeholder="Enter email" required>
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <!-- Password -->
                                <div class="col-md-6 mb-6">
                                    <label class="fw-bold mb-2">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                                    <input type="password" class="form-control"
                                           wire:model.defer="password"
                                           placeholder="Enter new password">
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6 mb-6">
                                    <label class="fw-bold mb-2">Confirm Password</label>
                                    <input type="password" class="form-control"
                                           wire:model.defer="password_confirmation"
                                           placeholder="Confirm new password">
                                </div>

                                <!-- Date of Birth -->
                                <div class="col-md-6 mb-6">
                                    <label class="fw-bold mb-2">Date of Birth</label>
                                    <input type="date" class="form-control"
                                           wire:model.defer="dob">
                                    @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <!-- Role -->
                                <div class="col-md-6 mb-6">
                                    <label class="fw-bold mb-2">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model.defer="role_id" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-md-12 mb-6">
                                    <label class="fw-bold mb-2">Address</label>
                                    <textarea class="form-control"
                                              wire:model.defer="address"
                                              rows="3"
                                              placeholder="Enter address"></textarea>
                                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="col-lg-4">
                    <!-- PROFILE IMAGE -->
                    <div class="card rounded-4 mb-8">
                        <div class="card-header p-7 bg-transparent">
                            <h4 class="fs-18 mb-0">Profile Image</h4>
                        </div>
                        <div class="card-body p-7 text-center">
                            <div class="mb-4">
                                @if($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="rounded-circle" width="150" height="150" alt="Profile Preview">
                                @elseif($existing_image)
                                    <img src="{{ asset('storage/' . $existing_image) }}" class="rounded-circle" width="150" height="150" alt="Current Profile">
                                @else
                                    <img src="{{ asset('assets/avatar.png') }}" class="rounded-circle" width="150" height="150" alt="Default Avatar">
                                @endif
                            </div>
                            <div class="d-grid">
                                <label class="btn btn-outline-primary btn-sm mb-2">
                                    <i class="far fa-upload me-2"></i> Change Photo
                                    <input type="file" class="d-none" wire:model="image" accept="image/*">
                                </label>
                                @if($image)
                                    <button type="button" class="btn btn-outline-danger btn-sm" wire:click="$set('image', null)">
                                        <i class="far fa-trash-alt me-2"></i> Remove
                                    </button>
                                @endif
                            </div>
                            @error('image') <span class="text-danger small d-block mt-2">{{ $message }}</span> @enderror
                            <p class="small text-muted mt-2 mb-0">JPG, PNG or GIF (Max 2MB)</p>
                        </div>
                    </div>

                    <!-- SETTINGS -->
                    <div class="card rounded-4 mb-8">
                        <div class="card-header p-7 bg-transparent">
                            <h4 class="fs-18 mb-0">Settings</h4>
                        </div>
                        <div class="card-body p-7">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input"
                                       wire:model="user_status"
                                       value="1">
                                <span class="form-check-label">Active User</span>
                            </label>
                        </div>
                    </div>

                    <!-- SUMMARY -->
                    <div class="card rounded-4">
                        <div class="card-header p-7 bg-transparent">
                            <h4 class="fs-18 mb-0">Summary</h4>
                        </div>
                        <div class="card-body p-7">
                            <div class="d-flex justify-content-between">
                                <span>Status:</span>
                                <span class="badge bg-{{ $user_status ? 'success' : 'secondary' }}">
                                    {{ $user_status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- FORM END -->
    </div>
</div>
