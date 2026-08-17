<script setup>
import { onMounted } from 'vue';
import { loadScript } from '../../../lib/loadScript';

const props = defineProps({
    trafficOverTime: {
        type: Array,
        required: true,
    },
});

function initTrafficChart() {
    const el = document.querySelector('#traffic-chart');
    if (!el || typeof ApexCharts === 'undefined') return;

    new ApexCharts(el, {
        series: [
            { name: 'Page Views', data: props.trafficOverTime.map((d) => d.page_views) },
            { name: 'Unique Visitors', data: props.trafficOverTime.map((d) => d.unique_visitors) },
        ],
        chart: { height: 300, type: 'area', toolbar: { show: false } },
        colors: ['#0d6efd', '#20c997'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: { type: 'datetime', categories: props.trafficOverTime.map((d) => d.date) },
    }).render();
}

onMounted(async () => {
    try {
        await loadScript('https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js');
        initTrafficChart();
    } catch (error) {
        console.error(error);
    }
});
</script>

<template></template>
