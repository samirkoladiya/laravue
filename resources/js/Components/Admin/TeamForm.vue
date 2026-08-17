<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import ImageCropper from '../ImageCropper.vue';

const props = defineProps({
    team: { type: Object, default: null },
});

const isEdit = !!props.team;

const form = useForm({
    name: props.team?.name ?? '',
    designation: props.team?.designation ?? '',
    bio: props.team?.bio ?? '',
    email: props.team?.email ?? '',
    facebook_url: props.team?.facebook_url ?? '',
    twitter_url: props.team?.twitter_url ?? '',
    instagram_url: props.team?.instagram_url ?? '',
    linkedin_url: props.team?.linkedin_url ?? '',
    sort_order: props.team?.sort_order ?? 0,
    status: props.team?.status ?? true,
    photo: null,
    // Honeypot: real users never see or fill this field. Any value here
    // on submit means a bot filled in every input blindly.
    website: '',
});

const submit = () => {
    const url = isEdit ? `/admin/team/${props.team.id}` : '/admin/team';

    form.post(url, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            if (!isEdit) form.reset();
        },
    });
};
</script>

<template>
    <form novalidate @submit.prevent="submit">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <ImageCropper
                            v-model="form.photo"
                            :preview-url="team?.photo_url ?? null"
                            :aspect-ratio="1"
                            :output-width="500"
                            :output-height="500"
                            label="Photo"
                            :error="form.errors.photo"
                            hint="Square photo recommended. JPG, PNG or WEBP, max 2MB."
                        />
                    </div>

                    <div class="col-md-6">
                        <label for="team-name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input
                            id="team-name"
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

                    <div class="col-md-6">
                        <label for="team-designation" class="form-label">Designation <span class="text-danger">*</span></label>
                        <input
                            id="team-designation"
                            v-model="form.designation"
                            type="text"
                            name="designation"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.designation }"
                            maxlength="255"
                            required
                        />
                        <div v-if="form.errors.designation" class="invalid-feedback">{{ form.errors.designation }}</div>
                    </div>

                    <div class="col-md-6">
                        <label for="team-email" class="form-label">Email</label>
                        <input
                            id="team-email"
                            v-model="form.email"
                            type="email"
                            name="email"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.email }"
                            maxlength="255"
                        />
                        <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                    </div>

                    <div class="col-md-3">
                        <label for="team-sort-order" class="form-label">Sort Order</label>
                        <input
                            id="team-sort-order"
                            v-model.number="form.sort_order"
                            type="number"
                            name="sort_order"
                            min="0"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.sort_order }"
                        />
                        <div v-if="form.errors.sort_order" class="invalid-feedback">{{ form.errors.sort_order }}</div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input
                                id="team-status"
                                v-model="form.status"
                                class="form-check-input"
                                type="checkbox"
                            />
                            <label class="form-check-label" for="team-status">
                                {{ form.status ? 'Active' : 'Inactive' }}
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="team-bio" class="form-label">Bio</label>
                        <textarea
                            id="team-bio"
                            v-model="form.bio"
                            name="bio"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.bio }"
                            rows="3"
                            maxlength="1000"
                        ></textarea>
                        <div v-if="form.errors.bio" class="invalid-feedback">{{ form.errors.bio }}</div>
                    </div>

                    <div class="col-md-6">
                        <label for="team-facebook" class="form-label"><i class="bi bi-facebook me-1"></i>Facebook URL</label>
                        <input
                            id="team-facebook"
                            v-model="form.facebook_url"
                            type="url"
                            name="facebook_url"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.facebook_url }"
                            placeholder="https://facebook.com/..."
                            maxlength="255"
                        />
                        <div v-if="form.errors.facebook_url" class="invalid-feedback">{{ form.errors.facebook_url }}</div>
                    </div>

                    <div class="col-md-6">
                        <label for="team-twitter" class="form-label"><i class="bi bi-twitter-x me-1"></i>Twitter / X URL</label>
                        <input
                            id="team-twitter"
                            v-model="form.twitter_url"
                            type="url"
                            name="twitter_url"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.twitter_url }"
                            placeholder="https://x.com/..."
                            maxlength="255"
                        />
                        <div v-if="form.errors.twitter_url" class="invalid-feedback">{{ form.errors.twitter_url }}</div>
                    </div>

                    <div class="col-md-6">
                        <label for="team-instagram" class="form-label"><i class="bi bi-instagram me-1"></i>Instagram URL</label>
                        <input
                            id="team-instagram"
                            v-model="form.instagram_url"
                            type="url"
                            name="instagram_url"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.instagram_url }"
                            placeholder="https://instagram.com/..."
                            maxlength="255"
                        />
                        <div v-if="form.errors.instagram_url" class="invalid-feedback">{{ form.errors.instagram_url }}</div>
                    </div>

                    <div class="col-md-6">
                        <label for="team-linkedin" class="form-label"><i class="bi bi-linkedin me-1"></i>LinkedIn URL</label>
                        <input
                            id="team-linkedin"
                            v-model="form.linkedin_url"
                            type="url"
                            name="linkedin_url"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.linkedin_url }"
                            placeholder="https://linkedin.com/in/..."
                            maxlength="255"
                        />
                        <div v-if="form.errors.linkedin_url" class="invalid-feedback">{{ form.errors.linkedin_url }}</div>
                    </div>

                    <!-- Honeypot: hidden from real users; bots tend to fill every field they find -->
                    <div class="honeypot-field" aria-hidden="true">
                        <label for="team-website-field">Website</label>
                        <input
                            v-model="form.website"
                            type="text"
                            id="team-website-field"
                            tabindex="-1"
                            autocomplete="off"
                        />
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <Link href="/admin/team" class="btn btn-secondary">Cancel</Link>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                    {{ isEdit ? 'Update Team Member' : 'Add Team Member' }}
                </button>
            </div>
        </div>
    </form>
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
