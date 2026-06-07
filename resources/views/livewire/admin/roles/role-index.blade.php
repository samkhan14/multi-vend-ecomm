<div>
    <div class="dashboard-page-content">

        <div class="row mb-9 align-items-center">
            <div class="col-12 col-md-3 mb-4 mb-md-0">
                <h2 class="fs-4 mb-0">Roles Management</h2>
                <p class="mb-0">Create and manage roles</p>
            </div>

            <div class="col-12 col-md-9 d-flex flex-wrap align-items-end justify-content-md-end gap-2">
                @can('roles.create')
                <button type="button" class="btn btn-primary d-flex align-items-center mb-2 mb-md-0"
                    wire:click="createRole">
                    <i class="far fa-plus me-2"></i>
                    <span>Add Role</span>
                </button>
                @endcan
            </div>
        </div>

        <!-- Roles Table -->
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
                                    placeholder="Search roles...">
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
                                        <th>Guard</th>
                                        <th>Created At</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles ?? [] as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $role->name }}</strong>
                                            </td>
                                            <td>{{ $role->guard_name }}</td>
                                            <td>{{ $role->created_at->format('d M Y') }}</td>

                                            <td class="text-center">
                                                <div class="d-flex flex-nowrap justify-content-center">
                                                    @can('roles.edit')
                                                    <button type="button" class="btn btn-sm btn-outline-primary {{ $role->name === 'Super Admin' ? 'disabled' : '' }}"
                                                        wire:click="editRole({{ $role->id }})"
                                                        @if ($role->name === 'Super Admin') disabled title="Super Admin cannot be edited" @endif>
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                    @endcan
                                                    @can('roles.delete')
                                                    <button x-data
                                                        @click="
                                                            @if ($role->name !== 'Super Admin')
                                                                $event.preventDefault();
                                                                Swal.fire({
                                                                    title: 'Are you sure?',
                                                                    text: 'You won\'t be able to revert this!',
                                                                    icon: 'warning',
                                                                    showCancelButton: true,
                                                                    confirmButtonColor: '#d33',
                                                                    cancelButtonColor: '#3085d6',
                                                                    confirmButtonText: 'Yes, delete it!',
                                                                    cancelButtonText: 'Cancel'
                                                                }).then((result) => {
                                                                    if (result.isConfirmed) {
                                                                        $wire.deleteRole({{ $role->id }})
                                                                    }
                                                                })
                                                            @endif
                                                        "
                                                        class="btn btn-outline-primary btn-hover-bg-danger btn-hover-border-danger btn-hover-text-light py-4 px-5 fs-13px btn-xs me-4 {{ $role->name === 'Super Admin' ? 'disabled' : '' }}"
                                                        @if ($role->name === 'Super Admin') disabled title="Super Admin cannot be deleted" @endif>
                                                        <i class="far fa-trash me-2"></i> Delete
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                           
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
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

<!-- Add/Edit Role Modal - Pure JS Version -->
<div id="roleModal" class="modal fade" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg p-5">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold" id="roleModalLabel">
                    <i class="far fa-user-shield me-2 text-primary"></i>
                    <span>{{ $roleId ? 'Edit Role' : 'Add New Role' }}</span>
                </h5>
                <button type="button" class="btn-close" onclick="closeRoleModal()" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="saveRole">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fs-16px mb-2">Role Name *</label>
                            <input type="text" wire:model="name" class="form-control bg-white"
                                placeholder="e.g., Administrator, Manager, Editor">
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fs-16px mb-2">Guard *</label>
                            <select wire:model="guard_name" class="form-control bg-white">
                                <option value="web">web</option>
                                <option value="api">api</option>
                            </select>
                            @error('guard_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeRoleModal()">
                        <i class="far fa-times me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="far fa-save me-1"></i>
                            Save Role
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal.show {
        display: block !important;
        background-color: rgba(0,0,0,0.5);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1055;
        width: 100%;
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
    }
    .modal {
        display: none;
    }
</style>

@push('scripts')
<script>
    function openRoleModal() {
        const modal = document.getElementById('roleModal');
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }
    
    function closeRoleModal() {
        const modal = document.getElementById('roleModal');
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        @this.resetForm();
    }
    
    document.addEventListener('livewire:init', () => {
        Livewire.on('openRoleModal', () => openRoleModal());
        Livewire.on('closeRoleModal', () => closeRoleModal());
    });
</script>
@endpush

    </div>



</div>
