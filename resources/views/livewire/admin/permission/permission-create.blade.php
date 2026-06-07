<div>
    <div class="dashboard-page-content">

        <div class="row mb-9 align-items-center justify-content-between">
            <div class="col-12 col-md-6 mb-4 mb-md-0">
                <h2 class="fs-4 mb-0">Assign Permissions</h2>
                <p class="mb-0">Select a role and assign permissions</p>
            </div>
            <div class="col-12 col-md-6 d-flex justify-content-md-end">
                <a href="{{ route('admin.permission-index') }}" class="btn btn-outline-secondary">
                    <i class="far fa-arrow-left me-2"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-header p-7 bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <label class="form-label mb-2">Select Role</label>
                                <select class="form-select" wire:model.live="roleId">
                                    <option value="">-- Select Role --</option>
                                    @foreach (($roles ?? []) as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('roleId')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-6 mt-6 mt-lg-0 d-flex justify-content-lg-end align-items-end">
                                <button type="button" class="btn btn-primary" wire:click="save"
                                    wire:loading.attr="disabled" @if (empty($roleId) || ($selectedRole && $selectedRole->name === 'Super Admin')) disabled @endif>
                                    <span wire:loading.remove>
                                        <i class="far fa-save me-2"></i>
                                        Save Assignments
                                    </span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Saving...
                                    </span>
                                </button>
                            </div>
                              @if ($selectedRole && $selectedRole->name === 'Super Admin')
                                    <small class="text-muted ms-3">Super Admin has all permissions by default</small>
                                @endif
                        </div>
                    </div>

                    <div class="card-body p-7">
                        @if (empty($roles ?? null) || ($roles ?? collect())->count() === 0)
                            <div class="text-center py-6">
                                <p class="text-muted mb-0">No roles found. Please create a role first.</p>
                            </div>
                        @else
                            @if (empty($groupedPermissions ?? []))
                                <div class="text-center py-6">
                                    <p class="text-muted mb-0">No permissions found.</p>
                                </div>
                            @else
                                @foreach ($groupedPermissions as $module => $moduleData)
                                    <div class="mb-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <h5 class="mb-0 me-3">{{ $moduleData['name'] }}</h5>
                                            <div class="badge bg-primary rounded-pill">{{ count($moduleData['permissions']) }} permissions</div>
                                        </div>
                                        
                                        <div class="row g-4">
                                            @foreach ($moduleData['permissions'] as $permission)
                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <label class="d-flex align-items-center gap-3 border rounded-3 p-4 w-100 {{ ($selectedRole && $selectedRole->name === 'Super Admin') ? 'bg-light opacity-75' : '' }}">
                                                        <input class="form-check-input m-0" type="checkbox"
                                                            value="{{ $permission->id }}" wire:model="selectedPermissions"
                                                            @if ($selectedRole && $selectedRole->name === 'Super Admin') disabled @endif>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-semibold text-dark">{{ $permission->name }}</div>
                                                            <div class="small text-muted">Guard: {{ $permission->guard_name }}</div>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    @if (!$loop->last)
                                        <hr class="my-6">
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
