<template>
    <AdminLayout title="Payment Receipts - Branch">
        <div class="py-8 min-h-screen bg-slate-50/50 dark:bg-slate-950/20 text-slate-800 dark:text-slate-100">
            <div class="mx-auto space-y-8 max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- Hero & Header Section -->
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Receipt Management
                        </h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Distribute receipt books to personnel, track your branch stock, and view transaction records.
                        </p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex flex-wrap items-center gap-3">
                        <Link
                            :href="route('payment-receipts.search')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all duration-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 active:scale-[0.98] shadow-sm"
                        >
                            <Search class="w-4.5 h-4.5" />
                            Search Receipt
                        </Link>
                        <!-- Distribute to Person Button -->
                        <button @click="showDistributeModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-blue-600 rounded-xl hover:bg-blue-700 active:scale-[0.98] shadow-sm hover:shadow shadow-blue-500/10">
                            <UserPlus class="w-4.5 h-4.5" />
                            Distribute to Person
                        </button>
                    </div>
                </div>

                <!-- Premium KPI Stats Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Period Received -->
                    <div class="relative overflow-hidden p-5 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Period Received</span>
                            <div class="p-2 bg-green-50 dark:bg-green-950/50 rounded-lg">
                                <TrendingUp class="w-5 h-5 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight text-green-600 dark:text-green-400">
                                {{ formatNumber(branchSummaries.period_received) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">receipts</span>
                        </div>
                        <div class="mt-2 text-xs text-slate-400 dark:text-slate-500">From Head Office (period)</div>
                    </div>

                    <!-- Period Distributed -->
                    <div class="relative overflow-hidden p-5 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Period Distributed</span>
                            <div class="p-2 bg-blue-50 dark:bg-blue-950/50 rounded-lg">
                                <TrendingDown class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight text-blue-600 dark:text-blue-400">
                                {{ formatNumber(branchSummaries.period_distributed) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">receipts</span>
                        </div>
                        <div class="mt-2 text-xs text-slate-400 dark:text-slate-500">Given to personnel (period)</div>
                    </div>

                    <!-- Currently Available -->
                    <div class="relative overflow-hidden p-5 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Currently Available</span>
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-950/50 rounded-lg">
                                <Package class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight text-indigo-600 dark:text-indigo-400">
                                {{ formatNumber(branchSummaries.current_available) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">receipts</span>
                        </div>
                        <div class="mt-2 text-xs text-slate-400 dark:text-slate-500">Available stock in hand</div>
                    </div>

                    <!-- Total Received Cumulative -->
                    <div class="relative overflow-hidden p-5 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Received</span>
                            <div class="p-2 bg-purple-50 dark:bg-purple-950/50 rounded-lg">
                                <BarChart3 class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight text-purple-600 dark:text-purple-400">
                                {{ formatNumber(branchSummaries.all_time_received) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">receipts</span>
                        </div>
                        <div class="mt-2 text-xs text-slate-400 dark:text-slate-500">Cumulative stock (all time)</div>
                    </div>
                </div>

                <!-- Filters Bar -->
                <div class="p-5 bg-white/60 dark:bg-slate-900/40 border border-slate-200/40 dark:border-slate-800/50 backdrop-blur-md rounded-2xl shadow-sm">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <!-- Start Date -->
                        <div class="flex flex-col gap-1.5">
                            <Label for="start_date" value="Start Date" class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500" />
                            <CustomDateInput v-model="filters.start_date" placeholder="dd/mm/yyyy"
                                class="block w-full mt-1" @update:modelValue="handleFilterChange" />
                        </div>

                        <!-- End Date -->
                        <div class="flex flex-col gap-1.5">
                            <Label for="end_date" value="End Date" class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500" />
                            <CustomDateInput v-model="filters.end_date" placeholder="dd/mm/yyyy" class="block w-full mt-1"
                                @update:modelValue="handleFilterChange" />
                        </div>
                    </div>
                </div>

                <!-- Available Stock by Lot Section -->
                <div class="space-y-4">
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Available Stock by Lot
                    </h2>
                    
                    <div v-if="availableBooks && availableBooks.length > 0" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="lot in availableBooks" :key="lot.lot_id"
                            class="p-6 bg-white dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/80 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                            
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="font-extrabold text-base text-slate-900 dark:text-white leading-tight">
                                        {{ lot.lot_number }}
                                    </h4>
                                    <p v-if="lot.lot_name" class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                                        {{ lot.lot_name }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 rounded-lg dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30">
                                    {{ lot.total_books }} books
                                </span>
                            </div>
                            
                            <div class="space-y-2.5 text-sm pt-2 border-t border-slate-100 dark:border-slate-800/50">
                                <div class="flex justify-between">
                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Book Range:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-200">#{{ lot.book_range }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Receipt Range:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-200">{{ lot.receipt_range }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-slate-800/50">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Receipts:</span>
                                    <span class="text-lg font-black text-indigo-600 dark:text-indigo-400">
                                        {{ formatNumber(lot.total_receipts) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-12 text-center bg-white dark:bg-slate-900/40 border border-slate-200/50 dark:border-slate-800/80 rounded-2xl">
                        <Package class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" />
                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">No active stock available in this branch.</p>
                    </div>
                </div>

                <!-- Recent Transactions Section -->
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/50 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                Recent Transactions
                            </h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                Log of received stock and distributions.
                            </p>
                        </div>
                    </div>

                    <!-- Flash messages inside panel -->
                    <div class="px-6 pt-4 space-y-3">
                        <div v-if="$page.props.flash.success"
                            class="flex items-center gap-3 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-900/50 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20">
                            <CheckCircle class="w-5 h-5 text-emerald-500 shrink-0" />
                            <span>{{ $page.props.flash.success }}</span>
                        </div>

                        <div v-if="$page.props.flash.error"
                            class="flex items-center gap-3 p-4 text-sm font-semibold text-red-800 dark:text-red-300 border border-red-100 dark:border-red-900/50 rounded-xl bg-red-50/50 dark:bg-red-950/20">
                            <AlertCircle class="w-5 h-5 text-red-500 shrink-0" />
                            <span>{{ $page.props.flash.error }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                            <thead class="bg-slate-50/75 dark:bg-slate-900/40">
                                <tr>
                                    <th class="px-6 py-3.5 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                        Date
                                    </th>
                                    <th class="px-6 py-3.5 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                        Lot
                                    </th>
                                    <th class="px-6 py-3.5 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                        Type
                                    </th>
                                    <th class="px-6 py-3.5 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                        Books Range
                                    </th>
                                    <th class="px-6 py-3.5 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                        Total Receipts
                                    </th>
                                    <th class="px-6 py-3.5 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                        Recipient / Issuer
                                    </th>
                                    <th class="px-6 py-3.5 text-xs font-bold tracking-wider text-right text-slate-400 dark:text-slate-500 uppercase">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 bg-white dark:bg-slate-900/25">
                                <tr v-for="transaction in receipts.data" :key="transaction.id"
                                    class="transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-900 whitespace-nowrap dark:text-slate-200">
                                        {{ formatDate(transaction.transaction_date) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap dark:text-slate-400">
                                        {{ transaction.lot?.lot_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                            :class="getTransactionTypeClass(transaction.transaction_type)">
                                            {{ getTransactionTypeLabel(transaction.transaction_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap dark:text-slate-400">
                                        Book #{{ transaction.book_from }} - #{{ transaction.book_to }}
                                        <span class="ml-1 text-xs text-slate-400 dark:text-slate-500 font-medium">({{ transaction.total_books }} books)</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-900 whitespace-nowrap dark:text-white">
                                        {{ formatNumber(transaction.total_receipts) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap dark:text-slate-400">
                                        {{ transaction.given_to || transaction.received_by || '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        <div class="flex justify-end">
                                            <button @click="viewTransactionDetails(transaction.id)"
                                                class="p-2 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all duration-200">
                                                <Eye class="w-4.5 h-4.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!receipts.data || receipts.data.length === 0">
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-400 dark:text-slate-500">
                                        No transactions found for the selected criteria.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="border-t border-slate-100 dark:border-slate-800" v-if="receipts.links">
                        <Pagination :links="receipts.links" :data="receipts" />
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
import { Link, router } from '@inertiajs/vue3';
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
    Search,
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
        'distribute_to_branch': 'bg-green-50 text-green-700 border border-green-100 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900/30',
        'distribute_to_person': 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30'
    };
    return classes[type] || 'bg-slate-50 text-slate-700 border border-slate-100 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800';
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
};

const handleDeleteFromDetails = (transactionId) => {
    showDetailsModal.value = false;
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
</script>
