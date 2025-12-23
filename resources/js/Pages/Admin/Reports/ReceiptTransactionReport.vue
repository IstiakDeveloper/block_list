<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    transactions: {
        type: Object,
        required: true
    },
    branches: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    summary: {
        type: Object,
        required: true
    }
});

// State
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');
const selectedBranchId = ref(props.filters.branch_id || '');
const viewMode = ref('detailed'); // 'detailed' or 'summary'

// Computed
const selectedBranchName = computed(() => {
    if (!selectedBranchId.value) return 'All Branches';
    const branch = props.branches.find(b => b.id == selectedBranchId.value);
    return branch ? branch.branch_name : 'All Branches';
});

// Methods
const applyFilters = () => {
    router.get(
        route('admin.receipt-transaction-report.index'),
        {
            start_date: startDate.value,
            end_date: endDate.value,
            branch_id: selectedBranchId.value
        },
        {
            preserveState: true,
            preserveScroll: true
        }
    );
};

const resetFilters = () => {
    startDate.value = '';
    endDate.value = '';
    selectedBranchId.value = '';
    applyFilters();
};

const downloadPdf = () => {
    const params = new URLSearchParams({
        start_date: startDate.value,
        end_date: endDate.value,
        branch_id: selectedBranchId.value
    });

    window.location.href = route('admin.receipt-transaction-report.pdf') + '?' + params.toString();
};

