<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import LoadingOverlay from '../Components/LoadingOverlay.vue';
import MainLayout from '../Layouts/MainLayout.vue';
import { Analytics } from '../lib/analytics';

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    // Links this inquiry back to its analytics session (journey, traffic
    // source, etc). A client-supplied hint only - the server re-resolves
    // and silently ignores it if the session doesn't exist, so this never
    // affects submission success.
    analytics_session_id: Analytics.getSessionId(),
    // Honeypot: real visitors never see or fill this field. Any value
    // here on submit means a bot filled in every input blindly.
    website: '',
});

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <MainLayout>

        <!-- Page Title -->
        <div class="page-title">
            <div class="container d-lg-flex justify-content-between align-items-center">
                <h1 class="mb-2 mb-lg-0">Contact</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><Link href="/">Home</Link></li>
                        <li class="current">Contact</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->

        <!-- Contact Section -->
        <section id="contact" class="contact section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Contact</h2>
            <p>Have a question or a project in mind? We'd love to hear from you</p>
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="row gy-4">

            <div class="col-lg-5">

                <div class="info-wrap">
                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                    <i class="bi bi-geo-alt flex-shrink-0"></i>
                    <div>
                    <h3>Address</h3>
                    <p>A108 Adam Street, New York, NY 535022</p>
                    </div>
                </div><!-- End Info Item -->

                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                    <i class="bi bi-telephone flex-shrink-0"></i>
                    <div>
                    <h3>Call Us</h3>
                    <p>+1 5589 55488 55</p>
                    </div>
                </div><!-- End Info Item -->

                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                    <i class="bi bi-envelope flex-shrink-0"></i>
                    <div>
                    <h3>Email Us</h3>
                    <p>info@example.com</p>
                    </div>
                </div><!-- End Info Item -->

                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d48389.78314118045!2d-74.006138!3d40.710059!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a22a3bda30d%3A0xb89d1fe6bc499443!2sDowntown%20Conference%20Center!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus" frameborder="0" style="border:0; width: 100%; height: 270px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <div class="col-lg-7">
                <form class="php-email-form position-relative" data-aos="fade-up" data-aos-delay="200" novalidate @submit.prevent="submit">
                <LoadingOverlay v-if="form.processing" />
                <div class="row gy-4">

                    <div class="col-md-6">
                    <label for="name-field" class="pb-2">Your Name <span class="text-danger">*</span></label>
                    <input v-model="form.name" type="text" name="name" id="name-field" class="form-control" :class="{ 'is-invalid': form.errors.name }" maxlength="255" required>
                    <div v-if="form.errors.name" class="invalid-feedback d-block">{{ form.errors.name }}</div>
                    </div>

                    <div class="col-md-6">
                    <label for="email-field" class="pb-2">Your Email <span class="text-danger">*</span></label>
                    <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" name="email" id="email-field" maxlength="255" required>
                    <div v-if="form.errors.email" class="invalid-feedback d-block">{{ form.errors.email }}</div>
                    </div>

                    <div class="col-md-12">
                    <label for="subject-field" class="pb-2">Subject <span class="text-danger">*</span></label>
                    <input v-model="form.subject" type="text" class="form-control" :class="{ 'is-invalid': form.errors.subject }" name="subject" id="subject-field" maxlength="255" required>
                    <div v-if="form.errors.subject" class="invalid-feedback d-block">{{ form.errors.subject }}</div>
                    </div>

                    <div class="col-md-12">
                    <label for="message-field" class="pb-2">Message</label>
                    <textarea v-model="form.message" class="form-control" :class="{ 'is-invalid': form.errors.message }" name="message" rows="10" id="message-field" maxlength="5000"></textarea>
                    <div v-if="form.errors.message" class="invalid-feedback d-block">{{ form.errors.message }}</div>
                    </div>

                    <!-- Honeypot: hidden from real users; bots tend to fill every field they find -->
                    <div class="honeypot-field" aria-hidden="true">
                    <label for="website-field">Website</label>
                    <input v-model="form.website" type="text" name="website" id="website-field" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="col-md-12 text-center">
                    <div v-if="Object.keys(form.errors).length" class="error-message d-block">Please correct the errors above and try again.</div>
                    <div v-if="page.props.flash?.success" class="sent-message d-block">{{ page.props.flash.success }}</div>

                    <button type="submit" :disabled="form.processing">Send Message</button>
                    </div>

                </div>
                </form>
            </div><!-- End Contact Form -->

            </div>

        </div>

        </section><!-- /Contact Section -->

    </MainLayout>
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
