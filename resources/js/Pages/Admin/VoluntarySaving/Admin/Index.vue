<template>

    <Head title="Voluntary Savings" />

    <AdminLayout>
        <div class="container px-4 py-8 mx-auto">
            <div
                class="overflow-hidden bg-white border border-blue-100 rounded-lg shadow-xl dark:bg-gray-900 dark:border-blue-900">
                <!-- Header Section with Blue Gradient -->
                <div class="p-6 bg-gradient-to-r from-blue-600 to-blue-800">
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Voluntary Savings Withdraw</h1>
                    <p class="mt-1 text-blue-100">Manage voluntary savings withdraw applications and records</p>
                </div>

                <!-- Filters Section with Improved Styling -->
                <div class="p-4 border-b border-blue-100 bg-blue-50 dark:bg-gray-800 sm:p-6 dark:border-blue-900">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        <!-- Status Filter -->
                        <div>
                            <label for="status-filter"
                                class="block mb-1 text-sm font-medium text-blue-700 dark:text-blue-300">Status</label>
                            <select id="status-filter" v-model="statusFilter" @change="applyFilters"
                                class="w-full px-4 py-2 text-gray-700 bg-white border border-blue-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-blue-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Branch Filter -->
                        <div>
                            <label for="branch-filter"
                                class="block mb-1 text-sm font-medium text-blue-700 dark:text-blue-300">Branch</label>
                            <select id="branch-filter" v-model="branchFilter" @change="applyFilters"
                                class="w-full px-4 py-2 text-gray-700 bg-white border border-blue-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-blue-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Branches</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.branch_name }}
                                </option>
                            </select>
                        </div>

                        <!-- From Date Filter -->
                        <div>
                            <label for="from-date"
                                class="block mb-1 text-sm font-medium text-blue-700 dark:text-blue-300">From
                                Date</label>
                            <input id="from-date" type="date" v-model="fromDateFilter" @change="applyFilters"
                                class="w-full px-4 py-2 text-gray-700 bg-white border border-blue-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-blue-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>

                        <!-- To Date Filter -->
                        <div>
                            <label for="to-date"
                                class="block mb-1 text-sm font-medium text-blue-700 dark:text-blue-300">To Date</label>
                            <input id="to-date" type="date" v-model="toDateFilter" @change="applyFilters"
                                class="w-full px-4 py-2 text-gray-700 bg-white border border-blue-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-blue-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>

                        <!-- Generate Report Button -->
                        <div class="flex items-end">
                            <button @click="generateReport"
                                class="flex items-center justify-center w-full px-4 py-2 text-white transition duration-150 ease-in-out bg-blue-600 rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Section with Blue Accents -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-blue-200 dark:divide-blue-900">
                        <thead class="bg-blue-50 dark:bg-blue-900/30">
                            <tr>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-left text-blue-700 uppercase dark:text-blue-300">
                                    Application ID
                                </th>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-left text-blue-700 uppercase dark:text-blue-300">
                                    Date
                                </th>
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-blue-700 uppercase md:table-cell dark:text-blue-300">
                                    Branch
                                </th>
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-blue-700 uppercase md:table-cell dark:text-blue-300">
                                    Member
                                </th>
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-blue-700 uppercase lg:table-cell dark:text-blue-300">
                                    Status
                                </th>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-right text-blue-700 uppercase dark:text-blue-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-blue-100 dark:bg-gray-900 dark:divide-blue-900/30">
                            <tr v-for="saving in voluntarySavings.data" :key="saving.id"
                                class="transition duration-150 hover:bg-blue-50 dark:hover:bg-blue-900/10">
                                <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-blue-100">
                                    <span class="font-medium">{{ saving.id }}</span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-blue-100">
                                            {{ formatDate(saving.application_date) }}
                                        </span>
                                        <!-- Mobile-only info -->
                                        <span class="mt-1 text-xs text-blue-600 md:hidden dark:text-blue-400">
                                            Branch: {{ saving.branch.branch_name }}
                                        </span>
                                        <span class="mt-1 text-xs text-blue-600 md:hidden dark:text-blue-400">
                                            Member: {{ saving.member_name }}
                                        </span>
                                    </div>
                                </td>
                                <td
                                    class="hidden p-4 text-sm text-gray-900 md:table-cell whitespace-nowrap dark:text-blue-100">
                                    {{ saving.branch.branch_name }}
                                </td>
                                <td
                                    class="hidden p-4 text-sm text-gray-900 md:table-cell whitespace-nowrap dark:text-blue-100">
                                    {{ saving.member_name }}
                                </td>
                                <td class="hidden p-4 lg:table-cell whitespace-nowrap">
                                    <span :class="{
                                        'px-3 py-1 text-xs font-medium rounded-full shadow-sm': true,
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200': saving.status === 'pending',
                                        'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200': saving.status === 'approved',
                                        'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200': saving.status === 'rejected'
                                    }">
                                        {{ capitalizeStatus(saving.status) }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div
                                        class="flex flex-col items-end justify-end space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2">
                                        <Link :href="route('admin.voluntary-savings.show', saving.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>View</span>
                                        </Link>

                                        <button v-if="saving.status === 'pending'"
                                            @click="approveApplication(saving.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Approve</span>
                                        </button>

                                        <button v-if="saving.status === 'pending'" @click="rejectApplication(saving.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Reject</span>
                                        </button>

                                        <a :href="`/admin/voluntary-savings/${saving.id}/pdf`"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>PDF</span>
                                        </a>

                                        <a v-if="saving.status === 'approved'"
                                            :href="`/admin/voluntary-savings/${saving.id}/approval-pdf`"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Approval</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- No data found message with improved styling -->
                            <tr v-if="voluntarySavings.data.length === 0">
                                <td colspan="6" class="p-8 text-center">
                                    <div class="max-w-sm py-8 mx-auto">
                                        <div class="p-6 rounded-lg shadow-inner bg-blue-50 dark:bg-blue-900/20">
                                            <svg class="w-16 h-16 mx-auto text-blue-400 dark:text-blue-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="mt-4 text-lg font-medium text-blue-700 dark:text-blue-300">No
                                                voluntary savings withdraw request found</p>
                                            <p class="mt-2 text-blue-600 dark:text-blue-400">Try adjusting your filter
                                                criteria</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination with Blue Styling -->
                <div class="border-t border-blue-100 dark:border-blue-900">
                    <Pagination :data="voluntarySavings" item-name="voluntary savings"
                        :extra-params="paginationParams" />
                </div>
            </div>
        </div>

        <!-- Confirmation Dialog with Blue Styling -->
        <ConfirmationDialog :show="isDialogVisible" :title="dialogTitle" :message="dialogMessage"
            @update:show="isDialogVisible = $event" @confirm="confirmAction"
            confirm-button-class="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500"
            cancel-button-class="bg-gray-300 hover:bg-gray-400 focus:ring-gray-400" />
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    voluntarySavings: Object,
    branches: Array,
    filters: Object,
});