const formatNumber = (number) => {
    return new Intl.NumberFormat('en-IN').format(number);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Group transactions by month and date
const groupedTransactions = computed(() => {
    const monthGroups = {};

    props.transactions.data.forEach(transaction => {
        const transactionDate = new Date(transaction.transaction_date);
        const monthKey = transactionDate.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit' });
        const monthName = transactionDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
        const dateKey = transaction.transaction_date;

        if (!monthGroups[monthKey]) {
            monthGroups[monthKey] = {
                monthName: monthName,
                dates: {}
            };
        }

        if (!monthGroups[monthKey].dates[dateKey]) {
            monthGroups[monthKey].dates[dateKey] = {
                date: dateKey,
                dayName: transactionDate.toLocaleDateString('en-US', { weekday: 'long' }),
                received: [],
                distributed: []
            };
        }

        // Check if this is a receive or distribution transaction
        if (transaction.receive_quantity > 0 && !transaction.given_to) {
            monthGroups[monthKey].dates[dateKey].received.push(transaction);
        }

        if (transaction.given_quantity > 0 && transaction.given_to) {
            monthGroups[monthKey].dates[dateKey].distributed.push(transaction);
        }
    });

    return monthGroups;
});
</script>

<template>
    <Head title="Receipt Transaction Report" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Receipt Transaction Report</h2>
                <p class="text-gray-600 mt-2">Date-wise receipt receiving and distribution report</p>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Filters</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date
                        </label>
                        <input
                            v-model="startDate"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            End Date
                        </label>
                        <input
                            v-model="endDate"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    <!-- Branch Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Branch
                        </label>
                        <select
                            v-model="selectedBranchId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Branches</option>
                            <option
                                v-for="branch in branches"
                                :key="branch.id"
                                :value="branch.id"
                            >
                                {{ branch.branch_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button
                            @click="applyFilters"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150"
                        >
                            Apply
                        </button>
                        <button
                            @click="resetFilters"
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-150"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Received</p>
                            <p class="text-3xl font-bold mt-2">{{ formatNumber(summary.total_received) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-30 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Distributed</p>
                            <p class="text-3xl font-bold mt-2">{{ formatNumber(summary.total_distributed) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-30 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Available</p>
                            <p class="text-3xl font-bold mt-2">{{ formatNumber(summary.total_available) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-30 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Total Transactions</p>
                            <p class="text-3xl font-bold mt-2">{{ formatNumber(summary.total_transactions) }}</p>
                        </div>
                        <div class="bg-white bg-opacity-30 rounded-full p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Button -->
            <div class="flex justify-end mb-4">
                <button
                    @click="downloadPdf"
                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition duration-150 flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </button>
            </div>

            <!-- Transactions List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Transaction Details</h3>

                    <div v-if="Object.keys(groupedTransactions).length === 0" class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 text-lg">No transactions found</p>
                    </div>

                    <div v-else class="space-y-8">
                        <!-- Month-wise grouped transactions -->
                        <div
                            v-for="(monthGroup, monthKey) in groupedTransactions"
                            :key="monthKey"
                            class="border-2 border-gray-300 rounded-lg overflow-hidden"
                        >
                            <!-- Month Header -->
                            <div class="bg-gradient-to-r from-gray-800 to-gray-700 text-white px-6 py-3">
                                <h3 class="text-xl font-bold flex items-center gap-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ monthGroup.monthName }}
                                </h3>
                            </div>

                            <!-- Date-wise transactions within month -->
                            <div class="p-4 space-y-4">
                                <div
                                    v-for="(dayData, dateKey) in monthGroup.dates"
                                    :key="dateKey"
                                    class="border border-gray-200 rounded-lg overflow-hidden"
                                >
                                    <!-- Date Header -->
                                    <div class="bg-gray-600 text-white px-4 py-2 flex items-center justify-between">
                                        <h4 class="font-semibold flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ formatDate(dateKey) }}
                                        </h4>
                                        <span class="text-sm text-gray-200">{{ dayData.dayName }}</span>
                                    </div>

                                    <div class="p-3">
                                        <!-- Received Section -->
                                        <div v-if="dayData.received.length > 0" class="mb-3">
                                            <h5 class="text-sm font-medium text-green-700 mb-2 flex items-center gap-2 bg-green-50 px-3 py-2 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4"/>
                                                </svg>
                                                📥 Received ({{ dayData.received.length }})
                                            </h5>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-xs border border-gray-300">
                                                    <thead class="bg-green-100">
                                                        <tr>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">SL</th>
                                                            <th class="px-2 py-1 text-left font-bold text-gray-800 uppercase border border-gray-300">Branch</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">Qty</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">From</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">To</th>
                                                            <th class="px-2 py-1 text-left font-bold text-gray-800 uppercase border border-gray-300">Received By</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">Available</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                        <tr
                                                            v-for="(transaction, index) in dayData.received"
                                                            :key="transaction.id"
                                                            :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                                                            class="hover:bg-green-50"
                                                        >
                                                            <td class="px-2 py-1 text-center font-medium text-gray-700 border border-gray-300">{{ index + 1 }}</td>
                                                            <td class="px-2 py-1 text-gray-900 border border-gray-300">{{ transaction.branch?.branch_name || 'N/A' }}</td>
                                                            <td class="px-2 py-1 text-center font-bold text-green-700 border border-gray-300">{{ formatNumber(transaction.receive_quantity) }}</td>
                                                            <td class="px-2 py-1 text-center text-gray-700 border border-gray-300">{{ formatNumber(transaction.receipt_from_number) }}</td>
                                                            <td class="px-2 py-1 text-center text-gray-700 border border-gray-300">{{ formatNumber(transaction.receipt_to_number) }}</td>
                                                            <td class="px-2 py-1 text-gray-900 border border-gray-300">{{ transaction.received_by || 'N/A' }}</td>
                                                            <td class="px-2 py-1 text-center font-bold text-purple-700 border border-gray-300">{{ formatNumber(transaction.available_receipts) }}</td>
                                                        </tr>
                                                        <tr class="bg-green-200 font-bold">
                                                            <td colspan="2" class="px-2 py-1 text-right border border-gray-300">Total:</td>
                                                            <td class="px-2 py-1 text-center border border-gray-300">{{ formatNumber(dayData.received.reduce((sum, t) => sum + t.receive_quantity, 0)) }}</td>
                                                            <td colspan="3" class="border border-gray-300"></td>
                                                            <td class="px-2 py-1 text-center border border-gray-300">{{ formatNumber(dayData.received.reduce((sum, t) => sum + t.available_receipts, 0)) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Distributed Section -->
                                        <div v-if="dayData.distributed.length > 0">
                                            <h5 class="text-sm font-medium text-blue-700 mb-2 flex items-center gap-2 bg-blue-50 px-3 py-2 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                                </svg>
                                                📤 Distributed ({{ dayData.distributed.length }})
                                            </h5>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-xs border border-gray-300">
                                                    <thead class="bg-blue-100">
                                                        <tr>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">SL</th>
                                                            <th class="px-2 py-1 text-left font-bold text-gray-800 uppercase border border-gray-300">Branch</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">Qty</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">From</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">To</th>
                                                            <th class="px-2 py-1 text-left font-bold text-gray-800 uppercase border border-gray-300">Given To</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">PIN</th>
                                                            <th class="px-2 py-1 text-center font-bold text-gray-800 uppercase border border-gray-300">Book</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                        <tr
                                                            v-for="(transaction, index) in dayData.distributed"
                                                            :key="transaction.id"
                                                            :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                                                            class="hover:bg-blue-50"
                                                        >
                                                            <td class="px-2 py-1 text-center font-medium text-gray-700 border border-gray-300">{{ index + 1 }}</td>
                                                            <td class="px-2 py-1 text-gray-900 border border-gray-300">{{ transaction.branch?.branch_name || 'N/A' }}</td>
                                                            <td class="px-2 py-1 text-center font-bold text-blue-700 border border-gray-300">{{ formatNumber(transaction.given_quantity) }}</td>
                                                            <td class="px-2 py-1 text-center text-gray-700 border border-gray-300">{{ formatNumber(transaction.given_from_number) }}</td>
                                                            <td class="px-2 py-1 text-center text-gray-700 border border-gray-300">{{ formatNumber(transaction.given_to_number) }}</td>
                                                            <td class="px-2 py-1 text-gray-900 border border-gray-300">{{ transaction.given_to || 'N/A' }}</td>
                                                            <td class="px-2 py-1 text-center text-gray-700 border border-gray-300">{{ transaction.pin_number || '-' }}</td>
                                                            <td class="px-2 py-1 text-center text-gray-700 border border-gray-300">{{ transaction.receipt_book_number || '-' }}</td>
                                                        </tr>
                                                        <tr class="bg-blue-200 font-bold">
                                                            <td colspan="2" class="px-2 py-1 text-right border border-gray-300">Total:</td>
                                                            <td class="px-2 py-1 text-center border border-gray-300">{{ formatNumber(dayData.distributed.reduce((sum, t) => sum + t.given_quantity, 0)) }}</td>
                                                            <td colspan="5" class="border border-gray-300"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="transactions.links.length > 3" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-wrap justify-center gap-1">
                        <template v-for="(link, index) in transactions.links" :key="index">
                            <component
                                :is="link.url ? 'Link' : 'span'"
                                :href="link.url"
                                :class="[
                                    'px-4 py-2 text-sm border rounded-lg',
                                    link.active
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : link.url
                                        ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                        : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom styles if needed */
</style>
