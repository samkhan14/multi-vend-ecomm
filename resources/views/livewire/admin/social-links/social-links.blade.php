
<div class="dashboard-page-content">
    <div class="row mb-9 align-items-center">
        <div class="col-sm-6">
            <h2 class="fs-4 mb-0">Social Media Links</h2>
            <p class="text-muted mb-0">Manage icons that appear in your website footer.</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('admin.social-links.create') }}" class="btn btn-primary text-white">
                Create New Link
            </a>
        </div>
    </div>

    <div class="card mb-8 rounded-4 bg-dark-01 border-0">
        <div class="card-body p-7">
            <div class="mb-6 col-md-4">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search by platform name...">
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Platform</th>
                            <th>Icon Preview</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($links as $index => $link)
                        <tr>
                            <td>{{ $links->firstItem() + $index }}</td>
                            <td><strong>{{ $link->platform }}</strong></td>
                            <td><i class="{{ $link->icon_class }} fs-4"></i></td>
                            <td class="text-muted">{{ $link->url }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                        wire:click="toggleStatus({{ $link->id }})" {{ $link->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.social-links.edit', $link->id) }}" class="btn btn-sm btn-light me-2">Edit</a>
                                <button wire:click="delete({{ $link->id }})" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                         @endforeach 
                    </tbody>
                </table>
                {{ $links->links() }} 
            </div>
        </div>
    </div>
</div>