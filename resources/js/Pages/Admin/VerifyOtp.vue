<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import LoadingOverlay from '../../Components/LoadingOverlay.vue';
import PageStyle from '../../Components/PageStyle.vue';
import PageScript from '../../Components/PageScript.vue';

const page = usePage();

const props = defineProps({
    maskedEmail: { type: String, default: '' },
    expiresAt: { type: String, default: null },
});

const OTP_LENGTH = 4;

const digits = ref(Array(OTP_LENGTH).fill(''));
const inputs = ref([]);
const resending = ref(false);

const form = useForm({ otp: '' });

const remainingSeconds = ref(0);
let timer = null;

const updateRemaining = () => {
    if (!props.expiresAt) {
        remainingSeconds.value = 0;
        return;
    }
    const diff = Math.floor((new Date(props.expiresAt).getTime() - Date.now()) / 1000);
    remainingSeconds.value = Math.max(diff, 0);
};

onMounted(() => {
    updateRemaining();
    timer = setInterval(updateRemaining, 1000);
    inputs.value[0]?.focus();
});

onBeforeUnmount(() => {
    clearInterval(timer);
});

const expired = computed(() => remainingSeconds.value <= 0);

const formattedTime = computed(() => {
    const m = Math.floor(remainingSeconds.value / 60);
    const s = remainingSeconds.value % 60;
    return `${m}:${String(s).padStart(2, '0')}`;
});

const code = computed(() => digits.value.join(''));

const onDigitInput = (index, event) => {
    const value = event.target.value.replace(/\D/g, '').slice(-1);
    digits.value[index] = value;

    if (value && index < OTP_LENGTH - 1) {
        inputs.value[index + 1]?.focus();
    }
};

const onKeydown = (index, event) => {
    if (event.key === 'Backspace' && !digits.value[index] && index > 0) {
        inputs.value[index - 1]?.focus();
    }
};

const onPaste = (event) => {
    const pasted = (event.clipboardData?.getData('text') || '').replace(/\D/g, '').slice(0, OTP_LENGTH);
    if (!pasted) return;
    event.preventDefault();

    pasted.split('').forEach((char, i) => {
        digits.value[i] = char;
    });

    inputs.value[Math.min(pasted.length, OTP_LENGTH) - 1]?.focus();
};

const clearDigits = () => {
    digits.value = Array(OTP_LENGTH).fill('');
    inputs.value[0]?.focus();
};

const submit = () => {
    form.otp = code.value;
    form.post('/admin/verify-otp', {
        onError: () => clearDigits(),
    });
};

const resend = () => {
    resending.value = true;
    router.post(
        '/admin/verify-otp/resend',
        {},
        {
            onFinish: () => {
                resending.value = false;
                clearDigits();
            },
        },
    );
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
                    <LoadingOverlay v-if="form.processing || resending" />
                    <div v-if="page.props.flash?.error" class="alert alert-danger">{{ page.props.flash.error }}</div>
                    <p class="login-box-msg mb-1">Enter the 4-digit code we sent to</p>
                    <p v-if="maskedEmail" class="text-center fw-semibold mb-3">{{ maskedEmail }}</p>

                    <form novalidate @submit.prevent="submit">
                        <div class="d-flex justify-content-center gap-2 mb-2" @paste="onPaste">
                            <input
                                v-for="(digit, index) in digits"
                                :key="index"
                                :ref="(el) => (inputs[index] = el)"
                                :value="digit"
                                type="text"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                maxlength="1"
                                class="form-control text-center fs-4 otp-box"
                                :class="{ 'otp-box-invalid': form.errors.otp }"
                                @input="onDigitInput(index, $event)"
                                @keydown="onKeydown(index, $event)"
                            />
                        </div>
                        <div v-if="form.errors.otp" class="text-danger text-center small mb-3">
                            {{ form.errors.otp }}
                        </div>

                        <p class="text-center mb-3">
                            <span v-if="!expired" class="text-secondary">Code expires in {{ formattedTime }}</span>
                            <span v-else class="text-danger">Your code has expired.</span>
                        </p>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="form.processing || expired || code.length < OTP_LENGTH"
                                    >
                                        Verify Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <p class="mt-3 mb-1 text-center">
                        Didn't get a code?
                        <a href="#" @click.prevent="resend">Resend</a>
                    </p>
                    <p class="mb-0">
                        <Link href="/admin/forgot-password" class="text-center"> Use a different email </Link>
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

<style scoped>
.otp-box {
    width: 3.25rem;
    height: 3.25rem;
    padding: 0;
}

/* Plain red border on error - deliberately not Bootstrap's .is-invalid,
   which also draws a warning icon that has no room in a 1-character box. */
.otp-box-invalid {
    border-color: var(--bs-danger);
}
</style>
