<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import TeamForm from '../../../Components/Admin/TeamForm.vue';

defineProps({
    team: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AdminLayout>
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Edit Team Member</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                  <li class="breadcrumb-item"><Link href="/admin/team">Team</Link></li>
                  <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!--end::App Content Header-->

        <div class="app-content">
          <div class="container-fluid">
            <TeamForm :team="team" />
          </div>
        </div>
        <!--end::App Content-->
    </AdminLayout>
</template>
