<div>
    <div class="dashboard-page-content">
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit.prevent="save">
            <div class="row mb-9 align-items-center">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-sm-6 mb-8 mb-sm-0">
                            <h2 class="fs-4 mb-0">About Page Content</h2>
                        </div>
                        <div class="col-sm-6 d-flex flex-wrap justify-content-sm-end">
                             <button 
                            type="submit" 
                            class="btn btn-lg btn-primary" 
                            {{-- style="height:auto; padding:4px 10px;"  --}}
                            wire:loading.attr="disabled"
                            >
                                 <span wire:loading.remove wire:target="store">Save Settings</span>
                                 <span wire:loading wire:target="store">
                                     <span class="spinner-border spinner-border-sm"></span>
                                     Saving...
                                 </span>
                             </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-8 rounded-4 border-0 shadow-sm">
                        <div class="card-header p-7 bg-transparent border-bottom">
                            <h4 class="fs-18 mb-0 font-weight-500">Page Content</h4>
                        </div>
                        <div class="card-body p-7">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-8">
                                        <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Page Title</label>
                                        <input type="text" wire:model="title" class="form-control" placeholder="e.g. Our Story">
                                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="mb-8">
                                        <label class="mb-4 fs-13px ls-1 fw-bold text-uppercase">Content *</label>

                                        <div x-data="{
                                            quill: null,
                                            init() {
                                                // Initialize Quill
                                                this.quill = new Quill($refs.quillEditor, {
                                                    theme: 'snow',
                                                    placeholder: 'Write your about page content...',
                                                });

                                                // Load existing content from Livewire
                                                this.quill.root.innerHTML = @js($content);

                                                // Update Livewire property on text change
                                                this.quill.on('text-change', () => {
                                                    let html = this.quill.root.innerHTML;
                                                    
                                                    // Handle Quill empty state (it usually leaves a <p><br></p>)
                                                    if (this.quill.getText().trim().length === 0) {
                                                        html = '';
                                                    }

                                                    // Optimized: Direct set without the dispatch/listener overhead
                                                    @this.set('content', html);
                                                });
                                            }
                                        }" wire:ignore>
                                            <div x-ref="quillEditor" style="min-height:350px; background:white;"></div>
                                        </div>

                                        @error('content') <span class="text-danger d-block mt-2 small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-8 rounded-4">
                        <div class="card-header p-7 bg-transparent">
                            <h4 class="fs-18px mb-0 font-weight-500">
                                <i class="far fa-image me-2"></i> About Image
                            </h4>
                        </div>
                        <div class="card-body p-7">
                            <div class="input-upload text-center position-relative">
                                
                                @if ($image)
                                    <div class="position-relative">
                                        <img src="{{ $image->temporaryUrl() }}"
                                            class="w-100 rounded mb-4 shadow-sm"
                                            style="max-height: 250px; object-fit: cover;">
                                        <button type="button" wire:click="$set('image', null)"
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 shadow">
                                                <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @elseif ($current_image)
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $current_image) }}"
                                            class="w-100 rounded mb-4 shadow-sm"
                                            style="max-height: 250px; object-fit: cover;">
                                        <p class="text-muted small bg-light py-1">Saved Image</p>
                                    </div>
                                @else
                                    <div class="py-4">
                                        <img src="{{ asset('assets/images/dashboard/upload.svg') }}"
                                            width="80" class="d-block mx-auto mb-3 opacity-50">
                                        <p class="text-muted small">No image uploaded</p>
                                    </div>
                                @endif

                                <input type="file" wire:model="image" id="upload_{{ $aboutId ?? 'new' }}" 
                                    class="form-control"
                                    accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Max: 2MB (WebP recommended)</small>
                                    
                                    {{-- Loading Indicator placed carefully --}}
                                    <div wire:loading wire:target="image">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    </div>
                                </div>

                                @error('image') 
                                    <span class="text-danger small d-block mt-2 text-start">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>