// State variables
const isDialogVisible = ref(false);
const dialogTitle = ref('');
const dialogMessage = ref('');
const actionType = ref('');
const selectedId = ref(null);

// Filter states
const statusFilter = ref(props.filters.status || '');
const branchFilter = ref(props.filters.branch_id || '');
const fromDateFilter = ref(props.filters.from_date || '');
const toDateFilter = ref(props.filters.to_date || '');

// Computed
const paginationParams = computed(() => ({
    status: statusFilter.value,
    branch_id: branchFilter.value,
    from_date: fromDateFilter.value,
    to_date: toDateFilter.value,
}));

// Methods
const formatDate = (date) => {
    if (!date) return '';
    const dateObj = new Date(date);
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    return `${day}/${month}/${year}`;
};

const capitalizeStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

function applyFilters() {
    const params = new URLSearchParams();

    if (statusFilter.value) {
        params.set('status', statusFilter.value);
    }

    if (branchFilter.value) {
        params.set('branch_id', branchFilter.value);
    }

    if (fromDateFilter.value) {
        params.set('from_date', fromDateFilter.value);
    }

    if (toDateFilter.value) {
        params.set('to_date', toDateFilter.value);
    }

    router.get(`/admin/voluntary-savings?${params.toString()}`, {}, {
        preserveState: true,
        preserveScroll: true,
    });
}

function generateReport() {
    const params = new URLSearchParams();

    if (statusFilter.value) {
        params.set('status', statusFilter.value);
    }

    if (branchFilter.value) {
        params.set('branch_id', branchFilter.value);
    }

    if (fromDateFilter.value) {
        params.set('from_date', fromDateFilter.value);
    }

    if (toDateFilter.value) {
        params.set('to_date', toDateFilter.value);
    }

    window.location.href = `/admin/voluntary-savings/report?${params.toString()}`;
}

function approveApplication(id) {
    dialogTitle.value = 'Confirm Approval';
    dialogMessage.value = 'Are you sure you want to approve this voluntary saving application?';
    actionType.value = 'approve';
    selectedId.value = id;
    isDialogVisible.value = true;
}

function rejectApplication(id) {
    dialogTitle.value = 'Confirm Rejection';
    dialogMessage.value = 'Are you sure you want to reject this voluntary saving application?';
    actionType.value = 'reject';
    selectedId.value = id;
    isDialogVisible.value = true;
}

function confirmAction() {
    const id = props.voluntarySaving?.id || selectedId.value;

    if (actionType.value === 'approve') {
        router.put(route('admin.voluntary-savings.approve', id), {}, {
            onSuccess: () => {
                isDialogVisible.value = false;
            },
        });
    } else if (actionType.value === 'reject') {
        router.put(route('admin.voluntary-savings.reject', id), {}, {
            onSuccess: () => {
                isDialogVisible.value = false;
            },
        });
    }
}

</script>

<style>
/* Add custom styles if needed */
.btn-primary {
    @apply px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-md transition duration-150 ease-in-out;
}
</style>
