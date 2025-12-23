<template>
    <AdminLayout title="Payment Receipts - Super Admin">
        <div class="py-6 dark:bg-gray-900">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">
                        <h2 class="flex items-center gap-2 text-xl font-semibold text-gray-800 dark:text-gray-200">
                            <LayoutDashboard class="w-6 h-6 text-blue-600" />
                            All Branches Overview
                        </h2>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <!-- Current Stock Display -->
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-lg dark:bg-indigo-900/30 dark:border-indigo-700">
                                <Package class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                <span class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
                                    Head Office Stock:
                                </span>
                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ formatNumber(currentStock) }}
                                </span>
                            </div>

                            <!-- Add Stock Button -->
                            <button @click="showAddStockModal = true"
                                class="inline-flex items-center gap-2 px-4 py-2 font-medium text-white transition-colors bg-purple-600 rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                <PackagePlus class="w-5 h-5" />
                                Add Stock
                            </button>

                            <!-- Distribute Button -->
                            <button @click="showDistributeModal = true"
                                class="inline-flex items-center gap-2 px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <SendIcon class="w-5 h-5" />
                                Distribute to Branch
                            </button>

                            <!-- Generate Report Button -->
                            <button @click="openReportModal"
                                class="inline-flex items-center gap-2 px-4 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <FileText class="w-5 h-5" />
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
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

                        <div>
                            <Label value="Branch Filter" class="text-gray-700 dark:text-gray-300" />
                            <div class="relative">
                                <input
                                    type="text"
                                    v-model="branchSearch"
                                    placeholder="Search by branch code or name..."
                                    class="block w-full mt-1 pl-8 border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    @focus="showBranchDropdown = true"
                                />
                                <Search class="absolute w-4 h-4 text-gray-400 left-2.5 top-[1.15rem]" />

                                <div v-if="showBranchDropdown && filteredBranches.length > 0"
                                    class="absolute z-50 w-full mt-1 overflow-auto bg-white border border-gray-300 rounded-md shadow-lg max-h-60 dark:bg-gray-700 dark:border-gray-600">
                                    <div class="py-1">
                                        <div class="px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                                            @click="selectBranch('')">
                                            All Branches
                                        </div>
                                        <div v-for="branch in filteredBranches"
                                            :key="branch.id"
                                            @click="selectBranch(branch)"
                                            class="px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ branch.branch_code }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ branch.branch_name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="selectedBranch" class="mt-2 p-2 text-sm bg-blue-50 dark:bg-blue-900/20 rounded-md">
                                <span class="text-blue-700 dark:text-blue-300">
                                    Selected: {{ selectedBranch.branch_code }} - {{ selectedBranch.branch_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branch Summary Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="summary in branchSummaries" :key="summary.branch_id"
                        class="p-6 transition-all bg-white rounded-lg shadow-sm hover:shadow-md dark:bg-gray-800">
                        <!-- Branch Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ summary.branch_name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ summary.branch_code }}
                                </p>
                            </div>
                            <button @click="viewBranchDetails(summary.branch_id)"
                                class="p-2 text-blue-600 transition-colors rounded-lg hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20">
                                <Eye class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Current Period Stats -->
                        <div class="p-4 mb-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Current Period
                            </h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Received</div>
                                    <div class="text-lg font-semibold text-green-600 dark:text-green-400">
                                        {{ formatNumber(summary.period_received) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Distributed</div>
                                    <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                        {{ formatNumber(summary.period_distributed) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Balance</div>
                                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ formatNumber(summary.period_received - summary.period_distributed) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- All Time Stats -->
                        <div class="p-4 mb-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Cumulative
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Received</div>
                                    <div class="text-lg font-semibold text-green-600 dark:text-green-400">
                                        {{ formatNumber(summary.all_time_received) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Distributed</div>
                                    <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                        {{ formatNumber(summary.all_time_distributed) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Balance -->
                        <div class="p-4 rounded-lg" :class="getStockColorClass(summary.current_available)">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-medium opacity-90">Currently Available</div>
                                    <div class="text-2xl font-bold">
                                        {{ formatNumber(summary.current_available) }}
                                    </div>
                                </div>
                                <div class="text-sm font-medium">
                                    {{ getStockStatus(summary.current_available) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overall Summary -->
                <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                        Overall Summary
                    </h3>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                        <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/30">
                            <div class="text-sm text-green-700 dark:text-green-300">Total Period Received</div>
                            <div class="text-2xl font-semibold text-green-900 dark:text-green-100">
                                {{ totalPeriodReceived }}
                            </div>
                        </div>

                        <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900/30">
                            <div class="text-sm text-blue-700 dark:text-blue-300">Total Period Distributed</div>
                            <div class="text-2xl font-semibold text-blue-900 dark:text-blue-100">
                                {{ totalPeriodDistributed }}
                            </div>
                        </div>

                        <div class="p-4 rounded-lg bg-indigo-50 dark:bg-indigo-900/30">
                            <div class="text-sm text-indigo-700 dark:text-indigo-300">Total Available</div>
                            <div class="text-2xl font-semibold text-indigo-900 dark:text-indigo-100">
                                {{ totalAvailable }}
                            </div>
                        </div>

                        <div class="p-4 rounded-lg bg-purple-50 dark:bg-purple-900/30">
                            <div class="text-sm text-purple-700 dark:text-purple-300">Active Branches</div>
                            <div class="text-2xl font-semibold text-purple-900 dark:text-purple-100">
                                {{ branchSummaries.length }}
                            </div>
                        </div>
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
                                            Branch
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
                                            Given To
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
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-300">
                                            {{ transaction.branch?.branch_name || '-' }}
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
                                                <button @click="handleEditTransaction(transaction)"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                    <Edit class="w-5 h-5" />
                                                </button>
                                                <button @click="confirmDelete(transaction.id)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    <Trash2 class="w-5 h-5" />
                                                </button>
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
        <AddStockModal :show="showAddStockModal" :active-lots="activeLots" @close="showAddStockModal = false"
            @success="handleFilterChange" />

        <DistributeToBranchModal :show="showDistributeModal" :branches="branches" :active-lots="activeLots"
            @close="showDistributeModal = false" @success="handleFilterChange" />

        <BranchTransactionsModal :show="showBranchModal" :branch="selectedBranch" :transactions="branchTransactions"
            :filters="filters" @close="closeBranchModal" @refresh="handleFilterChange"
            @view-details="viewTransactionDetails" @edit="handleEditTransaction" @delete="confirmDelete" />

        <TransactionDetailsModal :show="showDetailsModal" :transaction="selectedTransaction"
            @close="showDetailsModal = false"
            @edit="handleEditTransaction"
            @delete="handleDeleteFromDetails" />

        <ReportModal :show="showReportModal" :branches="branches" :filters="filters" @close="showReportModal = false" />

        <EditTransactionModal
            :show="showEditModal"
            :transaction="selectedTransaction"
            :active-lots="activeLots"
            @close="showEditModal = false"
            @success="handleEditSuccess"
        />

        <DeleteConfirmModal :show="showDeleteModal" :deleting="deleting" @close="showDeleteModal = false"
            @confirm="deleteTransaction" />
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Search } from 'lucide-vue-next';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Label from '@/Components/Label.vue';
import CustomDateInput from '@/Components/CustomDateInput.vue';
import Pagination from '@/Components/Pagination.vue';

// Import Modals
import AddStockModal from './Modals/AddStockModal.vue';
import DistributeToBranchModal from './Modals/DistributeToBranchModal.vue';
import BranchTransactionsModal from './Modals/BranchTransactionsModal.vue';
import TransactionDetailsModal from './Modals/TransactionDetailsModal.vue';
import EditTransactionModal from './Modals/EditTransactionModal.vue';
import ReportModal from './Modals/ReportModal.vue';
import DeleteConfirmModal from './Modals/DeleteConfirmModal.vue';

// Lucide Icons
import {
    LayoutDashboard,
    Package,
    PackagePlus,
    SendIcon,
    FileText,
    Eye,
    Edit,
    Trash2,
    CheckCircle,
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
    receipts: Object,
    branchSummaries: Array,
    branches: Array,
    activeLots: Array,
    currentStock: Number,
    filters: Object
});

// Modals State
const showAddStockModal = ref(false);
const showDistributeModal = ref(false);
const showBranchModal = ref(false);
const showDetailsModal = ref(false);
const showReportModal = ref(false);
const showDeleteModal = ref(false);
const showEditModal = ref(false);

// Data State
const selectedBranch = ref(null);
const selectedTransaction = ref(null);
const branchTransactions = ref([]);
const selectedTransactionId = ref(null);
const deleting = ref(false);

// Branch Filter State
const branchSearch = ref('');
const showBranchDropdown = ref(false);

// Filters
const filters = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
    branch_id: props.filters.branch_id || ''
});

// Computed Properties
const sortedBranches = computed(() => {
    return [...props.branches].sort((a, b) => a.branch_code.localeCompare(b.branch_code));
});

const filteredBranches = computed(() => {
    const search = branchSearch.value.toLowerCase();
    return sortedBranches.value.filter(branch =>
        branch.branch_code.toLowerCase().includes(search) ||
        branch.branch_name.toLowerCase().includes(search)
    );
});

const totalPeriodReceived = computed(() => {
    const total = props.branchSummaries.reduce((sum, branch) => sum + (Number(branch.period_received) || 0), 0);
    return formatNumber(total);
});

const totalPeriodDistributed = computed(() => {
    const total = props.branchSummaries.reduce((sum, branch) => sum + (Number(branch.period_distributed) || 0), 0);
    return formatNumber(total);
});

const totalAvailable = computed(() => {
    const total = props.branchSummaries.reduce((sum, branch) => sum + (Number(branch.current_available) || 0), 0);
    return formatNumber(total);
});

// Branch Selection Handler
const selectBranch = (branch) => {
    if (!branch) {
        selectedBranch.value = null;
        filters.value.branch_id = '';
        branchSearch.value = '';
    } else {
        selectedBranch.value = branch;
        filters.value.branch_id = branch.id;
        branchSearch.value = branch.branch_code;
    }
    showBranchDropdown.value = false;
    handleFilterChange();
};

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

const getStockStatus = (available) => {
    if (available < 100) return 'Low Stock';
    if (available < 500) return 'Moderate';
    return 'Good Stock';
};

const getStockColorClass = (available) => {
    if (available < 100) return 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300';
    if (available < 500) return 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300';
    return 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300';
};

const getTransactionTypeLabel = (type) => {
    const labels = {
        'stock_in': 'Stock In',
        'distribute_to_branch': 'To Branch',
        'distribute_to_person': 'To Person'
    };
    return labels[type] || type;
};

const getTransactionTypeClass = (type) => {
    const classes = {
        'stock_in': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
        'distribute_to_branch': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        'distribute_to_person': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
    };
    return classes[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
};

const handleFilterChange = debounce(() => {
    router.get(route('payment-receipts.index'), {
        start_date: filters.value.start_date,
        end_date: filters.value.end_date,
        branch_id: filters.value.branch_id
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['receipts', 'branchSummaries', 'currentStock']
    });
}, 300);

const viewBranchDetails = async (branchId) => {
    try {
        const params = new URLSearchParams({
            start_date: filters.value.start_date || '',
            end_date: filters.value.end_date || '',
            _t: Date.now()
        });

        const response = await fetch(`${route('payment-receipts.branch-transactions', { branch: branchId })}?${params}`);
        const data = await response.json();

        if (data.success) {
            branchTransactions.value = data.transactions;
            selectedBranch.value = props.branches.find(b => b.id === branchId);
            showBranchModal.value = true;
        }
    } catch (error) {
        console.error('Error fetching branch transactions:', error);
    }
};

const closeBranchModal = () => {
    showBranchModal.value = false;
    selectedBranch.value = null;
    branchTransactions.value = [];
};

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

const handleEditTransaction = (transaction) => {
    // Close details modal if open
    showDetailsModal.value = false;

    // Set selected transaction and open edit modal
    selectedTransaction.value = transaction;
    showEditModal.value = true;
};

const handleEditSuccess = () => {
    // Close edit modal
    showEditModal.value = false;

    // Refresh data
    handleFilterChange();

    // If branch modal is open, refresh branch transactions
    if (showBranchModal.value && selectedBranch.value) {
        viewBranchDetails(selectedBranch.value.id);
    }
};

const handleDeleteFromDetails = (transactionId) => {
    // Close details modal
    showDetailsModal.value = false;

    // Open delete confirmation
    selectedTransactionId.value = transactionId;
    showDeleteModal.value = true;
};
</script>
