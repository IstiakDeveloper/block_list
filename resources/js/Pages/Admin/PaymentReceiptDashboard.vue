<template>
    <AdminLayout>
        <Head title="Payment Receipt Dashboard" />

        <div class="min-h-full bg-gray-50 dark:bg-gray-900">
            <!-- Header Section -->
            <header class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                Payment Receipt Dashboard
                            </h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Track and manage receipt distribution across branches
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-3">
                            <Link :href="links.receiptStocks"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                <ChartBarIcon class="-ml-0.5 mr-2 h-4 w-4" />
                                Stock Overview
                            </Link>
                            <Link :href="links.branchOfficers"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                <UserGroupIcon class="-ml-0.5 mr-2 h-4 w-4" />
                                Officers
                            </Link>
                            <Link :href="links.createDistribution"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-400">
                                <DocumentArrowDownIcon class="-ml-0.5 mr-2 h-4 w-4" />
                                New Distribution
                            </Link>
                        </div>
                    </div>

                    <!-- Date Filter -->
                    <div class="mt-6 flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date Range</label>
                            <div class="flex items-center gap-2">
                                <input
                                    v-model="filters.startDate"
                                    type="date"
                                    class="block rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 sm:text-sm"
                                />
                                <span class="text-gray-500 dark:text-gray-400">to</span>
                                <input
                                    v-model="filters.endDate"
                                    type="date"
                                    class="block rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 sm:text-sm"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="pb-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Stats Grid -->
                    <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="stat in statsData" :key="stat.name"
                            class="relative overflow-hidden rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <dt>
                                <div :class="[stat.iconBackground, 'absolute rounded-md p-3']">
                                    <component :is="stat.icon" class="h-6 w-6 text-white" aria-hidden="true" />
                                </div>
                                <p class="ml-16 truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ stat.name }}
                                </p>
                            </dt>
                            <dd class="ml-16 flex items-baseline">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ stat.value }}
                                </p>
                                <p v-if="stat.change !== null"
                                    :class="[stat.changeType === 'increase' ? 'text-green-600' : 'text-red-600',
                                        'ml-2 flex items-baseline text-sm font-semibold']">
                                    <ArrowUpIcon v-if="stat.changeType === 'increase'"
                                        class="h-5 w-5 flex-shrink-0 self-center text-green-500" aria-hidden="true" />
                                    <ArrowDownIcon v-else class="h-5 w-5 flex-shrink-0 self-center text-red-500"
                                        aria-hidden="true" />
                                    <span class="sr-only">
                                        {{ stat.changeType === 'increase' ? 'Increased' : 'Decreased' }} by
                                    </span>
                                    {{ Math.abs(stat.change) }}%
                                </p>
                            </dd>
                        </div>
                    </div>

                    <!-- Branch Overview -->
                    <div class="mt-8">
                        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="p-6">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
                                    Branch Overview
                                </h3>
                                <div class="mt-6 flow-root">
                                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                        <div class="inline-block min-w-full py-2 align-middle">
                                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                                <thead>
                                                    <tr>
                                                        <th scope="col"
                                                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6 lg:pl-8">
                                                            Branch Name</th>
                                                        <th scope="col"
                                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                            Available Receipts</th>
                                                        <th scope="col"
                                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                            Used Receipts</th>
                                                        <th scope="col"
                                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                            Total Receipts</th>
                                                        <th scope="col"
                                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                            Usage %</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    <tr v-for="branch in branches" :key="branch.id">
                                                        <td
                                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6 lg:pl-8">
                                                            {{ branch.branch_name }}
                                                        </td>
                                                        <td
                                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ branch.receipt_stock?.available_receipts ?? 0 }}
                                                        </td>
                                                        <td
                                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ branch.receipt_stock?.used_receipts ?? 0 }}
                                                        </td>
                                                        <td
                                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ branch.receipt_stock?.total_receipts ?? 0 }}
                                                        </td>
                                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                            <div class="flex items-center">
                                                                <span
                                                                    :class="[getUsageColor(getUsagePercentage(branch)), 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium']">
                                                                    {{ getUsagePercentage(branch) }}%
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <!-- Recent Transfers -->
                        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="p-6">
                                <h3 class="flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                    <ArrowsRightLeftIcon class="mr-2 h-5 w-5 text-gray-400" />
                                    Recent Transfers
                                </h3>
                                <div class="mt-6 flow-root">
                                    <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                                        <li v-for="transfer in recentActivities.transfers" :key="transfer.id"
                                            class="py-5">
                                            <div class="relative">
                                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ transfer.from }} → {{ transfer.to }}
                                                </h4>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ transfer.quantity }} receipts transferred by {{ transfer.by }}
                                                </p>
                                                <time
                                                    class="mt-1 text-xs text-gray-500">{{ formatDateTime(transfer.date, transfer.time) }}</time>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Distributions -->
                        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="p-6">
                                <h3 class="flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                    <DocumentArrowDownIcon class="mr-2 h-5 w-5 text-gray-400" />
                                    Recent Distributions
                                </h3>
                                <div class="mt-6 flow-root">
                                    <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                                        <li v-for="distribution in recentActivities.distributions"
                                            :key="distribution.id" class="py-5">
                                            <div class="relative">
                                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ distribution.branch }}
                                                </h4>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ distribution.quantity }} receipts distributed to
                                                    {{ distribution.officer.name }}
                                                    <span class="text-xs text-gray-500">(PIN:
                                                        {{ distribution.officer.pin }})</span>
                                                </p>
                                                <time
                                                    class="mt-1 text-xs text-gray-500">{{ formatDateTime(distribution.date, distribution.time) }}</time>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </AdminLayout>
