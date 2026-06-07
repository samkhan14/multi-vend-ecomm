<div>
    <div class="dashboard-page-content">

        <div class="row mb-9 align-items-center">
            <div class="col-12 col-md-3 mb-4 mb-md-0">
                <h2 class="fs-4 mb-0">Roles & Permissions</h2>
                <p class="mb-0">View roles and their assigned permissions</p>
            </div>
            <div class="col-12 col-md-9 d-flex flex-wrap align-items-end justify-content-md-end gap-2">
                <a href="{{ route('admin.permission.create') }}" class="btn btn-primary d-flex align-items-center mb-2 mb-md-0" role="button">
                    <i class="far fa-plus me-2"></i>
                    <span>Assign Permissions</span>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-header p-7 bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="card-title fs-6 mb-0">All Roles</h5>
                            </div>
                            <div class="col-md-6 mt-4 mt-md-0">
                                <input type="text" wire:model.live="search" class="form-control"
                                    placeholder="Search by role name...">
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-7"> 
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Role Name</th>
                                        <th>Permission Count</th>
                                        <th>Guard</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse (($roles ?? []) as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $role->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $role->permissions->count() }} permissions
                                                </span>
                                            </td>
                                            <td>{{ $role->guard_name ?? '-' }}</td>
                                            <td>
                                                @if ($role->created_at)
                                                    {{ $role->created_at->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td> 
                                                <button class="btn btn-outline-secondary btn-sm" wire:click="showRolePermissions({{ $role->id }})">
                                                    <i class="fas fa-eye me-2"></i>View Permissions
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted mb-0">No roles found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Details Modal -->
        @if ($selectedRole)
            <div class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Permissions for: {{ $selectedRole->name }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeRoleDetails"></button>
                        </div>
                        <div class="modal-body">
                            @if ($selectedRole->permissions->isEmpty())
                                <div class="text-center py-4">
                                    <p class="text-muted mb-0">No permissions assigned to this role.</p>
                                </div>
                            @else
                                <div class="row g-3">
                                    @foreach ($selectedRole->permissions as $permission)
                                        <div class="col-12 col-md-6">
                                            <div class="border rounded p-3">
                                                <div class="fw-semibold">{{ $permission->name }}</div>
                                                <div class="small text-muted">Guard: {{ $permission->guard_name }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
