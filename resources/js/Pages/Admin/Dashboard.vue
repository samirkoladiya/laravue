<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import PageStyle from '../../Components/PageStyle.vue';
import DashboardCharts from './Dashboard/DashboardCharts.vue';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    trafficOverTime: {
        type: Array,
        required: true,
    },
    recentInquiries: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AdminLayout>
        <template #css>
            <PageStyle href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" />
        </template>

        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-primary">
                  <div class="inner"><h3>{{ stats.teamMembers }}</h3><p>Team Members</p></div>
                  <i class="bi bi-people small-box-icon"></i>
                  <Link href="/admin/team" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Manage <i class="bi bi-link-45deg"></i>
                  </Link>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-info">
                  <div class="inner"><h3>{{ stats.faqs }}</h3><p>FAQs</p></div>
                  <i class="bi bi-question-circle small-box-icon"></i>
                  <Link href="/admin/faq" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Manage <i class="bi bi-link-45deg"></i>
                  </Link>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-success">
                  <div class="inner"><h3>{{ stats.inquiries }}</h3><p>Inquiries</p></div>
                  <i class="bi bi-envelope small-box-icon"></i>
                  <Link href="/admin/inquiry" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    View All <i class="bi bi-link-45deg"></i>
                  </Link>
                </div>
              </div>
              <div class="col-lg-3 col-6">
                <div class="small-box text-bg-warning">
                  <div class="inner"><h3>{{ stats.visitors7d }}</h3><p>Visitors (7 Days)</p></div>
                  <i class="bi bi-graph-up small-box-icon"></i>
                  <Link href="/admin/analytics" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                    View Analytics <i class="bi bi-link-45deg"></i>
                  </Link>
                </div>
              </div>
            </div>
            <!--end::Row-->

            <!--begin::Row-->
            <div class="row">
              <div class="col-lg-8">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Traffic (Last 7 Days)</h3></div>
                  <div class="card-body">
                    <div v-if="!trafficOverTime.some((d) => d.page_views || d.sessions)" class="text-center text-secondary py-5">
                        <i class="bi bi-bar-chart fs-2 d-block mb-2"></i>No traffic data yet.
                    </div>
                    <div v-show="trafficOverTime.some((d) => d.page_views || d.sessions)" id="traffic-chart"></div>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="card mb-4">
                  <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Recent Inquiries</h3>
                    <Link href="/admin/inquiry" class="small">View All</Link>
                  </div>
                  <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li v-if="!recentInquiries.length" class="list-group-item text-center text-secondary py-4">
                            No inquiries yet.
                        </li>
                        <li v-for="inquiry in recentInquiries" :key="inquiry.id" class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ inquiry.name }}</div>
                                    <div class="text-secondary small text-truncate" style="max-width: 220px">{{ inquiry.subject }}</div>
                                </div>
                                <span class="text-secondary small text-nowrap ms-2">{{ inquiry.created_at }}</span>
                            </div>
                        </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row (main row) -->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->

        <template #js>
            <DashboardCharts :traffic-over-time="trafficOverTime" />
        </template>
    </AdminLayout>
</template>
