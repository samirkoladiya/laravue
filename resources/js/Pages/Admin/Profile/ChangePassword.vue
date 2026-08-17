<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import ProfileNav from '../../../Components/Admin/ProfileNav.vue';

const page = usePage();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
    // Honeypot: real admins never see or fill this field. Any value here
    // on submit means a bot filled in every input blindly.
    website: '',
});

const submit = () => {
    form.post('/admin/profile/password', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => form.reset('current_password', 'password', 'password_confirmation'),
    });
};
</script>

<template>
    <AdminLayout>
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Change Password</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                  <li class="breadcrumb-item"><Link href="/admin/profile">Profile</Link></li>
                  <li class="breadcrumb-item active" aria-current="page">Change Password</li>
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
                                <label for="current-password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                <input
                                    id="current-password"
                                    v-model="form.current_password"
                                    type="password"
                                    name="current_password"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.current_password }"
                                    autocomplete="current-password"
                                    required
                                />
                                <div v-if="form.errors.current_password" class="invalid-feedback">{{ form.errors.current_password }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="new-password" class="form-label">New Password <span class="text-danger">*</span></label>
                                <input
                                    id="new-password"
                                    v-model="form.password"
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.password }"
                                    autocomplete="new-password"
                                    required
                                />
                                <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="new-password-confirm" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <input
                                    id="new-password-confirm"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    autocomplete="new-password"
                                    required
                                />
                            </div>

                            <!-- Honeypot: hidden from real users; bots tend to fill every field they find -->
                            <div class="honeypot-field" aria-hidden="true">
                                <label for="password-website-field">Website</label>
                                <input
                                    v-model="form.website"
                                    type="text"
                                    id="password-website-field"
                                    tabindex="-1"
                                    autocomplete="off"
                                />
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                                Update Password
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
