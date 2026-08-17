<script setup>
/**
 * Reusable image-upload-with-crop field.
 *
 * Emits the cropped image as a real `File` (via canvas.toBlob), never a
 * base64 string, so callers can drop it straight into an Inertia
 * useForm() field and post it as multipart/form-data.
 */
import { ref, watch, onBeforeUnmount } from 'vue';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const props = defineProps({
    modelValue: { type: File, default: null },
    previewUrl: { type: String, default: null },
    aspectRatio: { type: Number, default: 1 },
    outputWidth: { type: Number, default: 500 },
    outputHeight: { type: Number, default: 500 },
    label: { type: String, default: 'Photo' },
    error: { type: String, default: '' },
    round: { type: Boolean, default: true },
    hint: { type: String, default: 'JPG, PNG or WEBP. Max 2MB.' },
});

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const modalRoot = ref(null);
const cropperImg = ref(null);
const preview = ref(props.previewUrl || null);

let cropperInstance = null;
let modalInstance = null;
let currentObjectUrl = null;

watch(
    () => props.previewUrl,
    (value) => {
        if (!props.modelValue) preview.value = value;
    },
);

function triggerSelect() {
    fileInput.value?.click();
}

function onFileChange(event) {
    const file = event.target.files?.[0];
    event.target.value = '';
    if (!file || !file.type.startsWith('image/')) return;

    const reader = new FileReader();
    reader.onload = (loaded) => {
        if (cropperImg.value) cropperImg.value.src = loaded.target.result;
        showModal();
    };
    reader.readAsDataURL(file);
}

function showModal() {
    if (!modalInstance) {
        modalInstance = new window.bootstrap.Modal(modalRoot.value);
        modalRoot.value.addEventListener('shown.bs.modal', initCropper);
        modalRoot.value.addEventListener('hidden.bs.modal', destroyCropper);
    }
    modalInstance.show();
}

function initCropper() {
    destroyCropper();
    cropperInstance = new Cropper(cropperImg.value, {
        aspectRatio: props.aspectRatio,
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 1,
        background: false,
    });
}

function destroyCropper() {
    cropperInstance?.destroy();
    cropperInstance = null;
}

function confirmCrop() {
    if (!cropperInstance) return;

    const canvas = cropperInstance.getCroppedCanvas({
        width: props.outputWidth,
        height: props.outputHeight,
        imageSmoothingQuality: 'high',
    });

    canvas.toBlob(
        (blob) => {
            if (!blob) return;

            const file = new File([blob], `photo-${Date.now()}.jpg`, { type: 'image/jpeg' });

            if (currentObjectUrl) URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = URL.createObjectURL(blob);
            preview.value = currentObjectUrl;

            emit('update:modelValue', file);
            modalInstance.hide();
        },
        'image/jpeg',
        0.9,
    );
}

function removePhoto() {
    if (currentObjectUrl) {
        URL.revokeObjectURL(currentObjectUrl);
        currentObjectUrl = null;
    }
    preview.value = null;
    emit('update:modelValue', null);
}

onBeforeUnmount(() => {
    destroyCropper();
    modalInstance?.dispose();
    if (currentObjectUrl) URL.revokeObjectURL(currentObjectUrl);
});
</script>

<template>
    <div class="image-cropper-field">
        <label class="form-label d-block">{{ label }}</label>

        <div class="d-flex align-items-center gap-3">
            <div class="cropper-preview" :class="{ 'rounded-circle': round, rounded: !round }">
                <img
                    v-if="preview"
                    :src="preview"
                    alt=""
                    class="cropper-preview-img"
                    :class="{ 'rounded-circle': round, rounded: !round }"
                />
                <i v-else class="bi bi-person-fill text-secondary fs-3"></i>
            </div>

            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" @click="triggerSelect">
                    <i class="bi bi-upload me-1"></i>{{ preview ? 'Change Photo' : 'Upload Photo' }}
                </button>
                <button v-if="preview" type="button" class="btn btn-outline-danger btn-sm ms-2" @click="removePhoto">
                    <i class="bi bi-trash"></i>
                </button>
                <div class="form-text mb-0">{{ hint }}</div>
            </div>
        </div>

        <input
            ref="fileInput"
            type="file"
            accept="image/png,image/jpeg,image/webp"
            class="d-none"
            @change="onFileChange"
        />

        <div v-if="error" class="invalid-feedback d-block">{{ error }}</div>

        <Teleport to="body">
            <div ref="modalRoot" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Crop Photo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="cropper-container-wrap">
                                <img ref="cropperImg" alt="" style="max-width: 100%; display: block" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" @click="confirmCrop">
                                <i class="bi bi-check-lg me-1"></i>Crop &amp; Use
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.cropper-preview {
    width: 84px;
    height: 84px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--bs-secondary-bg);
    overflow: hidden;
    flex-shrink: 0;
}

.cropper-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cropper-container-wrap {
    max-height: 60vh;
    overflow: hidden;
}
</style>
