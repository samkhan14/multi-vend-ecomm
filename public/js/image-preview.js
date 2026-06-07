window.ImagePreviewer = {
    files: {},

    // Initialize Livewire listener for file clearing
    init() {
        if (typeof Livewire !== 'undefined') {
            // Listen for Livewire updates to restore previews
            Livewire.hook('component.updated', (component) => {
                this.restorePreviews();
            });

            Livewire.on('file-cleared', (data) => {
                if (data.inputId) {
                    this.clearPreview(data.inputId);
                }
            });
        }
    },

    restorePreviews() {
        // Restore thumbnail preview if it exists
        const thumbnailInput = document.getElementById('thumbnail-input');
        const editThumbnailInput = document.getElementById('edit-thumbnail-input');

        if (thumbnailInput && thumbnailInput.files && thumbnailInput.files[0]) {
            this.previewSingle('thumbnail-input', 'thumbnail-preview', 'thumbnail-remove');
        }

        if (editThumbnailInput && editThumbnailInput.files && editThumbnailInput.files[0]) {
            this.previewSingle('edit-thumbnail-input', 'edit-thumbnail-preview', 'edit-thumbnail-remove');
        }

        // Restore gallery previews if they exist
        const galleryInput = document.getElementById('gallery-input');
        const editGalleryInput = document.getElementById('edit-gallery-input');

        if (galleryInput && galleryInput.files && galleryInput.files.length > 0) {
            this.previewMultiple('gallery-input', 'gallery-container');
        }

        if (editGalleryInput && editGalleryInput.files && editGalleryInput.files.length > 0) {
            this.previewMultiple('edit-gallery-input', 'edit-gallery-container');
        }

        // Restore variant previews for all variant combinations (create page)
        document.querySelectorAll('[id^="variant-images-"]').forEach(input => {
            const index = input.id.replace('variant-images-', '');
            const container = document.getElementById(`variant-container-${index}`);
            if (input.files && input.files.length > 0) {
                this.previewMultiple(input.id, container.id);
            }
        });

        // Restore variant previews for all variant combinations (edit page)
        document.querySelectorAll('[id^="edit-variant-images-"]').forEach(input => {
            const index = input.id.replace('edit-variant-images-', '');
            const container = document.getElementById(`edit-variant-container-${index}`);
            if (input.files && input.files.length > 0) {
                this.previewMultiple(input.id, container.id);
            }
        });
    },

    clearPreview(inputId) {
        delete this.files[inputId];
    },


    previewSingle(inputId, previewId, removeBtnId, placeholderId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const removeBtn = document.getElementById(removeBtnId);
        const placeholder = document.getElementById(placeholderId);

        if (!input || !input.files.length) return;

        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            removeBtn.classList.remove('d-none');
            placeholder.classList.add('d-none'); // 🔥 hide default
        };
        reader.readAsDataURL(input.files[0]);
    },

    removeSingle(inputId, previewId, removeBtnId, placeholderId, livewireProp) {
        document.getElementById(inputId).value = '';

        const preview = document.getElementById(previewId);
        const removeBtn = document.getElementById(removeBtnId);
        const placeholder = document.getElementById(placeholderId);

        preview.src = '';
        preview.classList.add('d-none');
        removeBtn.classList.add('d-none');
        placeholder.classList.remove('d-none'); // 🔥 show default again

        if (livewireProp && typeof Livewire !== 'undefined') {
            Livewire.dispatch('clearFile', livewireProp);
        }
    }


    previewMultiple(inputId, containerId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);

        this.files[inputId] = Array.from(input.files);
        container.innerHTML = '';

        this.files[inputId].forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                container.innerHTML += `
                    <div class="col-4 position-relative mb-2">
                        <img src="${e.target.result}" class="w-100 rounded"
                             style="height:100px;object-fit:cover">

                        <button type="button"
                            onclick="ImagePreviewer.removeMultiple('${inputId}', '${containerId}', ${index})"
                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                            style="width:28px;height:28px;padding:0;border-radius:6px;">
                            ×
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        });
    },

    removeMultiple(inputId, containerId, index) {
        this.files[inputId].splice(index, 1);

        const dt = new DataTransfer();
        this.files[inputId].forEach(file => dt.items.add(file));
        document.getElementById(inputId).files = dt.files;

        this.previewMultiple(inputId, containerId);
    }
};
