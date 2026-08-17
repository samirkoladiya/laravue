<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';

const props = defineProps({
    faqs: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const searchForm = useForm({
    search: props.filters.search ?? '',
});

const search = () => {
    searchForm.get('/admin/faq', {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const deleting = ref(null);

const destroy = (faq) => {
    if (!window.confirm('Delete this FAQ? This action cannot be undone.')) return;

    deleting.value = faq.id;
    router.delete(`/admin/faq/${faq.id}`, {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    });
};
</script>

<template>
    <AdminLayout>
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">FAQ Management</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                  <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!--end::App Content Header-->

        <div class="app-content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card mb-4 shadow-sm">
                  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-question-circle-fill text-primary me-2"></i>
                        FAQs
                        <span class="badge text-bg-primary rounded-pill fw-normal ms-1">{{ faqs.total }} total</span>
                    </h3>
                    <div class="d-flex align-items-center gap-2">
                        <form class="d-flex" role="search" @submit.prevent="search">
                            <input
                                v-model="searchForm.search"
                                type="search"
                                class="form-control form-control-sm"
                                placeholder="Search question..."
                                style="min-width: 220px"
                            />
                            <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                        <Link href="/admin/faq/create" class="btn btn-sm btn-primary text-nowrap">
                            <i class="bi bi-plus-lg me-1"></i>Add FAQ
                        </Link>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                          <thead class="table-light">
                            <tr class="text-uppercase small text-secondary">
                              <th class="ps-3">Question</th>
                              <th style="width: 100px">Status</th>
                              <th class="text-end pe-3" style="width: 140px">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-if="faqs.data.length === 0">
                              <td colspan="3" class="text-center text-secondary py-5">
                                <i class="bi bi-question-circle fs-2 d-block mb-2"></i>
                                No FAQs found.
                              </td>
                            </tr>
                            <tr v-for="faq in faqs.data" :key="faq.id">
                              <td class="ps-3">
                                <div class="fw-semibold">{{ faq.question }}</div>
                                <div class="text-secondary small answer-cell">{{ faq.answer }}</div>
                              </td>
                              <td>
                                <span class="badge" :class="faq.status ? 'text-bg-success' : 'text-bg-secondary'">
                                    {{ faq.status ? 'Active' : 'Inactive' }}
                                </span>
                              </td>
                              <td class="text-end pe-3">
                                <Link
                                    :href="`/admin/faq/${faq.id}/edit`"
                                    class="btn btn-sm btn-outline-primary me-1"
                                    title="Edit"
                                >
                                    <i class="bi bi-pencil"></i>
                                </Link>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Delete"
                                    :disabled="deleting === faq.id"
                                    @click="destroy(faq)"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                    </div>
                  </div>
                  <!-- /.card-body -->
                  <div v-if="faqs.data.length" class="card-footer clearfix">
                    <Pagination :links="faqs.links" />
                  </div>
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            </div>
          </div>
        </div>
        <!--end::App Content-->
    </AdminLayout>
</template>

<style scoped>
.answer-cell {
    max-width: 480px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
