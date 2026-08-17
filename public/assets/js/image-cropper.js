/**
 * Reusable image-cropper field. Wires a file input to a Bootstrap modal
 * containing Cropper.js, and replaces the input's file with the cropped
 * result on confirm.
 *
 * Usage:
 *   new ImageCropperField({
 *       input: '#profile_photo',
 *       modal: '#profile_photo_cropper',
 *       preview: '#profile_photo_preview',
 *       aspectRatio: 1,
 *       outputWidth: 300,
 *       outputHeight: 300,
 *   });
 */
class ImageCropperField {
    constructor(options) {
        this.input = document.querySelector(options.input);
        this.modalEl = document.querySelector(options.modal);
        this.preview = options.preview ? document.querySelector(options.preview) : null;
        this.confirmBtn = this.modalEl.querySelector(options.confirmButton || '[data-cropper-confirm]');
        this.cropperImage = this.modalEl.querySelector(options.image || '[data-cropper-image]');

        this.aspectRatio = options.aspectRatio || 1;
        this.viewMode = options.viewMode ?? 1;
        this.outputWidth = options.outputWidth || 300;
        this.outputHeight = options.outputHeight || 300;
        this.mimeType = options.mimeType || 'image/png';

        this.modal = new bootstrap.Modal(this.modalEl);
        this.cropper = null;

        this.input.addEventListener('change', (event) => this.onFileSelected(event));
        this.confirmBtn.addEventListener('click', () => this.crop());
        this.modalEl.addEventListener('shown.bs.modal', () => this.initCropper());
        this.modalEl.addEventListener('hidden.bs.modal', () => this.destroyCropper());
    }

    onFileSelected(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (loaded) => {
            this.cropperImage.src = loaded.target.result;
            this.modal.show();
        };
        reader.readAsDataURL(file);
    }

    initCropper() {
        this.destroyCropper();
        this.cropper = new Cropper(this.cropperImage, {
            aspectRatio: this.aspectRatio,
            viewMode: this.viewMode,
        });
    }

    destroyCropper() {
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
    }

    crop() {
        if (!this.cropper) return;

        const canvas = this.cropper.getCroppedCanvas({
            width: this.outputWidth,
            height: this.outputHeight,
        });

        canvas.toBlob((blob) => {
            const croppedFile = new File(
                [blob],
                this.input.files[0]?.name || 'cropped.png',
                { type: this.mimeType }
            );

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            this.input.files = dataTransfer.files;

            if (this.preview) {
                this.preview.src = canvas.toDataURL(this.mimeType);
                this.preview.classList.remove('d-none');
            }

            this.modal.hide();
        }, this.mimeType);
    }
}

window.ImageCropperField = ImageCropperField;
