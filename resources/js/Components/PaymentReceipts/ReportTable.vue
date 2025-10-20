<template>
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="p-6">
            <!-- Report Generation Actions -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <select v-model="reportForm.type"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="transaction_summary">Transaction Summary</option>
                        <option value="branch_stock">Branch Stock Status</option>
                        <option value="distribution_details">Distribution Details</option>
                        <option value="daily_collection">Daily Collection</option>
                    </select>
                    <select v-model="reportForm.format"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
                <button @click="generateReport"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-400">
                    <FileDownIcon class="w-5 h-5" />
                    Generate Report
                </button>
            </div>

            <!-- Filters -->
            <div class="mb-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <Label for="start_date" value="Start Date" />
                        <CustomDateInput v-model="filters.start_date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <Label for="end_date" value="End Date" />
                        <CustomDateInput v-model="filters.end_date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <Label for="branch" value="Branch" />
                        <select v-model="filters.branch_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <option value="">All Branches</option>
                            <option v-for="branch in sortedBranches" :key="branch.id" :value="branch.id">
                                {{ branch.branch_code }} - {{ branch.branch_name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Label for="type" value="Transaction Type" />
                        <select v-model="filters.type"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <option value="">All Types</option>
                            <option value="stock_in">Stock In</option>
                            <option value="distribute_to_branch">To Branch</option>
                            <option value="distribute_to_person">To Person</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th v-for="column in tableColumns" :key="column.key"
                                class="px-6 py-3 text-xs font-medium tracking-wider"
                                :class="[
                                    column.align === 'right' ? 'text-right' : 'text-left',
                                    'text-gray-500 uppercase dark:text-gray-400'
                                ]">
                                {{ column.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        <tr v-for="transaction in transactions" :key="transaction.id"
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td v-for="column in tableColumns" :key="column.key"
                                class="px-6 py-4 whitespace-nowrap"
                                :class="column.cellClass || 'text-sm text-gray-900 dark:text-gray-300'">
                                <template v-if="column.type === 'date'">
                                    {{ formatDate(transaction[column.key]) }}
                                </template>
                                <template v-else-if="column.type === 'number'">
                                    {{ formatNumber(transaction[column.key]) }}
                                </template>
                                <template v-else-if="column.type === 'status'">
                                    <span :class="[getStatusClass(transaction.status), 'inline-flex px-2 text-xs font-semibold leading-5 rounded-full']">
                                        {{ transaction.status }}
                                    </span>
                                </template>
                                <template v-else-if="column.key === 'actions'">
                                    <div class="flex justify-end gap-2">
                                        <button @click="$emit('view', transaction)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <Eye class="w-5 h-5" />
                                        </button>
                                        <button v-if="canEdit(transaction)"
                                            @click="$emit('edit', transaction)"
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            <Edit class="w-5 h-5" />
                                        </button>
                                        <button v-if="canDelete(transaction)"
                                            @click="$emit('delete', transaction)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <Trash2 class="w-5 h-5" />
                                        </button>
                                    </div>
                                </template>
                                <template v-else>
                                    {{ column.format ? column.format(transaction[column.key], transaction) : transaction[column.key] }}
                                </template>
                            </td>
                        </tr>
                        <tr v-if="!transactions.length"
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td :colspan="tableColumns.length"
                                class="px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                No transactions found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4" v-if="pagination && pagination.links">
                <Pagination :links="pagination.links" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Label from '@/Components/Label.vue';
import CustomDateInput from '@/Components/CustomDateInput.vue';
import Pagination from '@/Components/Pagination.vue';
import { Eye, Edit, Trash2, FileDownIcon } from 'lucide-vue-next';

const props = defineProps({
    transactions: {
        type: Array,
        required: true
    },
    branches: {
        type: Array,
        required: true
    },
    userRole: {
        type: String,
        required: true
    },
    pagination: {
        type: Object,
        default: null
    },
    initialFilters: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['view', 'edit', 'delete', 'filter']);

const reportForm = ref({
    type: 'transaction_summary',
    format: 'pdf'
});

const filters = ref({
    start_date: props.initialFilters.start_date || '',
    end_date: props.initialFilters.end_date || '',
    branch_id: props.initialFilters.branch_id || '',
    type: props.initialFilters.type || ''
});

const sortedBranches = computed(() => {
    return [...props.branches].sort((a, b) => a.branch_code.localeCompare(b.branch_code));
});

const tableColumns = [
    { key: 'transaction_date', label: 'Date', type: 'date' },
    { key: 'transaction_type', label: 'Type', type: 'badge' },
    { key: 'from', label: 'From', format: (_, row) => row.from_branch?.branch_name || 'System' },
    { key: 'to', label: 'To', format: (_, row) => row.to_branch?.branch_name || row.given_to || '-' },
    { key: 'total_receipts', label: 'Receipts', type: 'number' },
    { key: 'status', label: 'Status', type: 'status' },
    { key: 'actions', label: 'Actions', align: 'right' }
];

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const formatNumber = (number) => {
    return new Intl.NumberFormat('en-US').format(number);
};

const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        'completed': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
    };
    return classes[status.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300';
};

const canEdit = (transaction) => {
    if (props.userRole === 'super_admin') return true;
    if (props.userRole === 'branch_admin') {
        return transaction.status === 'pending' &&
               (transaction.from_branch_id === userBranch || transaction.to_branch_id === userBranch);
    }
    return false;
};

const canDelete = (transaction) => {
    return props.userRole === 'super_admin' && transaction.status === 'pending';
};

const generateReport = () => {
    const params = {
        ...filters.value,
        report_type: reportForm.value.type,
        format: reportForm.value.format
    };

    router.get(route('payment-receipts.generate-report'), params, {
        preserveScroll: true
    });
};

// Watch filters and emit changes
watch(filters, (newFilters) => {
    emit('filter', newFilters);
}, { deep: true });
</script>
