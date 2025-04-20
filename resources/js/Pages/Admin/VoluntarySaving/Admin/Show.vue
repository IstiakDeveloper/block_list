<template>

    <Head title="View Voluntary Saving" />

    <AdminLayout>
        <div class="container max-w-4xl px-4 py-8 mx-auto">
            <div class="mb-6">
                <Link :href="route('admin.voluntary-savings.index')"
                    class="flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Voluntary Savings
                </Link>
            </div>

            <!-- Unified Application Form (A4 style) -->
            <div class="bg-white rounded-lg shadow-lg dark:bg-gray-800 min-h-[1123px] w-full relative">
                <!-- Header with logo placeholder and title -->
                <div class="flex items-center justify-between p-6 border-b-2 border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-16 h-16 mr-4 rounded dark:bg-gray-700">
                            <img src="/logo.png" alt="Logo" class="object-contain w-14 h-14" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-300">Voluntary Saving Withdraw
                                Application</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Reference #{{ voluntarySaving.id }}</p>
                        </div>
                    </div>

                    <div>
                        <span :class="{
                            'px-3 py-1 text-sm font-medium rounded-full': true,
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': voluntarySaving.status === 'pending',
                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': voluntarySaving.status === 'approved',
                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': voluntarySaving.status === 'rejected'
                        }">
                            {{ capitalizeStatus(voluntarySaving.status) }}
                        </span>
                    </div>
                </div>

                <!-- Main application content -->
                <div class="p-6">
                    <!-- Application information section -->
                    <div class="mb-6">
                        <h2 class="pb-2 mb-4 text-lg font-semibold text-gray-800 border-b dark:text-gray-300">1.
                            Application Details</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Application
                                    Date:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{
                                    formatDate(voluntarySaving.application_date) }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Branch:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{
                                    voluntarySaving.branch.branch_name }}</span>
                            </div>

                        </div>
                    </div>

                    <!-- Somiti & Member information section -->
                    <div class="mb-6">
                        <h2 class="pb-2 mb-4 text-lg font-semibold text-gray-800 border-b dark:text-gray-300">2. Member
                            Information</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Somiti Name:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{ voluntarySaving.somiti_name
                                    }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Somiti Code:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{ voluntarySaving.somiti_code
                                    }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Name:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{ voluntarySaving.member_name
                                    }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Code:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{ voluntarySaving.member_code
                                    }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Mobile:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{ voluntarySaving.member_mobile
                                    || 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Applicant information section -->
                    <div class="mb-6">
                        <h2 class="pb-2 mb-4 text-lg font-semibold text-gray-800 border-b dark:text-gray-300">3.
                            Applicant Information</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Applicant
                                    Name:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{ voluntarySaving.applicant_name
                                    }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Designation:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{ voluntarySaving.designation ||
                                    'N/A' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">PIN:</span>
                                <span class="text-sm text-gray-900 dark:text-gray-200">{{ voluntarySaving.pin || 'N/A'
                                    }}</span>
                            </div>
                            <div class="flex flex-col" v-if="voluntarySaving.signature">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Signature:</span>
                                <img :src="`/storage/${voluntarySaving.signature}`" alt="Signature"
                                    class="h-10 p-1 mt-1 border border-gray-200 dark:border-gray-700" />
                            </div>
                        </div>
                    </div>

                    <!-- Deposits section -->
                    <div class="mb-6">
                        <h2 class="pb-2 mb-4 text-lg font-semibold text-gray-800 border-b dark:text-gray-300">4. Deposit
                            History</h2>
                        <div class="overflow-x-auto border border-gray-200 rounded dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-600 uppercase dark:text-gray-300">
                                            Deposit Date
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-600 uppercase dark:text-gray-300">
                                            Account Name
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-2 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                                            Amount
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-2 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                                            Profit
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-2 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                                            total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                    <tr v-for="deposit in voluntarySaving.deposits" :key="deposit.id">
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">
                                            {{ formatDate(deposit.deposit_date) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">
                                            {{ deposit.account_name }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-200">
                                            {{ formatCurrency(deposit.deposit_amount) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-200">
                                            {{ formatCurrency(deposit.profit) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-200">
                                            {{ formatCurrency((parseFloat(deposit.deposit_amount) || 0) +
                                                (parseFloat(deposit.profit) || 0)) }}
                                        </td>
                                    </tr>
                                    <tr class="font-bold bg-gray-50 dark:bg-gray-800">
                                        <td class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-200" colspan="4">
                                            Total
                                        </td>
                                        <td
                                            class="px-4 py-2 text-sm font-semibold text-right text-gray-900 dark:text-gray-200">
                                            {{ formatCurrency(totalDepositAmount) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes or additional information section -->
                    <!-- <div class="mb-6">
                        <h2 class="pb-2 mb-4 text-lg font-semibold text-gray-800 border-b dark:text-gray-300">5. Notes &
                            Additional Information</h2>
                        <p class="text-sm italic text-gray-600 dark:text-gray-400">No additional notes provided for this
                            application.</p>
                    </div> -->

                    <!-- Signature and verification section -->
                    <!-- <div class="grid grid-cols-3 gap-4 pt-6 mt-10 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-px mb-2 border-b border-gray-400 dark:border-gray-600"></div>
                            <span class="text-xs text-gray-600 dark:text-gray-400">Applicant Signature</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-px mb-2 border-b border-gray-400 dark:border-gray-600"></div>
                            <span class="text-xs text-gray-600 dark:text-gray-400">Branch Manager</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-px mb-2 border-b border-gray-400 dark:border-gray-600"></div>
                            <span class="text-xs text-gray-600 dark:text-gray-400">Authorized Signature</span>
                        </div>
                    </div> -->
                </div>

                <!-- Document footer with action buttons -->
                <div class="p-6 mt-auto border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col items-center justify-between sm:flex-row">
                        <div class="flex mb-4 space-x-2 sm:mb-0">
                            <a :href="`/admin/voluntary-savings/${voluntarySaving.id}/pdf`"
                                class="flex items-center px-3 py-1 text-sm text-white bg-blue-600 rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                        clip-rule="evenodd" />
                                </svg>
                                Download PDF
                            </a>

                            <a v-if="voluntarySaving.status === 'approved'"
                                :href="`/admin/voluntary-savings/${voluntarySaving.id}/approval-pdf`"
                                class="flex items-center px-3 py-1 text-sm text-white bg-green-600 rounded hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z"
                                        clip-rule="evenodd" />
                                </svg>
                                Approval Letter
                            </a>
                        </div>

                        <div class="flex space-x-3" v-if="voluntarySaving.status === 'pending'">
                            <button @click="rejectApplication"
                                class="flex items-center px-4 py-2 text-sm text-white bg-red-600 rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                Reject
                            </button>
                            <button @click="approveApplication"
                                class="flex items-center px-4 py-2 text-sm text-white bg-green-600 rounded hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Approve
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationDialog :show="isDialogVisible" :title="dialogTitle" :message="dialogMessage"
            @update:show="isDialogVisible = $event" @confirm="confirmAction" />
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

// State variables
const isDialogVisible = ref(false);
const dialogTitle = ref('');
const dialogMessage = ref('');
const actionType = ref('');

// Computed properties
const totalDepositAmount = computed(() => {
    return props.voluntarySaving.deposits.reduce((total, deposit) => {
        const depositAmount = parseFloat(deposit.deposit_amount) || 0;
        const profit = parseFloat(deposit.profit) || 0;
        return total + depositAmount + profit;
    }, 0);
});

// Methods
const formatDate = (date) => {
    if (!date) return '';
    const dateObj = new Date(date);
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    return `${day}/${month}/${year}`;
};

const formatCurrency = (amount) => {
    const formatter = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    return `৳${formatter.format(amount)}`;
};

const capitalizeStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

function approveApplication() {
    dialogTitle.value = 'Confirm Approval';
    dialogMessage.value = 'Are you sure you want to approve this voluntary saving application?';
    actionType.value = 'approve';
    isDialogVisible.value = true;
}

function rejectApplication() {
    dialogTitle.value = 'Confirm Rejection';
    dialogMessage.value = 'Are you sure you want to reject this voluntary saving application?';
    actionType.value = 'reject';
    isDialogVisible.value = true;
}

function confirmAction() {
    if (actionType.value === 'approve') {
        router.put(route('admin.voluntary-savings.approve', props.voluntarySaving.id), {}, {
            onSuccess: () => {
                isDialogVisible.value = false;
            },
        });
    } else if (actionType.value === 'reject') {
        router.put(route('admin.voluntary-savings.reject', props.voluntarySaving.id), {}, {
            onSuccess: () => {
                isDialogVisible.value = false;
            },
        });
    }
}
</script>

<style scoped>
/* A4 paper styling */
.max-w-4xl {
    max-width: 210mm;
    /* Standard A4 width */
}

/* Print media queries for proper A4 rendering */
@media print {
    body {
        margin: 0;
        padding: 0;
    }

    .container {
        width: 210mm;
        height: 297mm;
        padding: 0;
    }

    .shadow-lg {
        box-shadow: none !important;
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .grid-cols-3 {
        grid-template-columns: 1fr;
    }
}
</style>
