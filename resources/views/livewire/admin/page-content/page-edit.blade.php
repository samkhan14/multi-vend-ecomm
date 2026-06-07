<div x-data="{
    policy_name: @entangle('policy_name'),
    slug: @entangle('slug'),
    generateSlug() {
        if (!this.policy_name) return;
        this.slug = this.policy_name.toLowerCase()
            .replace(/ /g, '-')
            .replace(/[^\w-]+/g, '')
            .replace(/-+/g, '-');
    }
}" x-init="$watch('policy_name', value => value && generateSlug())">
    <div class="dashboard-page-content">

        <form wire:submit.prevent="update">
            <div class="row mb-9 align-items-center">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-sm-6 mb-8 mb-sm-0">
                            <h2 class="fs-4 mb-0">Edit Policy Page</h2>
                        </div>
                        <div class="col-sm-6 d-flex flex-wrap justify-content-sm-end">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="update">Update</span>
                                <span wire:loading wire:target="update">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Updating...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-8 rounded-4">
                        <div class="card-header p-7 bg-transparent">
                            <h4 class="fs-18 mb-0 font-weight-500">Policy Page Information</h4>
                        </div>
                        <div class="card-body p-7">
                            <div class="row">

                                <div class="col-lg-6">
                                    <div class="mb-8">
                                        <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Policy Name</label>
                                        <input type="text" wire:model="policy_name" class="form-control" placeholder="e.g. Privacy Policy, Shipping Info">
                                        @error('policy_name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Slug -->
                                <div class="col-lg-6">
                                    <div class="mb-8">
                                        <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Slug</label>
                                        <input type="text" x-model="slug" wire:model="slug" class="form-control"
                                            placeholder="Auto-generated">
                                        @error('slug')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="text-muted">Auto-generated from title</small>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-lg-6">
                                    <div class="mb-8">
                                        <label class="form-check mb-5">
        <input class="form-check-input" type="checkbox" wire:model.live="status">
        <span class="form-check-label">Active</span>
    </label>
    @error('status')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
                                </div>

                                <!-- Content with Quill -->
                                <div class="col-lg-12">
                                    <div class="mb-8">
                                        <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Content *</label>

                                        <div x-data="{
                                            quill: null,
                                            init() {
                                                this.quill = new Quill('#quill-page-content-editor', {
                                                    theme: 'snow',
                                                    placeholder: 'Write your policy content...',
                                                });

                                                // Set initial content
                                                this.quill.root.innerHTML = {{ Js::from($content) }};

                                                this.quill.on('text-change', () => {
                                                    let html = this.quill.root.innerHTML;
                                                    $refs.content.value = html;
                                                    Livewire.dispatch('update-quill-content', {
                                                        model: 'content',
                                                        content: html
                                                    });
                                                });

                                                Livewire.on('reset-quill', () => {
                                                    this.quill.setContents([]);
                                                });
                                            }
                                        }" wire:ignore>
                                            <div id="quill-page-content-editor" style="min-height:250px; background:white;"></div>
                                            <textarea wire:model="content" x-ref="content" style="display:none;"></textarea>
                                            @error('content')
                                                <span class="text-danger d-block mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>