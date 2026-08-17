<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import { loadScript } from '../../../lib/loadScript';

const props = defineProps({
    filters: { type: Object, required: true },
    summary: { type: Object, required: true },
    trafficOverTime: { type: Array, required: true },
    trafficSources: { type: Array, required: true },
    topPages: { type: Array, required: true },
    landingPages: { type: Array, required: true },
    exitPages: { type: Array, required: true },
    deviceBreakdown: { type: Object, required: true },
    conversions: { type: Array, required: true },
});

const PRESETS = [
    { value: 'today', label: 'Today' },
    { value: 'yesterday', label: 'Yesterday' },
    { value: '7d', label: 'Last 7 Days' },
    { value: '30d', label: 'Last 30 Days' },
    { value: 'this_month', label: 'This Month' },
    { value: 'last_month', label: 'Last Month' },
    { value: 'custom', label: 'Custom' },
];

const SOURCE_LABELS = {
    direct: 'Direct',
    organic: 'Organic Search',
    referral: 'Referral',
    social: 'Social',
    paid: 'Paid',
};

const range = ref(props.filters.range ?? '7d');
const customFrom = ref(props.filters.from ?? '');
const customTo = ref(props.filters.to ?? '');

const applyRange = () => {
    const params = { range: range.value };

    if (range.value === 'custom') {
        params.from = customFrom.value;
        params.to = customTo.value;
    }

    router.get('/admin/analytics', params, { preserveState: true, preserveScroll: true, replace: true });
};

const exportUrl = computed(() => {
    const params = new URLSearchParams({ range: props.filters.range ?? '7d' });

    if (props.filters.range === 'custom') {
        if (props.filters.from) params.set('from', props.filters.from);
        if (props.filters.to) params.set('to', props.filters.to);
    }

    return `/admin/analytics/export?${params.toString()}`;
});

const sourceLabel = (source) => SOURCE_LABELS[source] ?? source;

const formatDuration = (seconds) => {
    if (seconds === null || seconds === undefined) return '—';

    const minutes = Math.floor(seconds / 60);
    const remaining = seconds % 60;

    return minutes > 0 ? `${minutes}m ${remaining}s` : `${remaining}s`;
};

const formatPercent = (value) => (value === null || value === undefined ? '—' : `${value}%`);

// --- Real-time "online now" polling ---
const onlineCount = ref(null);
let pollTimer = null;

const pollOnline = async () => {
    try {
        const res = await fetch('/admin/analytics/realtime');
        if (!res.ok) return;
        const data = await res.json();
        onlineCount.value = data.online;
    } catch {
        // Transient failure - the next poll will retry.
    }
};

// --- Charts (ApexCharts via CDN, matching Admin/Dashboard's convention) ---
let trafficChart = null;
let sourcesChart = null;

