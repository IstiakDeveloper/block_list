<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    LineElement,
    PointElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';
import { Bar, Line, Pie } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    LineElement,
    PointElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
);

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    reportData: {
        type: Object,
        required: true,
    }
});

const calculateGrowthRate = () => {
    const currentMonth = props.reportData.monthlyCustomers[props.reportData.monthlyCustomers.length - 1]?.count || 0;
    const lastMonth = props.reportData.monthlyCustomers[props.reportData.monthlyCustomers.length - 2]?.count || 0;

    if (lastMonth === 0) return 0;
    const growthRate = ((currentMonth - lastMonth) / lastMonth) * 100;
    return growthRate.toFixed(1);
};

const getCurrentMonthCustomers = () => {
    return props.reportData.monthlyCustomers[props.reportData.monthlyCustomers.length - 1]?.count || 0;
};

const getLastMonthCustomers = () => {
    return props.reportData.monthlyCustomers[props.reportData.monthlyCustomers.length - 2]?.count || 0;
};

const getTopPerformingBranch = () => {
    return [...props.reportData.branchDetails]
        .sort((a, b) => b.total_customers - a.total_customers)[0];
};

const getMostRecentGrowthBranch = () => {
    return [...props.reportData.branchDetails]
        .sort((a, b) => b.this_month - a.this_month)[0];
};

const selectedBranch = ref(null);
const selectedDateRange = ref('all'); // 'all', 'month', 'week'

// Chart options with dark mode support
const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                color: 'currentColor',
                font: {
                    size: 12
                }
            }
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
            ticks: {
                color: 'currentColor'
            },
            grid: {
                color: 'rgba(128, 128, 128, 0.2)'
            }
        },
        y: {
            ticks: {
                color: 'currentColor'
            },
            grid: {
                color: 'rgba(128, 128, 128, 0.2)'
            }
        }
    }
}));

// Chart data
const branchChartData = computed(() => ({
    labels: props.reportData.branchWiseCustomers.map(b => b.branch_name),
    datasets: [{
        label: 'Number of Customers',
        data: props.reportData.branchWiseCustomers.map(b => b.customers_count),
        backgroundColor: '#3B82F6',
        borderRadius: 6,
    }]
}));

const monthlyChartData = computed(() => ({
    labels: props.reportData.monthlyCustomers.map(m => m.month),
    datasets: [{
        label: 'New Customers',
        data: props.reportData.monthlyCustomers.map(m => m.count),
        borderColor: '#10B981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true
    }]
}));

const ageChartData = computed(() => ({
    labels: props.reportData.ageDistribution.map(a => a.age_group),
    datasets: [{
        data: props.reportData.ageDistribution.map(a => a.count),
        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
        borderWidth: 0
    }]
}));

const downloadReport = (branchId = null) => {
    window.location.href = route('admin.reports.download', { branch_id: branchId });
};

// Filter functions
const filteredBranchData = computed(() => {
    let data = props.reportData.branchDetails;
    if (selectedDateRange.value === 'month') {
        return data.map(branch => ({
            ...branch,
            total_customers: branch.this_month
        }));
    } else if (selectedDateRange.value === 'week') {
        return data.map(branch => ({
            ...branch,
            total_customers: branch.last_7_days
        }));
    }
    return data;
});
</script>

<template>

    <Head title="Reports Dashboard" />

    <div class="py-6 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Download Button -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reports Dashboard</h1>
                <button @click="downloadReport()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Full Report
                </button>
            </div>

            <!-- Stats Overview -->
            <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Customers -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Customers</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ reportData.totalCustomers }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Branches -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Branches</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ reportData.totalBranches }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date Range:</label>
                    <select v-model="selectedDateRange"
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">All Time</option>
                        <option value="month">This Month</option>
                        <option value="week">Last 7 Days</option>
                    </select>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Branch-wise Customer Distribution -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customers by Branch</h3>
                    <div class="h-80">
                        <Bar :data="branchChartData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Monthly Customer Growth -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Monthly Customer Growth</h3>
                    <div class="h-80">
                        <Line :data="monthlyChartData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Age Distribution -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Age Distribution</h3>
                    <div class="h-80">
                        <Pie :data="ageChartData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Recent Customers -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Customers</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Branch
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Phone
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="customer in reportData.recentCustomers" :key="customer.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ customer.name }}
                                        <span v-if="customer.name_bn"
                                            class="text-xs text-gray-500 dark:text-gray-400 block">
                                            {{ customer.name_bn }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ customer.branch.branch_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ customer.phone_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ new Date(customer.created_at).toLocaleDateString() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics Section -->
            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Customer Demographics -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Customer Demographics
                    </h3>
                    <div class="space-y-4">
                        <!-- Age Groups -->
                        <div v-for="(count, ageGroup) in reportData.ageDistribution" :key="ageGroup"
                            class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ ageGroup }}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full"
                                        :style="{ width: `${(count.count / reportData.totalCustomers * 100)}%` }">
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ Math.round(count.count / reportData.totalCustomers * 100) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Growth Stats -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Monthly Growth
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Growth Rate</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                {{ calculateGrowthRate() }}%
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">This Month</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ getCurrentMonthCustomers() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Last Month</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ getLastMonthCustomers() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Branch Performance -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Branch Performance
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Top Performing</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ getTopPerformingBranch()?.name }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Most Recent Growth</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ getMostRecentGrowthBranch()?.name }}
                            </span>
                        </div>
                    </div>
                </div>



            </div>

            <!-- Branch Details Section -->
            <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center border-b dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Branch-wise Reports</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Branch Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Total Customers
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    This Month
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Last 7 Days
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="branch in filteredBranchData" :key="branch.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ branch.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ branch.total_customers }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ branch.this_month }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ branch.last_7_days }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="downloadReport(branch.id)"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-150 inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.chart-container {
    position: relative;
    height: 300px;
}

@media (max-width: 640px) {
    .chart-container {
        height: 200px;
    }
}

/* Add smooth transitions for dark mode */
.dark-mode-transition {
    transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
}
</style>