</template>
<script setup>
import { ref, computed, watchEffect } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ChartBarIcon,
    UserGroupIcon,
    DocumentArrowDownIcon,
    ArrowsRightLeftIcon,
    ArrowUpIcon,
    ArrowDownIcon,
} from '@heroicons/vue/24/outline';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Props definition with validation
const props = defineProps({
    statistics: {
        type: Object,
        required: true,
        default: () => ({
            branches: { total: 0, active: 0 },
            activeOfficers: 0,
            receipts: {
                total: 0,
                used: 0,
                available: 0,
                changes: { total: null, used: null, available: null }
            }
        })
    },
    recentActivities: {
        type: Object,
        required: true,
        default: () => ({ transfers: [], distributions: [] })
    },
    branches: {
        type: Array,
        required: true,
        default: () => []
    },
    filters: {
        type: Object,
        required: true,
        default: () => ({
            startDate: null,
            endDate: null
        })
    },
    links: {
        type: Object,
        required: true
    },
    userBranch: {
        type: Number,
        required: false,
        default: null
    }
});

// Reactive state
const filters = ref({
    startDate: props.filters.startDate,
    endDate: props.filters.endDate
});

// Computed statistics for the dashboard
const statsData = computed(() => [
    {
        name: 'Available Receipts',
        value: props.statistics.receipts.available,
        change: props.statistics.receipts.changes.available,
        icon: DocumentArrowDownIcon,
        iconBackground: 'bg-green-500',
        changeType: props.statistics.receipts.changes.available >= 0 ? 'increase' : 'decrease'
    },
    {
        name: 'Active Officers',
        value: props.statistics.activeOfficers,
        change: null,
        icon: UserGroupIcon,
        iconBackground: 'bg-blue-500'
    },
    {
        name: 'Used Receipts',
        value: props.statistics.receipts.used,
        change: props.statistics.receipts.changes.used,
        icon: ChartBarIcon,
        iconBackground: 'bg-yellow-500',
        changeType: props.statistics.receipts.changes.used >= 0 ? 'increase' : 'decrease'
    }
]);

// Utility functions
const getUsagePercentage = (branch) => {
    const stock = branch.receipt_stock;
    if (!stock || !stock.total_receipts) return 0;
    return Math.round((stock.used_receipts / stock.total_receipts) * 100);
};

const getUsageColor = (percentage) => {
    if (percentage >= 90) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    if (percentage >= 70) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
    return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
};

const formatDateTime = (date, time) => {
    if (!date) return '';
    const formattedDate = new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
    return time ? `${formattedDate} at ${time}` : formattedDate;
};

// Date filter handling
watchEffect(() => {
    if (filters.value.startDate && filters.value.endDate) {
        router.get(
            route('admin.payment-receipt-dashboard'),
            {
                start_date: filters.value.startDate,
                end_date: filters.value.endDate
            },
            {
                preserveState: true,
                preserveScroll: true,
                only: ['statistics', 'recentActivities']
            }
        );
    }
});

// Branch accessibility check
const canAccessBranch = (branchId) => {
    return !props.userBranch || props.userBranch === branchId;
};

// Format large numbers
const formatNumber = (number) => {
    return new Intl.NumberFormat('en-US').format(number);
};

// Check if branch has low stock
const hasLowStock = computed(() => {
    return props.branches.some(branch => {
        const available = branch.receipt_stock?.available_receipts ?? 0;
        const total = branch.receipt_stock?.total_receipts ?? 0;
        return available < (total * 0.1); // Less than 10% available
    });
});

// Alert messages handling
const showLowStockAlert = computed(() => {
    return hasLowStock.value && canManageStock.value;
});

const canManageStock = computed(() => {
    // Add your permission logic here
    return true; // Placeholder
});

// Export named function for potential reuse
const exportData = () => {
    // Implement export functionality
    console.log('Export functionality to be implemented');
};
</script>

<style scoped>
/* Custom scrollbar for tables */
.table-container {
    @apply overflow-x-auto;
    scrollbar-width: thin;
    scrollbar-color: theme('colors.gray.400') theme('colors.gray.200');
}

.table-container::-webkit-scrollbar {
    @apply h-1.5 w-1.5;
}

.table-container::-webkit-scrollbar-track {
    @apply bg-gray-200 dark:bg-gray-700;
}

.table-container::-webkit-scrollbar-thumb {
    @apply rounded-full bg-gray-400 dark:bg-gray-600;
}

/* Dark mode support for date inputs */
.dark input[type="date"] {
    color-scheme: dark;
}

/* Smooth transitions */
.fade-transition {
    @apply transition-all duration-300;
}

/* Status badge animations */
.status-badge {
    @apply transition-colors duration-200;
}

/* Hover effects */
.hover-effect {
    @apply transition-transform duration-200 hover:scale-105;
}
</style>
