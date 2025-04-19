<template>
    <Head title="View Voluntary Saving" />

    <AdminLayout>
        <div class="container px-4 py-8 mx-auto">
            <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <!-- Header and Action Buttons -->
                <div class="flex flex-col items-start justify-between p-4 space-y-4 border-b sm:p-6 sm:flex-row sm:items-center sm:space-y-0 dark:border-gray-700">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl dark:text-gray-300">
                            Voluntary Saving Details
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Viewing application #{{ voluntarySaving.id }}
                        </p>
                    </div>

                    <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                        <Link v-if="voluntarySaving.status === 'pending'" :href="route('branch.voluntary-savings.edit', voluntarySaving.id)" class="btn-action btn-edit">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Application
                        </Link>

                        <button @click="downloadPdf" class="btn-action btn-secondary">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </button>

                        <button v-if="voluntarySaving.status === 'pending'" @click="confirmDelete" class="btn-action btn-delete">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Application Status Bar -->
                <div :class="`px-6 py-3 font-medium text-white ${statusColorClass}`">
                    Status: {{ formatStatus(voluntarySaving.status) }}
                </div>

                <!-- Application Details -->
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="p-4 border rounded-md dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Member Information</h3>
                                <div class="grid grid-cols-1 gap-3 mt-3">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Member Name</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.member_name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Member Code</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.member_code }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Member Mobile</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.member_mobile || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Middle Column -->
                        <div class="space-y-4">
                            <div class="p-4 border rounded-md dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Somiti Information</h3>
                                <div class="grid grid-cols-1 gap-3 mt-3">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Somiti Name</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.somiti_name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Somiti Code</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.somiti_code }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Branch</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.branch.branch_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="p-4 border rounded-md dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Application Details</h3>
                                <div class="grid grid-cols-1 gap-3 mt-3">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Application Date</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ formatDate(voluntarySaving.application_date) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Profit Rate</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.profit ? `${voluntarySaving.profit}%` : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Created On</label>
                                        <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ formatDate(voluntarySaving.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Applicant Section -->
                    <div class="p-4 mt-4 border rounded-md dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Applicant Information</h3>
                        <div class="grid grid-cols-1 gap-4 mt-3 md:grid-cols-3">
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Applicant Name</label>
                                <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.applicant_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Designation</label>
                                <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.designation || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">PIN</label>
                                <p class="mt-1 text-sm text-gray-800 dark:text-gray-300">{{ voluntarySaving.pin || 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Signature section -->
                        <div v-if="voluntarySaving.signature" class="mt-3">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Signature</label>
                            <div class="mt-1">
                                <img :src="`/storage/${voluntarySaving.signature}`" alt="Applicant Signature" class="object-contain h-16 border border-gray-200 rounded-md dark:border-gray-700">
                            </div>
                        </div>
                    </div>

                    <!-- Deposits Table -->
                    <div class="p-4 mt-4 border rounded-md dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">Deposits</h3>
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            No.
                                        </th>
                                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                            Deposit Date
                                        </th>
                                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                            Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="(deposit, index) in voluntarySaving.deposits" :key="deposit.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                            {{ formatDate(deposit.deposit_date) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900 whitespace-nowrap dark:text-gray-100">
                                            {{ formatCurrency(deposit.deposit_amount) }}
                                        </td>
                                    </tr>
                                    <!-- Total row -->
                                    <tr class="font-semibold bg-gray-50 dark:bg-gray-700">
                                        <td colspan="2" class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                            Total Amount
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">
                                            {{ formatCurrency(totalDepositAmount) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="flex mt-6">
                <Link :href="route('branch.voluntary-savings.index')" class="btn-secondary">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </Link>
            </div>
        </div>

        <ConfirmationDialog
            :show="isDialogVisible"
            @update:show="isDialogVisible = $event"
            @confirm="deleteVoluntarySaving"
            title="Delete Voluntary Saving"
            message="Are you sure you want to delete this voluntary saving application? This action cannot be undone."
        />
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    voluntarySaving: Object,
});

const isDialogVisible = ref(false);

// Computed properties
const statusColorClass = computed(() => {
    switch(props.voluntarySaving.status) {
        case 'pending':
            return 'bg-yellow-600';
        case 'approved':
            return 'bg-green-600';
        case 'rejected':
            return 'bg-red-600';
        default:
            return 'bg-gray-600';
    }
});

const totalDepositAmount = computed(() => {
    return props.voluntarySaving.deposits.reduce((total, deposit) => total + parseFloat(deposit.deposit_amount), 0);
});

// Methods
const formatDate = (date) => {
    if (!date) return 'N/A';
    const dateObj = new Date(date);
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    return `${day}/${month}/${year}`;
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'BDT',
        minimumFractionDigits: 2
    }).format(amount);
};

const formatStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const downloadPdf = () => {
    window.location.href = route('branch.voluntary-savings.pdf', props.voluntarySaving.id);
};

const confirmDelete = () => {
    isDialogVisible.value = true;
};

const deleteVoluntarySaving = () => {
    router.delete(route('branch.voluntary-savings.destroy', props.voluntarySaving.id), {
        onSuccess: () => {
            isDialogVisible.value = false;
        },
    });
};
</script>

<style scoped>
.btn-primary {
    @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700 transition duration-200;
}

.btn-secondary {
    @apply inline-flex items-center bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded shadow hover:bg-gray-400 transition duration-200;
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
</style>
