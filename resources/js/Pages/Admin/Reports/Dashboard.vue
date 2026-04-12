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
    reportData: { type: Object, required: true },
    userBranches: { type: Array, required: true }
});

const selectedDateRange = ref(props.reportData.filter?.dateRange || 'all');
const startDate = ref(props.reportData.filter?.startDate || null);
const endDate = ref(props.reportData.filter?.endDate || null);
const selectedBranch = ref(props.reportData.filter?.branch_filter || '');

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
    applyFilters();
};

const handleBranchFilter = () => {
    applyFilters();
};

const applyFilters = () => {
    router.get(
        route('admin.reports'),
        {
            dateRange: selectedDateRange.value,
            startDate: startDate.value,
            endDate: endDate.value,
            branch_filter: selectedBranch.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['reportData']
        }
    );
};

const downloadReport = (branchId = null) => {
    const params = {
        dateRange: selectedDateRange.value,
        startDate: startDate.value || undefined,
        endDate: endDate.value || undefined,
        branch_filter: selectedBranch.value || undefined,
    };
    if (branchId != null) {
        params.branch_id = branchId;
    }
    window.location.href = route('admin.reports.download', params);
};

const downloadBranchUsersReport = () => {
    const params = {
        dateRange: selectedDateRange.value,
        startDate: startDate.value,
        endDate: endDate.value,
        branch_filter: selectedBranch.value
    };

    if (selectedBranch.value) {
        params.branch_id = selectedBranch.value;
    }

    window.location.href = route('admin.reports.branch-users-pdf', params);
};

watch([selectedDateRange, startDate, endDate], handleDateRangeChange);
watch(selectedBranch, handleBranchFilter);

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
    applyFilters();
};
</script>

<template>

    <Head title="Reports Dashboard" />

    <div class="py-4 sm:py-6 dark:bg-gray-900">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Reports Dashboard</h1>
                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:gap-3">
                    <button @click="downloadBranchUsersReport()" class="btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="hidden sm:inline">Branch Users Report</span>
                        <span class="sm:hidden">Users Report</span>
                    </button>
                    <button @click="downloadReport()" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 sm:w-5 sm:h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="hidden sm:inline">Download Full Report</span>
                        <span class="sm:hidden">Download</span>
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <StatsOverview :stats="statsData" />

            <!-- Filters Row -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:items-end sm:gap-6 sm:space-y-0">
                <!-- Date Range Filter -->
                <div class="flex-1 min-w-0">
                    <DateRangeFilter v-model="selectedDateRange" v-model:startDate="startDate" v-model:endDate="endDate"
                        @filter="handleFilter" class="w-full" />
                </div>

                <!-- Branch Filter -->
                <div class="mt-8 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap items-center gap-4">
                            <!-- Date Range Filter -->
                            <!-- Branch Filter -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Branch:</label>
                                <div class="relative">
                                    <select v-model="selectedBranch"
                                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500 min-w-[180px] transition-colors duration-200 appearance-none cursor-pointer hover:border-gray-400 dark:hover:border-gray-500 pr-10">
                                        <option value="">All Branches</option>
                                        <option v-for="branch in userBranches" :key="branch.id" :value="branch.id">
                                            {{ branch.branch_name }} ({{ branch.branch_code }})
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apply Filters Button (Mobile Only) -->
                <div class="block sm:hidden">
                    <button @click="handleFilter" class="w-full px-4 py-2.5 bg-blue-600 dark:bg-blue-500 text-white text-sm font-medium rounded-lg
                           hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                           transition-colors duration-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </div>



            <!-- Tables - Stack on mobile, side by side on desktop -->
            <div class="flex flex-col gap-6 mt-8 lg:flex-row">
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
    @apply inline-flex items-center justify-center px-3 py-2 sm:px-4 text-sm sm:text-base bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200;
}

.btn-secondary {
    @apply inline-flex items-center justify-center px-3 py-2 sm:px-4 text-sm sm:text-base bg-gray-600 dark:bg-gray-500 text-white rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200;
}

.form-select {
    @apply block w-full px-3 py-2 text-base border rounded-md shadow-sm focus:outline-none sm:text-sm;
}
</style>
