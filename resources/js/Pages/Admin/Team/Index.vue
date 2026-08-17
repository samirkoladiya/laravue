<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';

const props = defineProps({
    teams: {
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
    searchForm.get('/admin/team', {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const deleting = ref(null);

const destroy = (team) => {
    if (!window.confirm(`Delete "${team.name}"? This action cannot be undone.`)) return;

    deleting.value = team.id;
    router.delete(`/admin/team/${team.id}`, {
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
              <div class="col-sm-6"><h3 class="mb-0">Team Management</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                  <li class="breadcrumb-item active" aria-current="page">Team</li>
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
                        <i class="bi bi-people-fill text-primary me-2"></i>
                        Team Members
                        <span class="badge text-bg-primary rounded-pill fw-normal ms-1">{{ teams.total }} total</span>
                    </h3>
                    <div class="d-flex align-items-center gap-2">
                        <form class="d-flex" role="search" @submit.prevent="search">
                            <input
                                v-model="searchForm.search"
                                type="search"
                                class="form-control form-control-sm"
                                placeholder="Search name or designation..."
                                style="min-width: 220px"
                            />
                            <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                        <Link href="/admin/team/create" class="btn btn-sm btn-primary text-nowrap">
                            <i class="bi bi-plus-lg me-1"></i>Add Team Member
                        </Link>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                          <thead class="table-light">
                            <tr class="text-uppercase small text-secondary">
                              <th class="ps-3" style="width: 64px">Photo</th>
                              <th>Name</th>
                              <th>Designation</th>
                              <th>Email</th>
                              <th style="width: 100px">Status</th>
                              <th class="text-end pe-3" style="width: 140px">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-if="teams.data.length === 0">
                              <td colspan="6" class="text-center text-secondary py-5">
                                <i class="bi bi-people fs-2 d-block mb-2"></i>
                                No team members found.
                              </td>
                            </tr>
                            <tr v-for="team in teams.data" :key="team.id">
                              <td class="ps-3">
                                <img
                                    v-if="team.photo_url"
                                    :src="team.photo_url"
                                    :alt="team.name"
                                    class="rounded-circle team-thumb"
                                />
                                <div v-else class="rounded-circle team-thumb d-flex align-items-center justify-content-center bg-secondary-subtle">
                                    <i class="bi bi-person-fill text-secondary"></i>
                                </div>
                              </td>
                              <td class="fw-semibold">{{ team.name }}</td>
                              <td>{{ team.designation }}</td>
                              <td class="text-secondary small">{{ team.email || '—' }}</td>
                              <td>
                                <span class="badge" :class="team.status ? 'text-bg-success' : 'text-bg-secondary'">
                                    {{ team.status ? 'Active' : 'Inactive' }}
                                </span>
                              </td>
                              <td class="text-end pe-3">
                                <Link
                                    :href="`/admin/team/${team.id}/edit`"
                                    class="btn btn-sm btn-outline-primary me-1"
                                    title="Edit"
                                >
                                    <i class="bi bi-pencil"></i>
                                </Link>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Delete"
                                    :disabled="deleting === team.id"
                                    @click="destroy(team)"
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
                  <div v-if="teams.data.length" class="card-footer clearfix">
                    <Pagination :links="teams.links" />
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
.team-thumb {
    width: 42px;
    height: 42px;
    object-fit: cover;
}
</style>
