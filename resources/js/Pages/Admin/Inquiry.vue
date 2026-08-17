<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import Pagination from '../../Components/Pagination.vue';

defineProps({
    inquiries: {
        type: Object,
        required: true,
    },
});

const selectedInquiry = ref(null);
</script>

<template>
    <AdminLayout>
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Contact Inquiries</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                  <li class="breadcrumb-item active" aria-current="page">Inquiries</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-12">
                <div class="card mb-4 shadow-sm">
                  <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-envelope-paper-fill text-primary me-2"></i>
                        Inquiries
                    </h3>
                    <span class="badge text-bg-primary rounded-pill fw-normal">
                        {{ inquiries.total }} total
                    </span>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                          <thead class="table-light">
                            <tr class="text-uppercase small text-secondary">
                              <th class="ps-3" style="width: 48px">#</th>
                              <th>Contact</th>
                              <th>Subject</th>
                              <th style="width: 180px">Received</th>
                              <th class="text-end pe-3" style="width: 110px">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-if="inquiries.data.length === 0">
                              <td colspan="5" class="text-center text-secondary py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No inquiries found.
                              </td>
                            </tr>
                            <tr v-for="(inquiry, index) in inquiries.data" :key="inquiry.id">
                              <td class="ps-3 text-secondary">{{ inquiries.from + index }}</td>
                              <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ inquiry.name }}</div>
                                        <div class="text-secondary small">{{ inquiry.email }}</div>
                                    </div>
                                </div>
                              </td>
                              <td class="subject-cell">{{ inquiry.subject }}</td>
                              <td class="text-secondary small">{{ inquiry.created_at }}</td>
                              <td class="text-end pe-3">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#inquiryDetailModal"
                                    @click="selectedInquiry = inquiry"
                                >
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                    </div>
                  </div>
                  <!-- /.card-body -->
                  <div v-if="inquiries.data.length" class="card-footer clearfix">
                    <Pagination :links="inquiries.links" />
                  </div>
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->

        <!--begin::Inquiry Detail Modal-->
        <Teleport to="body">
            <div id="inquiryDetailModal" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div v-if="selectedInquiry" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-envelope-paper-fill text-primary me-2"></i>
                                {{ selectedInquiry.subject }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="fw-semibold">{{ selectedInquiry.name }}</div>
                                    <div class="text-secondary small">{{ selectedInquiry.email }}</div>
                                </div>
                            </div>
                            <p class="text-secondary small mb-2">
                                <i class="bi bi-clock me-1"></i>Received {{ selectedInquiry.created_at }}
                            </p>
                            <hr />
                            <p class="mb-0" style="white-space: pre-wrap">{{ selectedInquiry.message }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
        <!--end::Inquiry Detail Modal-->
    </AdminLayout>
</template>

<style scoped>
.avatar-circle {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: var(--bs-primary);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
}

.avatar-circle-lg {
    width: 48px;
    height: 48px;
    font-size: 0.9rem;
}

.subject-cell {
    max-width: 320px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
