<template>
    <AdminLayout title="Payment Receipts Overview">
        <div class="py-6 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Header with Download -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="p-6 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            All Branches Overview
                        </h2>
                        <div class="flex gap-2">
                            <button @click="openReportModal"
                                class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 text-white font-semibold rounded-md hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Generate Report
                            </button>
                        </div>


                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Date Range -->
                        <!-- Start Date -->
                        <div>
                            <Label for="start_date" value="Start Date" class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="filters.start_date" placeholder="dd/mm/yyyy"
                                class="mt-1 block w-full" @update:modelValue="handleFilterChange" />
                        </div>

                        <!-- End Date -->
                        <div>
                            <Label for="end_date" value="End Date" class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="filters.end_date" placeholder="dd/mm/yyyy"
                                class="mt-1 block w-full" @update:modelValue="handleFilterChange" />
                        </div>
                        <!-- Branch Filter -->
                        <div>
                            <Label value="Branch" class="text-gray-700 dark:text-gray-300" />
                            <select v-model="filters.branch_id" @change="handleFilterChange"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Branches</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.branch_name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Branch Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="summary in branchSummaries" :key="summary.branch_id"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6">
                        <!-- Branch Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ summary.branch_name }}
                            </h3>
                            <button @click="viewBranchDetails(summary.branch_id)"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                View Details
                            </button>
                        </div>

                        <!-- Statistics Grid -->
                        <div class="space-y-4">
                            <!-- Current Period -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">
                                    Current Period
                                </h4>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Received</div>
                                        <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ summary.period_received }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Disbursement</div>
                                        <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ summary.period_distributed }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Balance</div>
                                        <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ summary.period_received - summary.period_distributed }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- All Time -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">
                                    Cumulative
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Received</div>
                                        <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ summary.all_time_received }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Disbursement</div>
                                        <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                            {{ summary.all_time_distributed }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Balance Receipts -->
                            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-sm text-blue-600 dark:text-blue-200">Currently Balance</div>
                                        <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                            {{ summary.current_available }}
                                        </div>
                                    </div>
                                    <div :class="{
                                        'text-green-600 dark:text-green-400': summary.current_available > 500,
                                        'text-yellow-600 dark:text-yellow-400': summary.current_available >= 100 && summary.current_available < 500,
                                        'text-red-600 dark:text-red-400': summary.current_available < 100
                                    }" class="text-sm font-medium">
                                        {{ getStockStatus(summary.current_available) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Summary Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Overall Summary
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Total Period Received -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Period Received</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ totalPeriodReceived }}
                            </div>
                        </div>

                        <!-- Total Period Disbursement -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Period Disbursement</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ totalPeriodDistributed }}
                            </div>
                        </div>

                        <!-- Total Balance -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Balance Receipts</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ totalAvailable }}
                            </div>
                        </div>

                        <!-- Total Branches -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Active Branches</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ branchSummaries.length }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Recent Transactions
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Branch
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Branch Code
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                            Received
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider">
                                            Disbursement
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Balance
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Period Stats
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="summary in branchSummaries" :key="summary.branch_id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ summary.branch_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ summary.branch?.branch_code || '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                                                {{ summary.all_time_received }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                All Time
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-sky-600 dark:text-sky-400 font-medium">
                                                {{ summary.all_time_distributed }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                All Time
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium" :class="{
                                                'text-emerald-600 dark:text-emerald-400': summary.current_available > 100,
                                                'text-yellow-600 dark:text-yellow-400': summary.current_available <= 100 && summary.current_available > 0,
                                                'text-red-600 dark:text-red-400': summary.current_available === 0
                                            }">
                                                {{ summary.current_available }}
                                            </div>
                                            <div class="text-xs" :class="{
                                                'text-emerald-500 dark:text-emerald-400': summary.current_available > 100,
                                                'text-yellow-500 dark:text-yellow-400': summary.current_available <= 100 && summary.current_available > 0,
                                                'text-red-500 dark:text-red-400': summary.current_available === 0
                                            }">
                                                {{ getStockStatus(summary.current_available) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                <div class="text-sm">
                                                    <span class="text-emerald-600 dark:text-emerald-400">{{
                                                        summary.period_received }}</span>
                                                    <span class="text-gray-500 dark:text-gray-400"> received</span>
                                                </div>
                                                <div class="text-sm">
                                                    <span class="text-sky-600 dark:text-sky-400">{{
                                                        summary.period_distributed }}</span>
                                                    <span class="text-gray-500 dark:text-gray-400"> disbursement</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="viewBranchDetails(summary.branch_id)"
                                                class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                <span>View Details</span>
                                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <Modal :show="showTransactionModal" @close="closeTransactionModal" >
                            <div class="flex flex-col bg-white dark:bg-gray-800 rounded-lg shadow-xl">

                                <div
                                    class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ selectedBranch?.branch_name }} - Transaction Details
                                    </h2>
                                    <button @click="closeTransactionModal"
                                        class="p-2 text-gray-400 hover:text-gray-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <span class="sr-only">Close</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>


                                <div class="flex-1 px-6 py-4">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead>
                                                <tr class="bg-gray-50 dark:bg-gray-700">
                                                    <th
                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                                        Date
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                                        Received
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                                        From - To
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider">
                                                        Disbursed
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider">
                                                        From - To
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                        Balance
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <tr v-for="transaction in branchTransactions" :key="transaction.id"
                                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-300">
                                                        {{ formatDate(transaction.transaction_date) }}
                                                    </td>
                                                    <td class="px-4 py-4 text-center">
                                                        <div
                                                            class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                                            {{ transaction.receive_quantity || '-' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center">
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ transaction.receipt_from_number ?
                                                                `${transaction.receipt_from_number} -
                                                            ${transaction.receipt_to_number}` :
                                                                '-' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center">
                                                        <div class="text-sm font-medium text-sky-600 dark:text-sky-400">
                                                            {{ transaction.given_quantity || '-' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center">
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ transaction.given_from_number ?
                                                                `${transaction.given_from_number} -
                                                            ${transaction.given_to_number}` :
                                                                '-' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center">
                                                        <div class="text-sm font-medium" :class="{
                                                            'text-emerald-600 dark:text-emerald-400': transaction.available_receipts > 100,
                                                            'text-yellow-600 dark:text-yellow-400': transaction.available_receipts <= 100 && transaction.available_receipts > 0,
                                                            'text-red-600 dark:text-red-400': transaction.available_receipts === 0
                                                        }">
                                                            {{ transaction.available_receipts }}
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center">
                                                        <button @click="confirmDelete(transaction.id)" class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 hover:text-red-900
                                               dark:text-red-400 dark:hover:text-red-300 rounded-md hover:bg-red-50
                                               dark:hover:bg-red-900/20 transition-colors">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <Modal :show="showDeleteModal" @close="closeDeleteModal" maxWidth="md">
                                <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl">
                                    <div class="p-6">
                                        <div class="flex items-center mb-4">
                                            <div
                                                class="flex-shrink-0 bg-red-100 dark:bg-red-900/30 rounded-full p-2 mr-3">
                                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                Confirm Delete
                                            </h3>
                                        </div>

                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                            Are you sure you want to delete this transaction? This action cannot be
                                            undone and will affect all subsequent balance calculations.
                                        </p>

                                        <div class="flex justify-end space-x-3">
                                            <SecondaryButton @click="closeDeleteModal"
                                                class="px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                                Cancel
                                            </SecondaryButton>
                                            <DangerButton @click="deleteTransaction" :disabled="deleting"
                                                class="px-4 py-2 bg-red-600 hover:bg-red-700 focus:ring-red-500">
                                                <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4" />
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                                </svg>
                                                {{ deleting ? 'Deleting...' : 'Delete Transaction' }}
                                            </DangerButton>
                                        </div>
                                    </div>
                                </div>
                            </Modal>
                        </Modal>

                        <!-- Report Generation Modal -->
                        <Modal :show="showReportModal" @close="closeReportModal" maxWidth="md">
                            <div class="p-6 dark:bg-gray-800">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Generate PDF Report
                                </h2>

                                <div class="space-y-4">
                                    <!-- Date Range -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <Label for="start_date" value="Start Date"
                                                class="text-gray-700 dark:text-gray-300" />
                                            <CustomDateInput v-model="reportForm.start_date" placeholder="dd/mm/yyyy"
                                                class="mt-1 block w-full" @update:modelValue="handleFilterChange" />
                                        </div>

                                        <div>
                                            <Label for="start_date" value="Start Date"
                                                class="text-gray-700 dark:text-gray-300" />
                                            <CustomDateInput v-model="reportForm.end_date" placeholder="dd/mm/yyyy"
                                                class="mt-1 block w-full" @update:modelValue="handleFilterChange" />
                                        </div>

                                    </div>

                                    <!-- Branch Selection -->
                                    <div>
                                        <Label value="Branch" class="text-gray-700 dark:text-gray-300" />
                                        <select v-model="reportForm.branch_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">All Branches</option>
                                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                                {{ branch.branch_name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Processing Indicator -->
                                    <div v-if="reportForm.processing" class="text-sm text-gray-500 dark:text-gray-400">
                                        Generating report, please wait...
                                    </div>

                                    <!-- Error Display -->
                                    <div v-if="reportForm.error" class="text-sm text-red-600 dark:text-red-400">
                                        {{ reportForm.error }}
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end space-x-3">
                                    <SecondaryButton @click="closeReportModal" :disabled="reportForm.processing">
                                        Cancel
                                    </SecondaryButton>
                                    <div v-if="reportForm.error"
                                        class="mt-4 mb-4 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-md">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-red-400 dark:text-red-500 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-red-600 dark:text-red-400">{{ reportForm.error
                                                }}</span>
                                        </div>
                                    </div>
                                    <PrimaryButton @click="generateReport"
                                        :disabled="!isReportFormValid || reportForm.processing"
                                        class="transition-all duration-200 ease-in-out">
                                        <div class="flex items-center">
                                            <svg v-if="reportForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            {{ reportForm.processing ? 'Generating...' : 'Generate PDF' }}
                                        </div>
                                    </PrimaryButton>
                                </div>
                            </div>
                        </Modal>

                        <div v-if="$page.props.flash.success"
                            class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div v-if="$page.props.flash.error"
                            class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 rounded">
                            {{ $page.props.flash.error }}
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4" v-if="receipts.links">
                            <Pagination :links="receipts.links" :data="receipts" class="dark:text-gray-300" />
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import Pagination from '@/Components/Pagination.vue';
import CustomDateInput from '@/Components/CustomDateInput.vue';
import Modal from '@/Components/Modal.vue';                   // Add this
import PrimaryButton from '@/Components/PrimaryButton.vue';   // Add this
import SecondaryButton from '@/Components/SecondaryButton.vue'; // Add this
import DangerButton from '@/Components/DangerButton.vue';


const props = defineProps({
    receipts: Object,
    branchSummaries: Array,
    branches: Array,
    filters: Object
});

const showTransactionModal = ref(false);
const showDeleteModal = ref(false);
const selectedBranch = ref(null);
const branchTransactions = ref([]);
const selectedTransactionId = ref(null);
const deleting = ref(false);

// Filters state
const filters = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
    branch_id: props.filters.branch_id || ''
});

const formatNumber = (number) => {
    if (number === 0) return '0';
    if (!number) return '-';
    return number.toLocaleString('en-US', { maximumFractionDigits: 0 });
};

// Updated computed properties
const totalPeriodReceived = computed(() => {
    const total = props.branchSummaries.reduce((sum, branch) => {
        return sum + (Number(branch.period_received) || 0);
    }, 0);
    return formatNumber(total);
});

const totalPeriodDistributed = computed(() => {
    const total = props.branchSummaries.reduce((sum, branch) => {
        return sum + (Number(branch.period_distributed) || 0);
    }, 0);
    return formatNumber(total);
});

const totalAvailable = computed(() => {
    const total = props.branchSummaries.reduce((sum, branch) => {
        return sum + (Number(branch.current_available) || 0);
    }, 0);
    return formatNumber(total);
});

const viewBranchDetails = async (branchId) => {
    try {
        const response = await fetch(route('payment-receipts.branch-transactions', { branch: branchId }), {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch branch transactions');
        }

        const data = await response.json();
        branchTransactions.value = data.transactions;
        selectedBranch.value = props.branches.find(b => b.id === branchId);
        showTransactionModal.value = true;
    } catch (error) {
        console.error('Error fetching branch transactions:', error);
        // Handle error (show notification, etc.)
    }
};

const closeTransactionModal = () => {
    showTransactionModal.value = false;
    selectedBranch.value = null;
    branchTransactions.value = [];
};
const confirmDelete = (transactionId) => {
    selectedTransactionId.value = transactionId;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedTransactionId.value = null;
};

const deleteTransaction = () => {
    if (!selectedTransactionId.value) return;

    deleting.value = true;

    router.delete(route('payment-receipts.destroy', { receipt: selectedTransactionId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
            // Refresh the branch transactions
            viewBranchDetails(selectedBranch.value.id);
        },
        onError: (errors) => {
            console.error('Error deleting transaction:', errors);
        },
        onFinish: () => {
            deleting.value = false;
        }
    });
};


// Methods
const handleFilterChange = debounce(() => {
    router.get(route('payment-receipts.index'), {
        start_date: filters.value.start_date,
        end_date: filters.value.end_date,
        branch_id: filters.value.branch_id
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['receipts', 'branchSummaries']
    });
}, 300);

const downloadPDF = () => {
    const params = new URLSearchParams({
        start_date: filters.value.start_date,
        end_date: filters.value.end_date,
        branch_id: filters.value.branch_id || '',
        download: 'pdf'
    }).toString();

    window.location.href = `${route('payment-receipts.export')}?${params}`;
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
    if (available < 500) return 'Moderate Stock';
    return 'Good Stock';
};



const viewDetails = (receiptId) => {
    // Implement view details logic
    console.log('Viewing receipt:', receiptId);
};

const showReportModal = ref(false);

const reportForm = reactive({
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
    branch_id: props.filters.branch_id || '',
    processing: false,
    error: null
});

const isReportFormValid = computed(() => {
    if (!reportForm.start_date || !reportForm.end_date) {
        return false;
    }

    // Convert dates to comparable format
    const startDate = new Date(reportForm.start_date);
    const endDate = new Date(reportForm.end_date);

    // Validate dates
    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
        return false;
    }

    return endDate >= startDate;
});

const openReportModal = () => {
    reportForm.start_date = props.filters.start_date;
    reportForm.end_date = props.filters.end_date;
    reportForm.branch_id = props.filters.branch_id || '';
    reportForm.error = null;
    showReportModal.value = true;
};

const closeReportModal = () => {
    if (!reportForm.processing) {
        showReportModal.value = false;
        reportForm.error = null;
    }
};

const generateReport = async () => {
    if (!isReportFormValid.value) return;

    reportForm.processing = true;
    reportForm.error = null;

    try {
        const params = new URLSearchParams({
            start_date: reportForm.start_date,
            end_date: reportForm.end_date,
            ...(reportForm.branch_id && { branch_id: reportForm.branch_id })
        });

        // First check if report can be generated
        const response = await fetch(route('payment-receipts.report') + '?' + params.toString(), {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.error || 'Failed to generate report');
        }

        // If successful, trigger download
        window.location.href = route('payment-receipts.report') + '?' + params.toString();

        setTimeout(() => {
            reportForm.processing = false;
            closeReportModal();
        }, 1000);

    } catch (error) {
        reportForm.error = error.message;
        reportForm.processing = false;
    }
};



// Initialize default dates on mount
onMounted(() => {
    if (!filters.value.start_date || !filters.value.end_date) {
        const today = new Date();
        filters.value.start_date = new Date(today.getFullYear(), today.getMonth(), 1)
            .toISOString().split('T')[0];
        filters.value.end_date = new Date(today.getFullYear(), today.getMonth() + 1, 0)
            .toISOString().split('T')[0];
        handleFilterChange();
    }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Dark mode transition */
.dark-transition {
    transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

/* Hover effects */
.hover\:transform-scale {
    transition: transform 0.2s ease;
}

.hover\:transform-scale:hover {
    transform: scale(1.01);
}
</style>
