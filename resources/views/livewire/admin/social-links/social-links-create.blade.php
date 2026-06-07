<div class="dashboard-page-content">
    <form wire:submit.prevent="save">
        <div class="row mb-9 align-items-center">
            <div class="col-sm-6">
                <h2 class="fs-4 mb-0">Add New Social Link</h2>
            </div>
            <div class="col-sm-6 text-sm-end">
                <button type="submit" class="btn btn-primary px-8">
                    <span wire:loading.remove>Publish</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Saving...
                    </span>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-8 rounded-4 bg-dark-01 border-0">
                    <div class="card-header p-7 bg-transparent border-bottom">
                        <h4 class="fs-18 mb-0">Link Information</h4>
                    </div>
                    <div class="card-body p-7">
                        <div class="mb-6">
                            <label class="form-label">Platform Name</label>
                            <input type="text" wire:model="platform" class="form-control" placeholder="e.g. Instagram">
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Profile URL</label>
                            <input type="url" wire:model="url" class="form-control" placeholder="https://instagram.com/username">
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Icon</label>
                            <input type="text" wire:model="icon_class" class="form-control" placeholder="Instagram">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="is_active" id="status">
                            <label class="form-check-label" for="status">Active Status</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>