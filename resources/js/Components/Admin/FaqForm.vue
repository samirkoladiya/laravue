<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    faq: { type: Object, default: null },
});

const isEdit = !!props.faq;

const form = useForm({
    question: props.faq?.question ?? '',
    answer: props.faq?.answer ?? '',
    sort_order: props.faq?.sort_order ?? 0,
    status: props.faq?.status ?? true,
    // Honeypot: real users never see or fill this field. Any value here
    // on submit means a bot filled in every input blindly.
    website: '',
});

const submit = () => {
    const url = isEdit ? `/admin/faq/${props.faq.id}` : '/admin/faq';

    form.post(url, {
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
                        <label for="faq-question" class="form-label">Question <span class="text-danger">*</span></label>
                        <input
                            id="faq-question"
                            v-model="form.question"
                            type="text"
                            name="question"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.question }"
                            maxlength="500"
                            required
                        />
                        <div v-if="form.errors.question" class="invalid-feedback">{{ form.errors.question }}</div>
                    </div>

                    <div class="col-12">
                        <label for="faq-answer" class="form-label">Answer <span class="text-danger">*</span></label>
                        <textarea
                            id="faq-answer"
                            v-model="form.answer"
                            name="answer"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.answer }"
                            rows="5"
                            maxlength="2000"
                            required
                        ></textarea>
                        <div v-if="form.errors.answer" class="invalid-feedback">{{ form.errors.answer }}</div>
                    </div>

                    <div class="col-md-3">
                        <label for="faq-sort-order" class="form-label">Sort Order</label>
                        <input
                            id="faq-sort-order"
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
                                id="faq-status"
                                v-model="form.status"
                                class="form-check-input"
                                type="checkbox"
                            />
                            <label class="form-check-label" for="faq-status">
                                {{ form.status ? 'Active' : 'Inactive' }}
                            </label>
                        </div>
                    </div>

                    <!-- Honeypot: hidden from real users; bots tend to fill every field they find -->
                    <div class="honeypot-field" aria-hidden="true">
                        <label for="faq-website-field">Website</label>
                        <input
                            v-model="form.website"
                            type="text"
                            id="faq-website-field"
                            tabindex="-1"
                            autocomplete="off"
                        />
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <Link href="/admin/faq" class="btn btn-secondary">Cancel</Link>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                    {{ isEdit ? 'Update FAQ' : 'Add FAQ' }}
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
