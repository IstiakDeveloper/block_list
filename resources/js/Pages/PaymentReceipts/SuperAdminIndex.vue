<template>
    <AdminLayout title="Payment Receipts Overview">
        <div class="py-6 dark:bg-gray-900">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <!-- Header with Download -->
                <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="flex items-center justify-between p-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            All Branches Overview
                        </h2>
                        <div class="flex gap-2">
                            <button @click="openReportModal"
                                class="inline-flex items-center px-4 py-2 font-semibold text-white transition-colors duration-200 bg-green-600 rounded-md dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Generate Report
                            </button>

                            <!-- Add New Entry Button for Super Admin -->
                            <button @click="openNewEntryModal"
                                class="inline-flex items-center px-4 py-2 font-semibold text-white transition-colors duration-200 bg-blue-600 rounded-md dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add New Entry
                            </button>
                        </div>




                    </div>
                </div>

                <!-- Filters -->
                <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- Date Range -->
                        <!-- Start Date -->
                        <div>
                            <Label for="start_date" value="Start Date" class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="filters.start_date" placeholder="dd/mm/yyyy"
                                class="block w-full mt-1" @update:modelValue="handleFilterChange" />
                        </div>

                        <!-- End Date -->
                        <div>
                            <Label for="end_date" value="End Date" class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="filters.end_date" placeholder="dd/mm/yyyy"
                                class="block w-full mt-1" @update:modelValue="handleFilterChange" />
                        </div>
                        <!-- Branch Filter -->
                        <div>
                            <Label value="Branch" class="text-gray-700 dark:text-gray-300" />
                            <select v-model="filters.branch_id" @change="handleFilterChange"
                                class="block w-full mt-1 border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Branches</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.branch_name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Branch Summary Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="summary in branchSummaries" :key="summary.branch_id"
                        class="p-6 transition-shadow bg-white rounded-lg shadow-sm dark:bg-gray-800 hover:shadow-md">
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
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">
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
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">
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
                            <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900">
                                <div class="flex items-center justify-between">
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
                <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                        Overall Summary
                    </h3>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                        <!-- Total Period Received -->
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Period Received</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ totalPeriodReceived }}
                            </div>
                        </div>

                        <!-- Total Period Disbursement -->
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Period Disbursement</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ totalPeriodDistributed }}
                            </div>
                        </div>

                        <!-- Total Balance -->
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Balance Receipts</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ totalAvailable }}
                            </div>
                        </div>

                        <!-- Total Branches -->
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Active Branches</div>
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
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
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Branch
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Branch Code
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-emerald-600 dark:text-emerald-400">
                                            Received
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-sky-600 dark:text-sky-400">
                                            Disbursement
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Balance
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Period Stats
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="summary in branchSummaries" :key="summary.branch_id"
                                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
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
                                            <div class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                                {{ summary.all_time_received }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                All Time
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-sky-600 dark:text-sky-400">
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
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <button @click="viewBranchDetails(summary.branch_id)"
                                                class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                <span>View Details</span>
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
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


                        <Modal :show="showTransactionModal" @close="closeTransactionModal" maxWidth="5xl">
                            <div class="flex flex-col bg-white rounded-lg shadow-xl dark:bg-gray-800">
                                <div
                                    class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ selectedBranch?.branch_name }} - Transaction Details
                                    </h2>
                                    <button @click="closeTransactionModal"
                                        class="p-2 text-gray-400 transition-colors rounded-full hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <span class="sr-only">Close</span>
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                                        class="w-32 px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                        Date
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-xs font-medium tracking-wider text-center uppercase text-emerald-600 dark:text-emerald-400">
                                                        Received
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-xs font-medium tracking-wider text-center uppercase text-emerald-600 dark:text-emerald-400">
                                                        From - To
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-xs font-medium tracking-wider text-center uppercase text-emerald-600 dark:text-emerald-400">
                                                        Received By
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-xs font-medium tracking-wider text-center uppercase text-sky-600 dark:text-sky-400">
                                                        Disbursed
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-xs font-medium tracking-wider text-center uppercase text-sky-600 dark:text-sky-400">
                                                        From - To
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-xs font-medium tracking-wider text-center uppercase text-sky-600 dark:text-sky-400">
                                                        Given To
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                                        Balance
                                                    </th>
                                                    <th
                                                        class="w-24 px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                                <tr v-for="transaction in branchTransactions" :key="transaction.id"
                                                    class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
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
                                                            {{ transaction.received_by || '-' }}
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
                                                        <div class="text-sm font-medium text-sky-600 dark:text-sky-400">
                                                            {{ transaction.given_to || '-' }}
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
                                                        <div class="flex justify-center space-x-2">
                                                            <button @click="editTransaction(transaction)"
                                                                class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 transition-colors rounded-md hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                                <svg class="w-4 h-4 mr-1.5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Edit
                                                            </button>
                                                            <button @click="confirmDelete(transaction.id)"
                                                                class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 transition-colors rounded-md hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                                <svg class="w-4 h-4 mr-1.5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </Modal>

                        <!-- Delete Confirmation Modal (Separate from Transaction Modal) -->
                        <Modal :show="showDeleteModal" @close="closeDeleteModal" maxWidth="md">
                            <div class="overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
                                <div class="p-6">
                                    <div class="flex items-center mb-4">
                                        <div class="flex-shrink-0 p-2 mr-3 bg-red-100 rounded-full dark:bg-red-900/30">
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            Confirm Delete
                                        </h3>
                                    </div>

                                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
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
                                            <svg v-if="deleting" class="w-4 h-4 mr-2 -ml-1 text-white animate-spin"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                            </svg>
                                            {{ deleting ? 'Deleting...' : 'Delete Transaction' }}
                                        </DangerButton>
                                    </div>
                                </div>
                            </div>
                        </Modal>

                        <!-- Report Generation Modal -->
                        <Modal :show="showReportModal" @close="closeReportModal" maxWidth="md">
                            <div class="p-6 dark:bg-gray-800">
                                <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Generate PDF Report
                                </h2>

                                <div class="space-y-4">
                                    <!-- Date Range -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <Label for="start_date" value="Start Date"
                                                class="text-gray-700 dark:text-gray-300" />
                                            <CustomDateInput v-model="reportForm.start_date" placeholder="dd/mm/yyyy"
                                                class="block w-full mt-1" @update:modelValue="handleFilterChange" />
                                        </div>

                                        <div>
                                            <Label for="start_date" value="Start Date"
                                                class="text-gray-700 dark:text-gray-300" />
                                            <CustomDateInput v-model="reportForm.end_date" placeholder="dd/mm/yyyy"
                                                class="block w-full mt-1" @update:modelValue="handleFilterChange" />
                                        </div>

                                    </div>

                                    <!-- Branch Selection -->
                                    <div>
                                        <Label value="Branch" class="text-gray-700 dark:text-gray-300" />
                                        <select v-model="reportForm.branch_id"
                                            class="block w-full mt-1 border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
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

                                <div class="flex justify-end mt-6 space-x-3">
                                    <SecondaryButton @click="closeReportModal" :disabled="reportForm.processing">
                                        Cancel
                                    </SecondaryButton>
                                    <div v-if="reportForm.error"
                                        class="p-4 mt-4 mb-4 border border-red-200 rounded-md bg-red-50 dark:bg-red-900/50 dark:border-red-800">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-red-400 dark:text-red-500" fill="none"
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
                                            <svg v-if="reportForm.processing" class="w-4 h-4 mr-2 -ml-1 animate-spin"
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
                            class="p-4 mb-4 text-green-700 bg-green-100 border border-green-400 rounded dark:bg-green-900/30 dark:border-green-600 dark:text-green-300">
                            {{ $page.props.flash.success }}
                        </div>

                        <div v-if="$page.props.flash.error"
                            class="p-4 mb-4 text-red-700 bg-red-100 border border-red-400 rounded dark:bg-red-900/30 dark:border-red-600 dark:text-red-300">
                            {{ $page.props.flash.error }}
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4" v-if="receipts.links">
                            <Pagination :links="receipts.links" :data="receipts" class="dark:text-gray-300" />
                        </div>
                    </div>
                </div>

                <Modal :show="showNewEntryModal" @close="closeNewEntryModal" maxWidth="2xl">
                    <div class="p-6 dark:bg-gray-800">
                        <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                            New Receipt Entry
                        </h2>

                        <!-- Error Message Display -->
                        <div v-if="Object.keys(form.errors).length > 0"
                            class="p-4 mb-4 border border-red-400 rounded bg-red-50 dark:bg-red-900/30 dark:border-red-500">
                            <div v-for="(error, key) in form.errors" :key="key"
                                class="text-sm text-red-600 dark:text-red-400">
                                {{ error }}
                            </div>
                        </div>

                        <form @submit.prevent="submitForm">
                            <!-- Branch Selection (Super Admin Only) -->
                            <div class="mb-4">
                                <Label for="branch_id" value="Select Branch" required
                                    class="text-gray-700 dark:text-gray-300" />
                                <select id="branch_id" v-model="form.branch_id" :error="form.errors.branch_id" required
                                    class="block w-full mt-1 border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select a branch</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.branch_name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.branch_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.branch_id }}
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="mb-4">
                                <Label for="transaction_date" value="Date" required
                                    class="text-gray-700 dark:text-gray-300" />
                                <CustomDateInput v-model="form.transaction_date" placeholder="dd/mm/yyyy"
                                    class="block w-full mt-1" required />
                            </div>

                            <!-- Receive Section -->
                            <div class="pt-4 mb-6 border-t dark:border-gray-700">
                                <h3 class="mb-4 font-medium text-green-600 text-md dark:text-green-400">
                                    H/O Receive Section
                                </h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <Label for="receive_quantity" value="Quantity"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="receive_quantity" type="number" v-model="form.receive_quantity"
                                            :error="form.errors.receive_quantity"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="0" @input="validateReceiveSection" />
                                    </div>
                                    <div>
                                        <Label for="received_by" value="Received By"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="received_by" type="text" v-model="form.received_by"
                                            :error="form.errors.received_by"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                    </div>
                                    <div>
                                        <Label for="receipt_from_number" value="From Number"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="receipt_from_number" type="number" v-model="form.receipt_from_number"
                                            :error="form.errors.receipt_from_number"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="1" />
                                    </div>
                                    <div>
                                        <Label for="receipt_to_number" value="To Number"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="receipt_to_number" type="number" v-model="form.receipt_to_number"
                                            :error="form.errors.receipt_to_number"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="1" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6 space-x-3">
                                <SecondaryButton @click="closeNewEntryModal" type="button"
                                    class="dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton :disabled="form.processing || !isFormValid"
                                    class="dark:bg-blue-600 dark:hover:bg-blue-700">
                                    {{ form.processing ? 'Saving...' : 'Save Entry' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Modal>


                <!-- Edit Transaction Modal -->
                <Modal :show="showEditModal" @close="closeEditModal" maxWidth="2xl">
                    <div class="p-6 dark:bg-gray-800">
                        <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                            Edit Transaction
                        </h2>

                        <!-- Error Message Display -->
                        <div v-if="Object.keys(editForm.errors).length > 0"
                            class="p-4 mb-4 border border-red-400 rounded bg-red-50 dark:bg-red-900/30 dark:border-red-500">
                            <div v-for="(error, key) in editForm.errors" :key="key"
                                class="text-sm text-red-600 dark:text-red-400">
                                {{ error }}
                            </div>
                        </div>

                        <form @submit.prevent="updateTransaction">
                            <!-- Date -->
                            <div class="mb-4">
                                <Label for="edit_transaction_date" value="Date"
                                    class="text-gray-700 dark:text-gray-300" />
                                <CustomDateInput v-model="editForm.transaction_date" placeholder="dd/mm/yyyy"
                                    class="block w-full mt-1" />
                            </div>

                            <!-- Receive Section -->
                            <div class="pt-4 mb-6 border-t dark:border-gray-700">
                                <h3 class="mb-4 font-medium text-green-600 text-md dark:text-green-400">
                                    H/O Receive Section
                                </h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <Label for="edit_receive_quantity" value="Quantity"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_receive_quantity" type="number"
                                            v-model="editForm.receive_quantity"
                                            :error="editForm.errors.receive_quantity"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="0" />
                                    </div>
                                    <div>
                                        <Label for="edit_received_by" value="Received By"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_received_by" type="text" v-model="editForm.received_by"
                                            :error="editForm.errors.received_by"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                    </div>
                                    <div>
                                        <Label for="edit_receipt_from_number" value="From Number"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_receipt_from_number" type="number"
                                            v-model="editForm.receipt_from_number"
                                            :error="editForm.errors.receipt_from_number"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="1" />
                                    </div>
                                    <div>
                                        <Label for="edit_receipt_to_number" value="To Number"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_receipt_to_number" type="number"
                                            v-model="editForm.receipt_to_number"
                                            :error="editForm.errors.receipt_to_number"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="1" />
                                    </div>
                                </div>
                            </div>

                            <!-- Distribution Section -->
                            <div class="pt-4 mb-6 border-t dark:border-gray-700">
                                <h3 class="mb-4 font-medium text-blue-600 text-md dark:text-blue-400">
                                    Disbursement Section
                                </h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <Label for="edit_given_quantity" value="Disburse Quantity"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_given_quantity" type="number" v-model="editForm.given_quantity"
                                            :error="editForm.errors.given_quantity"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="0" />
                                    </div>
                                    <div>
                                        <Label for="edit_given_to" value="Given To"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_given_to" type="text" v-model="editForm.given_to"
                                            :error="editForm.errors.given_to"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                    </div>
                                    <div>
                                        <Label for="edit_pin_number" value="PIN Number"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_pin_number" type="text" v-model="editForm.pin_number"
                                            :error="editForm.errors.pin_number"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                    </div>
                                    <div>
                                        <Label for="edit_given_from_number" value="From Number"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_given_from_number" type="number"
                                            v-model="editForm.given_from_number"
                                            :error="editForm.errors.given_from_number"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="1" />
                                    </div>
                                    <div>
                                        <Label for="edit_given_to_number" value="To Number"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_given_to_number" type="number"
                                            v-model="editForm.given_to_number" :error="editForm.errors.given_to_number"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                            min="1" />
                                    </div>
                                    <div>
                                        <Label for="edit_receipt_book_number" value="Book Number"
                                            class="text-gray-700 dark:text-gray-300" />
                                        <Input id="edit_receipt_book_number" type="text"
                                            v-model="editForm.receipt_book_number"
                                            :error="editForm.errors.receipt_book_number"
                                            class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <SecondaryButton @click="closeEditModal" type="button"
                                    class="dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton :disabled="editForm.processing"
                                    class="dark:bg-blue-600 dark:hover:bg-blue-700">
                                    {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Modal>
            </div>
        </div>




    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
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
const showNewEntryModal = ref(false);
const showEditModal = ref(false);
const selectedTransaction = ref(null);


// Filters state
const filters = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
    branch_id: props.filters.branch_id || ''
});

const form = useForm({
    branch_id: '', // Add this for super admin
    transaction_date: new Date().toISOString().split('T')[0],
    receive_quantity: 0,
    receipt_from_number: null,
    receipt_to_number: null,
    received_by: '',
    given_to: '',
    pin_number: '',
    given_from_number: null,
    given_to_number: null,
    receipt_book_number: '',
    given_quantity: 0
});

const isFormValid = computed(() => {
    return form.branch_id && form.receive_quantity > 0;
});

// Modal handlers
const openNewEntryModal = () => {
    form.reset();
    form.transaction_date = new Date().toISOString().split('T')[0];
    form.clearErrors();
    showNewEntryModal.value = true;
};

const closeNewEntryModal = () => {
    form.reset();
    form.clearErrors();
    showNewEntryModal.value = false;
};

// Form validation methods
const validateReceiveSection = () => {
    if (form.receive_quantity > 0) {
        // Validation logic if needed
    }
};

// Form submission
const submitForm = () => {
    form.post(route('payment-receipts.store-admin'), {
        preserveScroll: true,
        onSuccess: () => {
            closeNewEntryModal();
            // Refresh the data
            handleFilterChange();
        },
        onError: (errors) => {
            console.error('Form submission failed:', errors);
        }
    });
};


const editForm = useForm({
    id: null,
    transaction_date: '',
    receive_quantity: 0,
    receipt_from_number: null,
    receipt_to_number: null,
    received_by: '',
    given_quantity: 0,
    given_to: '',
    pin_number: '',
    given_from_number: null,
    given_to_number: null,
    receipt_book_number: '',
});

const editTransaction = (transaction) => {
    selectedTransaction.value = transaction;

    // Get the original date string
    const originalDate = transaction.transaction_date;

    // Add 1 day to the date to compensate for timezone issue
    let formattedDate;

    if (originalDate && originalDate.includes('-')) {
        // If it's in YYYY-MM-DD format (or YYYY-MM-DDT... format)
        const datePart = originalDate.includes('T') ? originalDate.split('T')[0] : originalDate;
        const [year, month, day] = datePart.split('-').map(Number);

        // Create a date and add 1 day
        const date = new Date(year, month - 1, day);
        date.setDate(date.getDate() + 1);

        // Format back to YYYY-MM-DD
        const adjustedYear = date.getFullYear();
        const adjustedMonth = String(date.getMonth() + 1).padStart(2, '0');
        const adjustedDay = String(date.getDate()).padStart(2, '0');

        formattedDate = `${adjustedYear}-${adjustedMonth}-${adjustedDay}`;
    } else {
        // Fallback to the original date
        formattedDate = originalDate;
    }

    console.log('Original transaction date:', originalDate);
    console.log('Adjusted date for form:', formattedDate);

    // Populate form with transaction data
    editForm.reset();
    editForm.clearErrors();

    editForm.id = transaction.id;
    editForm.transaction_date = formattedDate;
    editForm.receive_quantity = transaction.receive_quantity || 0;
    editForm.receipt_from_number = transaction.receipt_from_number;
    editForm.receipt_to_number = transaction.receipt_to_number;
    editForm.received_by = transaction.received_by || '';
    editForm.given_quantity = transaction.given_quantity || 0;
    editForm.given_to = transaction.given_to || '';
    editForm.pin_number = transaction.pin_number || '';
    editForm.given_from_number = transaction.given_from_number;
    editForm.given_to_number = transaction.given_to_number;
    editForm.receipt_book_number = transaction.receipt_book_number || '';

    showEditModal.value = true;
};

// Add this helper function
const formatDateForInput = (dateString) => {
    if (!dateString) return '';

    try {
        // Handle ISO format (with T)
        if (dateString.includes('T')) {
            dateString = dateString.split('T')[0];
        }

        // If it's already in YYYY-MM-DD format
        if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
            return dateString;
        }

        // Parse the date using Date object
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            console.error('Invalid date:', dateString);
            return '';
        }

        // Format as YYYY-MM-DD
        return date.toISOString().split('T')[0];
    } catch (error) {
        console.error('Error formatting date:', error);
        return '';
    }
};

const closeEditModal = () => {
    showEditModal.value = false;
    selectedTransaction.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const updateTransaction = () => {
    if (!editForm.id) {
        console.error('No transaction ID found');
        return;
    }

    console.log('Updating transaction with ID:', editForm.id);

    editForm.put(route('payment-receipts.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal();
            // Refresh the branch transactions
            if (selectedBranch.value && selectedBranch.value.id) {
                viewBranchDetails(selectedBranch.value.id);
            }
        },
        onError: (errors) => {
            console.error('Error updating transaction:', errors);
        }
    });
};

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
        // Build query string with date parameters
        const params = new URLSearchParams();
        if (filters.value.start_date) {
            params.append('start_date', filters.value.start_date);
        }
        if (filters.value.end_date) {
            params.append('end_date', filters.value.end_date);
        }
        // Add a timestamp to prevent caching
        params.append('_t', Date.now());

        // Construct URL with parameters
        const url = `${route('payment-receipts.branch-transactions', { branch: branchId })}?${params.toString()}`;

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
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
