<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import ProfileNav from '../../../Components/Admin/ProfileNav.vue';
import ImageCropper from '../../../Components/ImageCropper.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const page = usePage();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    photo: null,
    // Honeypot: real admins never see or fill this field. Any value here
    // on submit means a bot filled in every input blindly.
    website: '',
});

const submit = () => {
    form.post('/admin/profile', { preserveScroll: true, forceFormData: true });
};
</script>

<template>
    <AdminLayout>
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Edit Profile</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                  <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!--end::App Content Header-->

        <div class="app-content">
          <div class="container-fluid">
            <ProfileNav />

            <div class="row">
              <div class="col-lg-6">
                <form novalidate @submit.prevent="submit">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div v-if="page.props.flash?.success" class="alert alert-success">{{ page.props.flash.success }}</div>

                            <div class="mb-3">
                                <ImageCropper
                                    v-model="form.photo"
                                    :preview-url="user.photo_url"
                                    :aspect-ratio="1"
                                    :output-width="300"
                                    :output-height="300"
                                    label="Profile Photo"
                                    :error="form.errors.photo"
                                    hint="Square photo recommended. JPG, PNG or WEBP, max 2MB."
                                />
                            </div>

                            <div class="mb-3">
                                <label for="profile-name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input
                                    id="profile-name"
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.name }"
                                    maxlength="255"
                                    required
                                />
                                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="profile-email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input
                                    id="profile-email"
                                    v-model="form.email"
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.email }"
                                    maxlength="255"
                                    required
                                />
                                <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                            </div>

                            <!-- Honeypot: hidden from real users; bots tend to fill every field they find -->
                            <div class="honeypot-field" aria-hidden="true">
                                <label for="profile-website-field">Website</label>
                                <input
                                    v-model="form.website"
                                    type="text"
                                    id="profile-website-field"
                                    tabindex="-1"
                                    autocomplete="off"
                                />
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!--end::App Content-->
    </AdminLayout>
</template>

<style scoped>
.honeypot-field {
    position: absolute;
    left: -9999px;
    width: 1px;
    height: 1px;
    overflow: hidden;
}
</style>
