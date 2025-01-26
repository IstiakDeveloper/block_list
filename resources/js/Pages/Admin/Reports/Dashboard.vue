<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { chartConfig } from './config/chartConfig';
import DateRangeFilter from '@/Components/DateRangeFilter.vue';
import ChartGrid from '@/Components/ChartGrid.vue';
import RecentCustomers from '@/Components/RecentCustomers.vue';
import MetricsSection from '@/Components/MetricsSection.vue';
import BranchUserTable from '@/Components/BranchUserTable.vue';
import BranchDetailsTable from '@/Components/BranchDetailsTable.vue';
import StatsOverview from '@/Components/StatsOverview.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    reportData: { type: Object, required: true }
});


const selectedDateRange = ref('all');
const startDate = ref(null);
const endDate = ref(null);

// Computed
const filteredBranchData = computed(() => {
    return props.reportData.branchDetails.map(branch => ({
        ...branch,
        total_customers: getFilteredTotal(branch)
    }));
});

// Methods
const getFilteredTotal = (branch) => {
    switch (selectedDateRange.value) {
        case 'month': return branch.this_month;
        case 'week': return branch.last_7_days;
        case 'custom': return branch.total_customers;
        default: return branch.all_time;
    }
};

const handleDateRangeChange = () => {
    if (selectedDateRange.value !== 'custom') {
        startDate.value = endDate.value = null;
    }
    router.get(
        route('admin.reports'),
        { dateRange: selectedDateRange.value, startDate: startDate.value, endDate: endDate.value },
        { preserveState: true }
    );
};

const downloadReport = (branchId = null) => {
    window.location.href = route('admin.reports.download', { branch_id: branchId });
};

watch([selectedDateRange, startDate, endDate], handleDateRangeChange);

// Stats calculations
const statsData = computed(() => ({
    totalCustomers: props.reportData.totalCustomers,
    totalBranches: props.reportData.totalBranches,
    growthRate: calculateGrowthRate(),
    currentMonthCustomers: getCurrentMonthCustomers(),
    lastMonthCustomers: getLastMonthCustomers(),
    topPerformingBranch: getTopPerformingBranch(),
    mostRecentGrowthBranch: getMostRecentGrowthBranch()
}));

const calculateGrowthRate = () => {
    const currentMonth = props.reportData.monthlyCustomers[props.reportData.monthlyCustomers.length - 1]?.count || 0;
    const lastMonth = props.reportData.monthlyCustomers[props.reportData.monthlyCustomers.length - 2]?.count || 0;
    if (lastMonth === 0) return 0;
    return ((currentMonth - lastMonth) / lastMonth * 100).toFixed(1);
};

const getCurrentMonthCustomers = () => {
    return props.reportData.monthlyCustomers[props.reportData.monthlyCustomers.length - 1]?.count || 0;
};

const getLastMonthCustomers = () => {
    return props.reportData.monthlyCustomers[props.reportData.monthlyCustomers.length - 2]?.count || 0;
};

const getTopPerformingBranch = () => {
    return [...props.reportData.branchDetails].sort((a, b) => b.total_customers - a.total_customers)[0];
};

const getMostRecentGrowthBranch = () => {
    return [...props.reportData.branchDetails].sort((a, b) => b.this_month - a.this_month)[0];
};

const handleFilter = () => {
    router.get(
        route('admin.reports'),
        {
            dateRange: selectedDateRange.value,
            startDate: startDate.value,
            endDate: endDate.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['reportData']
        }
    );
};
</script>

<template>

    <Head title="Reports Dashboard" />

    <div class="py-6 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reports Dashboard</h1>
                <button @click="downloadReport()" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Full Report
                </button>
            </div>

            <!-- Stats Overview -->
            <StatsOverview :stats="statsData" />

            <!-- Date Range Filter -->
            <DateRangeFilter v-model="selectedDateRange" v-model:startDate="startDate" v-model:endDate="endDate"
                @filter="handleFilter" />

            <div class="mt-8 flex gap-6">
                <BranchDetailsTable :branches="reportData.branchDetails" @download="downloadReport" />
                <BranchUserTable :branches="reportData.branchDetails" :getFilteredTotal="getFilteredTotal" />
            </div>
            <!-- Charts -->
            <ChartGrid :branch-data="props.reportData.branchWiseCustomers"
                :monthly-data="props.reportData.monthlyCustomers" :age-data="props.reportData.ageDistribution" />

            <!-- Recent Customers -->
            <RecentCustomers :customers="props.reportData.recentCustomers" />

            <!-- Metrics -->
            <MetricsSection :demographics="props.reportData.ageDistribution" :stats="statsData" />


        </div>
    </div>
</template>

<style scoped>
.btn-primary {
    @apply inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200;
}
</style>