const renderTrafficChart = () => {
    const el = document.querySelector('#traffic-over-time-chart');
    if (!el || typeof ApexCharts === 'undefined') return;

    trafficChart?.destroy();

    trafficChart = new ApexCharts(el, {
        series: [
            { name: 'Page Views', data: props.trafficOverTime.map((d) => d.page_views) },
            { name: 'Unique Visitors', data: props.trafficOverTime.map((d) => d.unique_visitors) },
        ],
        chart: { height: 300, type: 'area', toolbar: { show: false } },
        legend: { show: true },
        colors: ['#0d6efd', '#20c997'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: { categories: props.trafficOverTime.map((d) => d.date), type: 'datetime' },
    });
    trafficChart.render();
};

const renderSourcesChart = () => {
    const el = document.querySelector('#traffic-sources-chart');
    if (!el || typeof ApexCharts === 'undefined') return;

    sourcesChart?.destroy();

    if (!props.trafficSources.length) return;

    sourcesChart = new ApexCharts(el, {
        series: props.trafficSources.map((s) => s.sessions),
        labels: props.trafficSources.map((s) => sourceLabel(s.source)),
        chart: { height: 260, type: 'donut' },
        legend: { position: 'bottom' },
        colors: ['#0d6efd', '#20c997', '#ffc107', '#6f42c1', '#dc3545'],
    });
    sourcesChart.render();
};

const renderCharts = async () => {
    try {
        await loadScript('https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js');
        renderTrafficChart();
        renderSourcesChart();
    } catch (error) {
        console.error(error);
    }
};

// Inertia reuses this component instance across range-filter navigations
// (preserveState), so props change without a remount - re-render charts
// whenever the underlying data changes.
watch(() => [props.trafficOverTime, props.trafficSources], () => renderCharts());

onMounted(() => {
    renderCharts();
    pollOnline();
    pollTimer = setInterval(pollOnline, 20000);
});

onUnmounted(() => {
    clearInterval(pollTimer);
    trafficChart?.destroy();
    sourcesChart?.destroy();
});
</script>

<template>
    <AdminLayout>
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row align-items-center">
              <div class="col-sm-6"><h3 class="mb-0">Analytics</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><Link href="/admin/dashboard">Home</Link></li>
                  <li class="breadcrumb-item active" aria-current="page">Analytics</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!--end::App Content Header-->

        <div class="app-content">
          <div class="container-fluid">

            <!-- Filters + real-time -->
            <div class="row mb-3 align-items-center g-2">
              <div class="col-md-8">
                <div class="btn-group flex-wrap" role="group">
                  <button
                    v-for="preset in PRESETS"
                    :key="preset.value"
                    type="button"
                    class="btn btn-sm"
                    :class="range === preset.value ? 'btn-primary' : 'btn-outline-secondary'"
                    @click="range = preset.value; if (preset.value !== 'custom') applyRange();"
                  >
                    {{ preset.label }}
                  </button>
                </div>
                <div v-if="range === 'custom'" class="d-inline-flex align-items-center gap-2 ms-2 mt-2 mt-md-0">
                  <input v-model="customFrom" type="date" class="form-control form-control-sm" style="width: auto" />
                  <span class="text-secondary">to</span>
                  <input v-model="customTo" type="date" class="form-control form-control-sm" style="width: auto" />
                  <button type="button" class="btn btn-sm btn-primary" @click="applyRange">Apply</button>
                </div>
              </div>
              <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end gap-2">
                <span class="badge text-bg-success fs-6 fw-normal">
                    <i class="bi bi-circle-fill text-white me-1" style="font-size: 0.5rem"></i>
                    {{ onlineCount === null ? '—' : onlineCount }} online now
                </span>
                <a :href="exportUrl" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
              </div>
            </div>

            <!-- Summary cards -->
            <div class="row">
              <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box text-bg-primary">
                  <div class="inner"><h3>{{ summary.unique_visitors }}</h3><p>Visitors</p></div>
                  <i class="bi bi-people small-box-icon"></i>
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box text-bg-info">
                  <div class="inner"><h3>{{ summary.sessions }}</h3><p>Sessions</p></div>
                  <i class="bi bi-clock-history small-box-icon"></i>
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box text-bg-secondary">
                  <div class="inner"><h3>{{ summary.page_views }}</h3><p>Page Views</p></div>
                  <i class="bi bi-file-earmark-text small-box-icon"></i>
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box text-bg-success">
                  <div class="inner"><h3>{{ summary.leads }}</h3><p>Leads</p></div>
                  <i class="bi bi-person-check small-box-icon"></i>
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box text-bg-warning">
                  <div class="inner"><h3>{{ formatPercent(summary.conversion_rate) }}</h3><p>Conversion Rate</p></div>
                  <i class="bi bi-graph-up-arrow small-box-icon"></i>
                </div>
              </div>
              <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box text-bg-danger">
                  <div class="inner"><h3>{{ formatDuration(summary.avg_session_duration_seconds) }}</h3><p>Avg. Session</p></div>
                  <i class="bi bi-stopwatch small-box-icon"></i>
                </div>
              </div>
            </div>
            <!-- /Summary cards -->

            <!-- Charts -->
            <div class="row">
              <div class="col-lg-8">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Traffic Over Time</h3></div>
                  <div class="card-body">
                    <div v-if="!trafficOverTime.some((d) => d.page_views || d.sessions)" class="text-center text-secondary py-5">
                        <i class="bi bi-bar-chart fs-2 d-block mb-2"></i>No traffic data for this period yet.
                    </div>
                    <div v-show="trafficOverTime.some((d) => d.page_views || d.sessions)" id="traffic-over-time-chart"></div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Traffic Sources</h3></div>
                  <div class="card-body">
                    <div v-if="!trafficSources.length" class="text-center text-secondary py-5">
                        <i class="bi bi-signpost-split fs-2 d-block mb-2"></i>No traffic yet.
                    </div>
                    <div v-show="trafficSources.length" id="traffic-sources-chart"></div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /Charts -->

            <!-- Pages -->
            <div class="row">
              <div class="col-lg-4">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Top Pages</h3></div>
                  <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                      <tbody>
                        <tr v-if="!topPages.length"><td class="text-center text-secondary py-4">No data.</td></tr>
                        <tr v-for="row in topPages" :key="row.path">
                          <td class="text-truncate" style="max-width: 220px">{{ row.path }}</td>
                          <td class="text-end text-secondary">{{ row.views }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Landing Pages</h3></div>
                  <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                      <tbody>
                        <tr v-if="!landingPages.length"><td class="text-center text-secondary py-4">No data.</td></tr>
                        <tr v-for="row in landingPages" :key="row.path">
                          <td class="text-truncate" style="max-width: 220px">{{ row.path }}</td>
                          <td class="text-end text-secondary">{{ row.sessions }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Exit Pages</h3></div>
                  <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                      <tbody>
                        <tr v-if="!exitPages.length"><td class="text-center text-secondary py-4">No data yet.</td></tr>
                        <tr v-for="row in exitPages" :key="row.path">
                          <td class="text-truncate" style="max-width: 220px">{{ row.path }}</td>
                          <td class="text-end text-secondary">{{ row.sessions }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /Pages -->

            <!-- Audience -->
            <div class="row">
              <div class="col-lg-4">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Device Type</h3></div>
                  <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                      <tbody>
                        <tr v-if="!deviceBreakdown.device_type.length"><td class="text-center text-secondary py-4">No data.</td></tr>
                        <tr v-for="row in deviceBreakdown.device_type" :key="row.label">
                          <td class="text-capitalize">{{ row.label }}</td>
                          <td class="text-end text-secondary">{{ row.sessions }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Browser</h3></div>
                  <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                      <tbody>
                        <tr v-if="!deviceBreakdown.browser.length"><td class="text-center text-secondary py-4">No data.</td></tr>
                        <tr v-for="row in deviceBreakdown.browser" :key="row.label">
                          <td>{{ row.label }}</td>
                          <td class="text-end text-secondary">{{ row.sessions }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Operating System</h3></div>
                  <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                      <tbody>
                        <tr v-if="!deviceBreakdown.os.length"><td class="text-center text-secondary py-4">No data.</td></tr>
                        <tr v-for="row in deviceBreakdown.os" :key="row.label">
                          <td>{{ row.label }}</td>
                          <td class="text-end text-secondary">{{ row.sessions }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /Audience -->

            <!-- Conversions -->
            <div class="row">
              <div class="col-12">
                <div class="card mb-4">
                  <div class="card-header"><h3 class="card-title">Conversions &amp; Events</h3></div>
                  <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                      <thead class="table-light">
                        <tr class="text-uppercase small text-secondary">
                          <th class="ps-3">Event</th>
                          <th>Type</th>
                          <th class="text-end pe-3">Count</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="!conversions.length">
                          <td colspan="3" class="text-center text-secondary py-4">No events tracked for this period yet.</td>
                        </tr>
                        <tr v-for="row in conversions" :key="row.event_name">
                          <td class="ps-3">{{ row.event_name }}</td>
                          <td>
                            <span class="badge" :class="row.is_conversion ? 'text-bg-success' : 'text-bg-secondary'">
                                {{ row.is_conversion ? 'Lead' : 'Engagement' }}
                            </span>
                          </td>
                          <td class="text-end pe-3">{{ row.total }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /Conversions -->

          </div>
        </div>
        <!--end::App Content-->
    </AdminLayout>
</template>
