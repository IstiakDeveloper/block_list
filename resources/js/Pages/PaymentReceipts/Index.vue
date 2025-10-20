<template>
    <AdminLayout title="Payment Receipts - Branch">
        <div class="py-6 dark:bg-gray-900">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">
                        <h2 class="flex items-center gap-2 text-xl font-semibold text-gray-800 dark:text-gray-200">
                            <LayoutDashboard class="w-6 h-6 text-blue-600" />
                            Receipt Management
                        </h2>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <!-- Distribute Button -->
                            <button @click="showDistributeModal = true"
                                class="inline-flex items-center gap-2 px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <UserPlus class="w-5 h-5" />
                                Distribute to Person
                            </button>

                            <!-- Generate Report Button -->
                            <!-- <button @click="openReportModal"
                                class="inline-flex items-center gap-2 px-4 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <FileText class="w-5 h-5" />
                                Generate Report
                            </button> -->
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <Label for="start_date" value="Start Date" class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="filters.start_date" placeholder="dd/mm/yyyy"
                                class="block w-full mt-1" @update:modelValue="handleFilterChange" />
                        </div>

                        <div>
                            <Label for="end_date" value="End Date" class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="filters.end_date" placeholder="dd/mm/yyyy" class="block w-full mt-1"
                                @update:modelValue="handleFilterChange" />
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Period Received</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ formatNumber(branchSummaries.period_received) }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full dark:bg-green-900/30">
                                <TrendingUp class="w-6 h-6 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Period Distributed</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ formatNumber(branchSummaries.period_distributed) }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full dark:bg-blue-900/30">
                                <TrendingDown class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Currently Available</p>
                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ formatNumber(branchSummaries.current_available) }}
                                </p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-full dark:bg-indigo-900/30">
                                <Package class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Received</p>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ formatNumber(branchSummaries.all_time_received) }}
                                </p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-full dark:bg-purple-900/30">
                                <BarChart3 class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Books by Lot -->
                <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                        Available Stock by Lot
                    </h3>

                    <div v-if="availableBooks && availableBooks.length > 0" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="lot in availableBooks" :key="lot.lot_id"
                            class="p-4 border border-gray-200 rounded-lg dark:border-gray-600">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ lot.lot_number }}</h4>
                                    <p v-if="lot.lot_name" class="text-sm text-gray-500 dark:text-gray-400">{{ lot.lot_name }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ lot.total_books }} books
                                </span>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Book Range:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ lot.book_range }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Receipt Range:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ lot.receipt_range }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">Total Receipts:</span>
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ formatNumber(lot.total_receipts) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-8 text-center text-gray-500 dark:text-gray-400">
                        <Package class="w-16 h-16 mx-auto mb-3 opacity-50" />
                        <p>No stock available</p>
                    </div>
                </div>

                <!-- Recent Transactions Table -->
                <div class="overflow-hidden bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                            Recent Transactions
                        </h3>

                        <!-- Flash Messages -->
                        <div v-if="$page.props.flash.success"
                            class="flex items-center gap-2 p-4 mb-4 text-green-700 border border-green-400 rounded-lg bg-green-50 dark:bg-green-900/30 dark:border-green-600 dark:text-green-300">
                            <CheckCircle class="w-5 h-5" />
                            {{ $page.props.flash.success }}
                        </div>

                        <div v-if="$page.props.flash.error"
                            class="flex items-center gap-2 p-4 mb-4 text-red-700 border border-red-400 rounded-lg bg-red-50 dark:bg-red-900/30 dark:border-red-600 dark:text-red-300">
                            <AlertCircle class="w-5 h-5" />
                            {{ $page.props.flash.error }}
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Lot
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Books
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Receipts
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Person
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="transaction in receipts.data" :key="transaction.id"
                                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-300">
                                            {{ formatDate(transaction.transaction_date) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-300">
                                            {{ transaction.lot?.lot_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full"
                                                :class="getTransactionTypeClass(transaction.transaction_type)">
                                                {{ getTransactionTypeLabel(transaction.transaction_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-300">
                                            {{ transaction.book_from }} - {{ transaction.book_to }}
                                            <span class="text-xs text-gray-500">({{ transaction.total_books }})</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-300">
                                            {{ formatNumber(transaction.total_receipts) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-300">
                                            {{ transaction.given_to || transaction.received_by || '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">

                                            <div class="flex justify-end gap-2">
                                                <button @click="viewTransactionDetails(transaction.id)"
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    <Eye class="w-5 h-5" />
                                                </button>

                                                <!-- <button v-if="transaction.transaction_type === 'distribute_to_person'"
                                                    @click="confirmDelete(transaction.id)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    <Trash2 class="w-5 h-5" />
                                                </button> -->
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4" v-if="receipts.links">
                            <Pagination :links="receipts.links" :data="receipts" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <DistributeToPersonModal :show="showDistributeModal" :available-books="availableBooks"
            @close="showDistributeModal = false" @success="handleFilterChange" />

        <TransactionDetailsModal :show="showDetailsModal" :transaction="selectedTransaction"
            @close="showDetailsModal = false"
            @edit="handleEditTransaction"
            @delete="handleDeleteFromDetails" />

        <ReportModal :show="showReportModal" :branches="[]" :filters="filters" @close="showReportModal = false" />

        <DeleteConfirmModal :show="showDeleteModal" :deleting="deleting" @close="showDeleteModal = false"
            @confirm="deleteTransaction" />
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Label from '@/Components/Label.vue';
import CustomDateInput from '@/Components/CustomDateInput.vue';
import Pagination from '@/Components/Pagination.vue';

import DistributeToPersonModal from './Modals/DistributeToPersonModal.vue';
import TransactionDetailsModal from './Modals/TransactionDetailsModal.vue';
import ReportModal from './Modals/ReportModal.vue';
import DeleteConfirmModal from './Modals/DeleteConfirmModal.vue';

import {
    LayoutDashboard,
    Package,
    UserPlus,
    FileText,
    Eye,
    Trash2,
    CheckCircle,
    AlertCircle,
    TrendingUp,
    TrendingDown,
    BarChart3
} from 'lucide-vue-next';

const props = defineProps({
    receipts: Object,
    branchSummaries: Object,
    availableBooks: Array,
    filters: Object
});

// State
const showDistributeModal = ref(false);
const showDetailsModal = ref(false);
const showReportModal = ref(false);
const showDeleteModal = ref(false);
const selectedTransaction = ref(null);
const selectedTransactionId = ref(null);
const deleting = ref(false);

const filters = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || ''
});

// Methods
const formatNumber = (number) => {
    if (number === 0) return '0';
    if (!number) return '-';
    return number.toLocaleString('en-US', { maximumFractionDigits: 0 });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const getTransactionTypeLabel = (type) => {
    const labels = {
        'distribute_to_branch': 'Received',
        'distribute_to_person': 'Distributed'
    };
    return labels[type] || type;
};

const getTransactionTypeClass = (type) => {
    const classes = {
        'distribute_to_branch': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'distribute_to_person': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
    };
    return classes[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
};

const handleFilterChange = debounce(() => {
    router.get(route('payment-receipts.index'), {
        start_date: filters.value.start_date,
        end_date: filters.value.end_date
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['receipts', 'branchSummaries', 'availableBooks']
    });
}, 300);

const viewTransactionDetails = async (transactionId) => {
    try {
        const response = await fetch(route('payment-receipts.transaction-details', { transaction: transactionId }));
        const data = await response.json();

        if (data.success) {
            selectedTransaction.value = data.transaction;
            showDetailsModal.value = true;
        }
    } catch (error) {
        console.error('Error fetching transaction details:', error);
    }
};

const handleEditTransaction = (transaction) => {
    showDetailsModal.value = false;
    alert(`Edit transaction #${transaction.id}\nGiven To: ${transaction.given_to}\nPIN: ${transaction.pin_number || 'N/A'}`);
    // TODO: Implement edit modal
};

const handleDeleteFromDetails = (transactionId) => {
    showDetailsModal.value = false;
    selectedTransactionId.value = transactionId;
    showDeleteModal.value = true;
};

const confirmDelete = (transactionId) => {
    selectedTransactionId.value = transactionId;
    showDeleteModal.value = true;
};

const deleteTransaction = () => {
    if (!selectedTransactionId.value) return;

    deleting.value = true;

    router.delete(route('payment-receipts.destroy', { receipt: selectedTransactionId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedTransactionId.value = null;
            handleFilterChange();
        },
        onError: (errors) => {
            console.error('Error deleting transaction:', errors);
        },
        onFinish: () => {
            deleting.value = false;
        }
    });
};

const openReportModal = () => {
    showReportModal.value = true;
};
</script>
