<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import LoadingOverlay from '../../Components/LoadingOverlay.vue';
import PageStyle from '../../Components/PageStyle.vue';
import PageScript from '../../Components/PageScript.vue';

const page = usePage();

const form = useForm({
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/admin/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <PageStyle href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <PageStyle href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />
    <PageStyle href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />

    <div class="login-page bg-body-secondary">
        <div class="login-box">
            <div class="login-logo">
                <Link href="/">Lara Vue <b>Admin</b></Link>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body position-relative">
                    <LoadingOverlay v-if="form.processing" />
                    <div v-if="page.props.flash?.error" class="alert alert-danger">{{ page.props.flash.error }}</div>
                    <p class="login-box-msg">Choose a new password for your account</p>
                    <form novalidate @submit.prevent="submit">
                        <div class="input-group mb-3">
                            <input
                                v-model="form.password"
                                type="password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.password }"
                                placeholder="New Password"
                                autocomplete="new-password"
                                required
                                autofocus
                            />
                            <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
                            <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                        </div>
                        <div class="input-group mb-3">
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                class="form-control"
                                placeholder="Confirm New Password"
                                autocomplete="new-password"
                                required
                            />
                            <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                        Reset Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <p class="mt-3 mb-0">
                        <Link href="/admin/login" class="text-center"> Back to login </Link>
                    </p>
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
        <!-- /.login-box -->
    </div>

    <PageScript src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" />
    <PageScript src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" />
    <PageScript src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" />
    <PageScript src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.js" />
</template>
