<div class="dashboard-page-content">
    <div class="row mb-9 align-items-center">
        <div class="col-sm-6">
            <h2 class="fs-4 mb-0">Edit Social Link</h2>
            <p class="text-muted mb-0">Update the details for {{ $platform }}</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('admin.social-links.index') }}" class="btn btn-light">Back to List</a>
        </div>
    </div>

    <div class="card rounded-4 border-0">
        <div class="card-body p-7">
            <form wire:submit.prevent="update">
                <div class="row g-6">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Platform Name</label>
                        <input type="text" wire:model="platform" class="form-control" placeholder="e.g. Instagram">
                        @error('platform') <span class="text-danger fs-13px">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Icon Class</label>
                        <input type="text" wire:model="icon_class" class="form-control" placeholder="Instagram">
                        @error('icon_class') <span class="text-danger fs-13px">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">URL</label>
                        <input type="text" wire:model="url" class="form-control" placeholder="https://instagram.com/yourprofile">
                        @error('url') <span class="text-danger fs-13px">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="is_active" id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Active Status</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn dashboard-theme-primary-button text-white">
                            Update Social Link
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>