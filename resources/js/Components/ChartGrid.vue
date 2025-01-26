<script setup>
import { computed } from 'vue';
import { Bar, Line, Pie } from 'vue-chartjs';

const props = defineProps({
    branchData: { type: Array, required: true },
    monthlyData: { type: Array, required: true },
    ageData: { type: Array, required: true }
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: { color: 'currentColor', font: { size: 12 } }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 10,
            displayColors: false
        }
    },
    scales: {
        x: {
            ticks: { color: 'currentColor' },
            grid: { color: 'rgba(128, 128, 128, 0.2)' }
        },
        y: {
            ticks: { color: 'currentColor' },
            grid: { color: 'rgba(128, 128, 128, 0.2)' }
        }
    }
};

const branchChartData = computed(() => ({
    labels: props.branchData.map(b => b.branch_name),
    datasets: [{
        label: 'Number of Customers',
        data: props.branchData.map(b => b.customers_count),
        backgroundColor: '#3B82F6',
        borderRadius: 6
    }]
}));

const monthlyChartData = computed(() => ({
    labels: props.monthlyData.map(m => m.month),
    datasets: [{
        label: 'New Customers',
        data: props.monthlyData.map(m => m.count),
        borderColor: '#10B981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true
    }]
}));

const ageChartData = computed(() => ({
    labels: props.ageData.map(a => a.age_group),
    datasets: [{
        data: props.ageData.map(a => a.count),
        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
        borderWidth: 0
    }]
}));
</script>

<template>
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <div class="chart-card">
            <h3 class="chart-title">Customers by Branch</h3>
            <div class="chart-container">
                <Bar :data="branchChartData" :options="chartOptions" />
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">Monthly Customer Growth</h3>
            <div class="chart-container">
                <Line :data="monthlyChartData" :options="chartOptions" />
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">Age Distribution</h3>
            <div class="chart-container">
                <Pie :data="ageChartData" :options="chartOptions" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.chart-card {
    @apply bg-white dark:bg-gray-800 shadow rounded-lg p-6;
}

.chart-title {
    @apply text-lg font-medium text-gray-900 dark:text-white mb-4;
}

.chart-container {
    @apply h-80;
}
</style>
