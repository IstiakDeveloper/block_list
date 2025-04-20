<template>

    <Head title="Voluntary Savings" />

    <AdminLayout>
        <div class="container px-4 py-8 mx-auto">
            <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <!-- Header and Filters -->
                <div
                    class="flex flex-col items-start justify-between p-4 space-y-4 sm:p-6 sm:flex-row sm:items-center sm:space-y-0">
                    <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl dark:text-gray-300">Voluntary Savings</h1>

                    <div
                        class="flex flex-col items-start w-full space-y-4 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4 sm:w-auto">
                        <!-- Search Input -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" v-model="search" placeholder="Search by member name, code..."
                                @input="handleSearchInput"
                                class="w-full px-4 py-2 pl-10 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500" />
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Add New Application Button -->
                        <Link href="/branch/voluntary-savings/create"
                            class="flex items-center justify-center w-full btn-primary sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
                        </svg>
                        Add New Application
                        </Link>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    ID
                                </th>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Member & Somiti
                                </th>
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase md:table-cell dark:text-gray-300">
                                    Application Date
                                </th>
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase lg:table-cell dark:text-gray-300">
                                    Total Deposits
                                </th>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Status
                                </th>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr v-for="saving in voluntarySavings.data" :key="saving.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                    {{ saving.id }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ saving.member_name }} ({{ saving.member_code }})
                                        </span>
                                        <span class="mt-1 text-xs text-gray-500">
                                            {{ saving.somiti_name }} ({{ saving.somiti_code }})
                                        </span>
                                    </div>
                                </td>
                                <td
                                    class="hidden p-4 text-sm text-gray-900 md:table-cell whitespace-nowrap dark:text-gray-100">
                                    {{ formatDate(saving.application_date) }}
                                </td>
                                <td
                                    class="hidden p-4 text-sm text-gray-900 lg:table-cell whitespace-nowrap dark:text-gray-100">
                                    ৳{{ calculateTotalDeposits(saving.deposits) }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <span :class="getStatusBadgeClass(saving.status)"
                                        class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ capitalizeFirstLetter(saving.status) }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div
                                        class="flex flex-wrap items-center justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                                        <Link :href="`/branch/voluntary-savings/${saving.id}`"
                                            class="w-full btn-action btn-show sm:w-auto">
                                        <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>View</span>
                                        </Link>

                                        <Link v-if="saving.status === 'pending'"
                                            :href="`/branch/voluntary-savings/${saving.id}/edit`"
                                            class="w-full btn-action btn-edit sm:w-auto">
                                        <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        <span>Edit</span>
                                        </Link>

                                        <a v-if="saving.status !== 'pending'"
                                            :href="`/branch/voluntary-savings/${saving.id}/pdf`"
                                            class="w-full btn-action btn-pdf sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>PDF</span>
                                        </a>

                                        <button v-if="saving.status === 'pending'" @click="confirmDelete(saving.id)"
                                            class="w-full btn-action btn-delete sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                            <!-- No applications found message -->
                            <tr v-if="voluntarySavings.data.length === 0">
                                <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                    <div class="py-6">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 text-lg font-medium">No applications found</p>
                                        <p class="mt-1">Create a new voluntary saving application</p>
                                        <Link href="/branch/voluntary-savings/create"
                                            class="inline-block mt-4 btn-primary">
                                        Add New Application
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :data="voluntarySavings" item-name="applications" :extra-params="paginationParams" />
            </div>
        </div>

        <ConfirmationDialog :show="isDialogVisible" @update:show="isDialogVisible = $event" @confirm="deleteApplication"
            title="Delete Voluntary Saving Application"
            message="Are you sure you want to delete this application? This action cannot be undone."
            confirm-button-text="Delete" cancel-button-text="Cancel" />
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, computed } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    voluntarySavings: Object,
    filters: {
        type: Object,
        default: () => ({})
    }
});

// State variables
const isDialogVisible = ref(false);
const applicationToDelete = ref(null);
const search = ref(props.filters.search || '');
const form = useForm({});

// Computed
const paginationParams = computed(() => ({
    search: search.value
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

const capitalizeFirstLetter = (string) => {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
        case 'approved':
            return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
        case 'rejected':
            return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100';
    }
};

const calculateTotalDeposits = (deposits) => {
    if (!deposits || !Array.isArray(deposits)) return 0;
    return deposits.reduce((total, deposit) => total + parseFloat(deposit.deposit_amount), 0).toFixed(2);
};

// Create debounced search function
const handleSearchInput = debounce(() => {
    applyFilters();
}, 300);

function applyFilters() {
    const params = new URLSearchParams();

    if (search.value) {
        params.set('search', search.value);
    }

    router.get(`/branch/voluntary-savings?${params.toString()}`, {}, {
        preserveState: true,
        preserveScroll: true,
    });
}

function confirmDelete(id) {
    applicationToDelete.value = id;
    isDialogVisible.value = true;
}

function deleteApplication() {
    if (applicationToDelete.value) {
        form.delete(`/branch/voluntary-savings/${applicationToDelete.value}`, {
            onSuccess: () => {
                isDialogVisible.value = false;
            },
        });
    }
}
</script>

<style scoped>
.btn-primary {
    @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700 transition duration-200;
}

.btn-secondary {
    @apply bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded shadow hover:bg-gray-400 transition duration-200;
}

.btn-action {
    @apply inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded transition duration-200;
}

.btn-show {
    @apply text-green-600 bg-green-100 hover:bg-green-200 dark:bg-green-600 dark:text-white dark:hover:bg-green-500;
}

.btn-edit {
    @apply text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500;
}

.btn-delete {
    @apply text-red-600 bg-red-100 hover:bg-red-200 dark:bg-red-600 dark:text-white dark:hover:bg-red-500;
}

.btn-pdf {
    @apply text-purple-600 bg-purple-100 hover:bg-purple-200 dark:bg-purple-600 dark:text-white dark:hover:bg-purple-500;
}
</style>
