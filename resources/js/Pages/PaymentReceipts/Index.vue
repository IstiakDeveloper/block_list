<template>
    <AdminLayout title="Payment Receipts">
        <div class="py-6 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Header Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <!-- Desktop View -->
                        <div class="hidden sm:flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                Receipts Payment Management
                            </h2>
                            <div class="flex gap-2">
                                <!-- Download Button -->
                                <button @click="downloadPDF"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 text-white font-semibold rounded-md hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download PDF
                                </button>

                                <!-- Add New Entry Button -->
                                <button @click="openNewEntryModal"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white font-semibold rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add New Entry
                                </button>
                            </div>
                        </div>

                        <!-- Mobile View -->
                        <div class="sm:hidden space-y-4">
                            <!-- Title -->
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 text-center mb-4">
                                Payment Receipts
                            </h2>

                            <!-- Mobile Action Buttons -->
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Download Button -->
                                <button @click="downloadPDF"
                                    class="flex items-center justify-center px-3 py-2 bg-green-600 dark:bg-green-700 text-white text-sm font-medium rounded-md hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download
                                </button>

                                <!-- Add New Entry Button -->
                                <button @click="openNewEntryModal"
                                    class="flex items-center justify-center px-3 py-2 bg-blue-600 dark:bg-blue-700 text-white text-sm font-medium rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    New Entry
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

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

                        <!-- Branch Selector (Super Admin Only) -->
                        <div v-if="isSuperAdmin">
                            <Label for="branch_id" value="Branch" class="text-gray-700 dark:text-gray-300" />
                            <select id="branch_id" v-model="filters.branch_id" @change="handleFilterChange"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Branches</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.branch_name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Status Message -->
                <div v-if="$page.props.flash.success"
                    class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                </div>

                <!-- Branch Summaries (Super Admin) -->
                <div v-if="isSuperAdmin" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="summary in branchSummaries" :key="summary.branch_id"
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ summary.branch_name }}
                        </h3>

                        <!-- Current Period Stats -->
                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Current Period
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
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
                                </div>
                            </div>

                            <!-- All Time Stats -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Cumulative</h4>
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

                            <!-- Available Receipts -->
                            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                                <div class="text-sm text-blue-600 dark:text-blue-200">Current Balance</div>
                                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                    {{ summary.current_available }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Single Branch Summary -->
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Three Boxes in Line for Current Period -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Current Period</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Received</div>
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ branchSummaries.period_received }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Disbursement</div>
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ branchSummaries.period_distributed }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Balance in Period</div>
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ branchSummaries.period_received - branchSummaries.period_distributed }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Three Boxes in Line for All Time -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Cumulative</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Received</div>
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ branchSummaries.all_time_received }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Disbursement</div>
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ branchSummaries.all_time_distributed }}
                                    </div>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="text-sm text-blue-600 dark:text-blue-200">Current Balance
                                            </div>
                                            <div class="text-3xl font-bold text-blue-700 dark:text-blue-300">
                                                {{ branchSummaries.current_available }}
                                            </div>
                                        </div>
                                        <div :class="{
                                            'text-green-600 dark:text-green-400': branchSummaries.current_available > 500,
                                            'text-yellow-600 dark:text-yellow-400': branchSummaries.current_available <= 500 && branchSummaries.current_available > 100,
                                            'text-red-600 dark:text-red-400': branchSummaries.current_available <= 100
                                        }" class="text-sm font-medium">
                                            {{ branchSummaries.current_available > 500 ? 'Good Stock' :
                                                branchSummaries.current_available > 100 ? 'Moderate Stock' : 'Low Stock' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Currently Available Box in Line with Status Indicator -->
                        <div class="mt-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="table-wrapper desktop-only">
                            <table
                                class="receipt-table min-w-full divide-y divide-gray-300 dark:divide-gray-600 border border-gray-300 dark:border-gray-600">
                                <thead>
                                    <tr
                                        class="divide-x divide-gray-300 dark:divide-gray-600 border-b border-gray-300 dark:border-gray-600">
                                        <th scope="col" colspan="1"
                                            class="date-col px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700 border-r border-gray-300 dark:border-gray-600">
                                        </th>
                                        <th scope="col" colspan="5"
                                            class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider bg-emerald-50 dark:bg-emerald-900/30 border-r border-gray-300 dark:border-gray-600">
                                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">★ Receive
                                                Section ★</span>
                                        </th>
                                        <th scope="col" colspan="6"
                                            class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider bg-sky-50 dark:bg-sky-900/30">
                                            <span class="text-sky-600 dark:text-sky-400 font-bold">◆ Disbursement
                                                Section ◆</span>
                                        </th>
                                    </tr>
                                    <tr
                                        class="bg-gray-50 dark:bg-gray-700 divide-x divide-gray-300 dark:divide-gray-600">
                                        <th
                                            class="date-col px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border-r border-gray-300 dark:border-gray-600">
                                            Date
                                        </th>
                                        <th
                                            class="qty-col px-2 py-2 text-center text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider bg-emerald-50/50 dark:bg-emerald-900/20 w-16">
                                            Qty
                                        </th>
                                        <th
                                            class="number-col px-2 py-2 text-center text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider bg-emerald-50/50 dark:bg-emerald-900/20 w-20">
                                            From
                                        </th>
                                        <th
                                            class="number-col px-2 py-2 text-center text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider bg-emerald-50/50 dark:bg-emerald-900/20 w-20">
                                            To
                                        </th>
                                        <th
                                            class="total-col px-2 py-2 text-center text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider bg-emerald-50/50 dark:bg-emerald-900/20 w-20">
                                            Total
                                        </th>
                                        <th
                                            class="name-col px-1 py-2 text-left text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider bg-emerald-50/50 dark:bg-emerald-900/20 border-r border-gray-300 dark:border-gray-600">
                                            Received By
                                        </th>
                                        <th
                                            class="name-col px-1 py-2 text-left text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider bg-sky-50/50 dark:bg-sky-900/20">
                                            Disburse To
                                        </th>
                                        <th
                                            class="pin-col px-1 py-2 text-center text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider bg-sky-50/50 dark:bg-sky-900/20 w-4">
                                            PIN
                                        </th>
                                        <th
                                            class="number-col px-2 py-2 text-center text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider bg-sky-50/50 dark:bg-sky-900/20 w-20">
                                            From
                                        </th>
                                        <th
                                            class="number-col px-2 py-2 text-center text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider bg-sky-50/50 dark:bg-sky-900/20 w-20">
                                            To
                                        </th>
                                        <th
                                            class="book-col px-1 py-2 text-center text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider bg-sky-50/50 dark:bg-sky-900/20 w-4">
                                            Book
                                        </th>
                                        <th
                                            class="available-col px-2 py-2 text-center text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider bg-sky-50/50 dark:bg-sky-900/20 w-20">
                                            Balance
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-600">
                                    <tr v-for="receipt in receipts.data" :key="receipt.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors divide-x divide-gray-300 dark:divide-gray-600">
                                        <td
                                            class="text-center py-2 text-sm text-gray-900 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                            {{ formatDate(receipt.transaction_date) }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-sm text-emerald-600 dark:text-emerald-400 font-medium text-center bg-emerald-50/30 dark:bg-emerald-900/10">
                                            {{ receipt.receive_quantity || '-' }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-sm text-gray-600 dark:text-gray-400 text-center bg-emerald-50/30 dark:bg-emerald-900/10">
                                            {{ receipt.receipt_from_number || '-' }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-sm text-gray-600 dark:text-gray-400 text-center bg-emerald-50/30 dark:bg-emerald-900/10">
                                            {{ receipt.receipt_to_number || '-' }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-sm font-medium text-gray-900 dark:text-gray-300 text-center bg-emerald-50/30 dark:bg-emerald-900/10">
                                            {{ receipt.total_cumulative_quantity }}
                                        </td>
                                        <td
                                            class="px-1 py-2 text-sm text-gray-600 dark:text-gray-400 text-left bg-emerald-50/30 dark:bg-emerald-900/10 border-r border-gray-300 dark:border-gray-600">
                                            {{ receipt.received_by || '-' }}
                                        </td>
                                        <td
                                            class="px-1 py-2 text-sm text-sky-600 dark:text-sky-400 font-medium text-center bg-sky-50/30 dark:bg-sky-900/10">
                                            {{ receipt.given_to || '-' }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-sm text-gray-600 dark:text-gray-400 text-center bg-sky-50/30 dark:bg-sky-900/10">
                                            {{ receipt.pin_number || '-' }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-sm text-gray-600 dark:text-gray-400 text-center bg-sky-50/30 dark:bg-sky-900/10">
                                            {{ receipt.given_from_number || '-' }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-sm text-gray-600 dark:text-gray-400 text-center bg-sky-50/30 dark:bg-sky-900/10">
                                            {{ receipt.given_to_number || '-' }}
                                        </td>
                                        <td
                                            class="px-2 py-2 text-sm text-gray-600 dark:text-gray-400 text-center bg-sky-50/30 dark:bg-sky-900/10">
                                            {{ receipt.receipt_book_number || '-' }}
                                        </td>
                                        <td class="px-2 py-2 text-sm text-center font-medium bg-sky-50/30 dark:bg-sky-900/10"
                                            :class="{
                                                'text-emerald-600 dark:text-emerald-400': receipt.available_receipts > 100,
                                                'text-yellow-600 dark:text-yellow-400': receipt.available_receipts <= 100 && receipt.available_receipts > 0,
                                                'text-red-600 dark:text-red-400': receipt.available_receipts === 0
                                            }">
                                            {{ receipt.available_receipts }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                        <div class="mobile-only">
                            <div class="space-y-4">
                                <div v-for="receipt in receipts.data" :key="receipt.id"
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                                    <!-- Date Header -->
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 flex justify-between items-center">
                                        <div class="text-gray-900 dark:text-gray-100 font-medium">
                                            {{ formatDate(receipt.transaction_date) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            #{{ receipt.id }}
                                        </div>
                                    </div>

                                    <!-- Receive Section -->
                                    <div class="receipt-section-header receive-section">
                                        Receive Details
                                    </div>
                                    <div class="p-4 space-y-3 bg-emerald-50/20 dark:bg-emerald-900/10">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Quantity</span>
                                            <span class="font-medium text-emerald-600 dark:text-emerald-400">
                                                {{ receipt.receive_quantity || '-' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">From - To</span>
                                            <span class="text-sm">
                                                {{ receipt.receipt_from_number || '-' }} - {{ receipt.receipt_to_number
                                                    || '-' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Received By</span>
                                            <span class="text-sm">{{ receipt.received_by || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                                            <span class="font-medium">{{ receipt.total_cumulative_quantity }}</span>
                                        </div>
                                    </div>

                                    <!-- Distribution Section -->
                                    <div class="receipt-section-header distribute-section">
                                        Disbursement Details
                                    </div>
                                    <div class="p-4 space-y-3 bg-sky-50/20 dark:bg-sky-900/10">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Disburse To</span>
                                            <span class="font-medium text-sky-600 dark:text-sky-400">
                                                {{ receipt.given_to || '-' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">PIN</span>
                                            <span class="text-sm">{{ receipt.pin_number || '-' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">From - To</span>
                                            <span class="text-sm">
                                                {{ receipt.given_from_number || '-' }} - {{ receipt.given_to_number ||
                                                    '-' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Book Number</span>
                                            <span class="text-sm">{{ receipt.receipt_book_number || '-' }}</span>
                                        </div>
                                    </div>

                                    <!-- Available Receipts -->
                                    <div class="p-4 border-t dark:border-gray-700">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                                Balance
                                            </span>
                                            <span class="text-lg font-bold" :class="{
                                                'text-emerald-600 dark:text-emerald-400': receipt.available_receipts > 100,
                                                'text-yellow-600 dark:text-yellow-400': receipt.available_receipts <= 100 && receipt.available_receipts > 0,
                                                'text-red-600 dark:text-red-400': receipt.available_receipts === 0
                                            }">
                                                {{ receipt.available_receipts }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4" v-if="receipts.links">
                            <Pagination :links="receipts.links" class="dark:text-gray-300" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Entry Modal -->
        <Modal :show="showNewEntryModal" @close="closeNewEntryModal" maxWidth="2xl">
            <div class="p-6 dark:bg-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    New Receipt Entry
                </h2>

                <!-- Error Message Display -->
                <div v-if="Object.keys(form.errors).length > 0"
                    class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-400 dark:border-red-500 rounded">
                    <div v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600 dark:text-red-400">
                        {{ error }}
                    </div>
                </div>

                <form @submit.prevent="submitForm">
                    <!-- Date -->
                    <div class="mb-4">
                        <Label for="transaction_date" value="Date" required class="text-gray-700 dark:text-gray-300" />
                        <CustomDateInput v-model="form.transaction_date" placeholder="dd/mm/yyyy"
                            class="mt-1 block w-full" required />
                    </div>

                    <!-- Receive Section -->
                    <div class="mb-6 border-t dark:border-gray-700 pt-4">
                        <h3 class="text-md font-medium text-green-600 dark:text-green-400 mb-4">
                            H/O Receive Section
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="receive_quantity" value="Quantity"
                                    class="text-gray-700 dark:text-gray-300" />
                                <Input id="receive_quantity" type="number" v-model="form.receive_quantity"
                                    :error="form.errors.receive_quantity"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    min="0" @input="validateReceiveSection" />
                            </div>
                            <div>
                                <Label for="received_by" value="Received By" class="text-gray-700 dark:text-gray-300" />
                                <Input id="received_by" type="text" v-model="form.received_by"
                                    :error="form.errors.received_by"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    :required="!!form.receive_quantity" />
                            </div>
                            <div>
                                <Label for="receipt_from_number" value="From Number"
                                    class="text-gray-700 dark:text-gray-300" />
                                <Input id="receipt_from_number" type="number" v-model="form.receipt_from_number"
                                    :error="form.errors.receipt_from_number"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    min="1" :required="!!form.receive_quantity" />
                            </div>
                            <div>
                                <Label for="receipt_to_number" value="To Number"
                                    class="text-gray-700 dark:text-gray-300" />
                                <Input id="receipt_to_number" type="number" v-model="form.receipt_to_number"
                                    :error="form.errors.receipt_to_number"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    min="1" :required="!!form.receive_quantity" />
                            </div>
                        </div>
                    </div>

                    <!-- Distribution Section -->
                    <div class="mb-6 border-t dark:border-gray-700 pt-4">
                        <h3 class="text-md font-medium text-blue-600 dark:text-blue-400 mb-4">
                            Disbursement Section
                        </h3>
                        <!-- Distribution Section Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Given Quantity -->
                            <div>
                                <Label for="given_quantity" value="Disburse Quantity"
                                    class="text-gray-700 dark:text-gray-300" />
                                <Input id="given_quantity" type="number" v-model="form.given_quantity"
                                    :error="form.errors.given_quantity"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    min="0" @input="validateDistributeSection" />
                            </div>

                            <!-- Given To -->
                            <div>
                                <Label for="given_to" value="Given To" class="text-gray-700 dark:text-gray-300" />
                                <Input id="given_to" type="text" v-model="form.given_to" :error="form.errors.given_to"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    :required="!!form.given_quantity" />
                            </div>

                            <!-- PIN Number -->
                            <div>
                                <Label for="pin_number" value="PIN Number" class="text-gray-700 dark:text-gray-300" />
                                <Input id="pin_number" type="text" v-model="form.pin_number"
                                    :error="form.errors.pin_number"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    :required="!!form.given_quantity" />
                            </div>

                            <!-- Given From Number -->
                            <div>
                                <Label for="given_from_number" value="From Number"
                                    class="text-gray-700 dark:text-gray-300" />
                                <Input id="given_from_number" type="number" v-model="form.given_from_number"
                                    :error="form.errors.given_from_number"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    min="1" :required="!!form.given_quantity" />
                            </div>

                            <!-- Given To Number -->
                            <div>
                                <Label for="given_to_number" value="To Number"
                                    class="text-gray-700 dark:text-gray-300" />
                                <Input id="given_to_number" type="number" v-model="form.given_to_number"
                                    :error="form.errors.given_to_number"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    min="1" :required="!!form.given_quantity" />
                            </div>

                            <!-- Receipt Book Number -->
                            <div>
                                <Label for="receipt_book_number" value="Book Number"
                                    class="text-gray-700 dark:text-gray-300" />
                                <Input id="receipt_book_number" type="text" v-model="form.receipt_book_number"
                                    :error="form.errors.receipt_book_number"
                                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                    :required="!!form.given_quantity" />
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
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import CustomDateInput from '@/Components/CustomDateInput.vue';
import Pagination from '@/Components/Pagination.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// Props
const props = defineProps({
    receipts: Object,
    branchSummaries: [Object, Array],
    branches: Array,
    filters: Object,
    isSuperAdmin: Boolean
});

// State
const showNewEntryModal = ref(false);
const processing = ref(false);

// Initialize filters
const filters = ref({
    start_date: props.filters.start_date || new Date().toISOString().split('T')[0],
    end_date: props.filters.end_date || new Date().toISOString().split('T')[0],
    branch_id: props.filters.branch_id || ''
});

// Form
const form = useForm({
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
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Validation computed
const isFormValid = computed(() => {
    // Only check if at least one section has data
    return form.receive_quantity > 0 || form.given_quantity > 0;
});



// Modal Handlers
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

// Form Submission
const submitForm = () => {
    form.post(route('payment-receipts.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeNewEntryModal();
            handleFilterChange();
        },
        onError: () => {
            console.error('Form submission failed:', form.errors);
        }
    });
};

const formatDisplayDate = (date) => {
    if (!date) return '';
    const [year, month, day] = date.split('-');
    return `${day}/${month}/${year}`;
};

// Add these methods to handle date conversions
const formatForInput = (dateStr) => {
    if (!dateStr) return '';
    const [day, month, year] = dateStr.split('/');
    return `${year}-${month}-${day}`;
};

// Update your computed properties
const displayStartDate = computed({
    get: () => filters.value.start_date ? formatDisplayDate(filters.value.start_date) : '',
    set: (value) => {
        filters.value.start_date = value;
        handleFilterChange();
    }
});

const displayEndDate = computed({
    get: () => filters.value.end_date ? formatDisplayDate(filters.value.end_date) : '',
    set: (value) => {
        filters.value.end_date = value;
        handleFilterChange();
    }
});

const displayTransactionDate = computed({
    get: () => form.transaction_date ? formatDisplayDate(form.transaction_date) : '',
    set: (value) => {
        form.transaction_date = value;
    }
});



// Initialize default dates on mount
onMounted(() => {
    if (!filters.value.start_date || !filters.value.end_date) {
        const today = new Date();
        // Keep ISO format for input fields
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

/* Custom scrollbar for dark mode */
.dark ::-webkit-scrollbar {
    width: 12px;
    height: 12px;
}

.dark ::-webkit-scrollbar-track {
    background: rgb(31, 41, 55);
    border-radius: 6px;
}

.dark ::-webkit-scrollbar-thumb {
    background-color: rgb(75, 85, 99);
    border-radius: 6px;
    border: 3px solid rgb(31, 41, 55);
}

.dark ::-webkit-scrollbar-thumb:hover {
    background-color: rgb(107, 114, 128);
}

/* Focus styles for dark mode */
.dark *:focus-visible {
    outline: 2px solid rgb(59, 130, 246);
    outline-offset: 2px;
}

/* Table hover animation */
.hover\:transform-scale {
    transition: transform 0.2s ease;
}

.hover\:transform-scale:hover {
    transform: scale(1.01);
}

table-wrapper {
    min-width: 100%;
    overflow-x: auto;
}

.receipt-table {
    width: 100%;
    table-layout: fixed;
}

.receipt-table th,
.receipt-table td {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Column widths */
.date-col {
    width: 8%;
}

.qty-col {
    width: 5%;
}

.number-col {
    width: 7%;
}

.total-col {
    width: 7%;
}

.name-col {
    width: 10%;
}

.pin-col {
    width: 8%;
}

.book-col {
    width: 7%;
}

.available-col {
    width: 7%;
}


@media (max-width: 768px) {
    .table-wrapper {
        margin: -1rem;
        /* Compensate for padding */
    }

    .receipt-table thead {
        display: none;
        /* Hide table headers on mobile */
    }

    .receipt-table tbody tr {
        display: block;
        margin-bottom: 1rem;
        background-color: white;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .receipt-table td {
        display: flex;
        padding: 0.75rem 1rem;
        border: none;
        border-bottom: 1px solid #e5e7eb;
        align-items: center;
        text-align: right;
    }

    .receipt-table td::before {
        content: attr(data-label);
        font-weight: 600;
        margin-right: auto;
        text-align: left;
        color: #4b5563;
    }

    /* Section Headers for Mobile */
    .receipt-section-header {
        display: block;
        padding: 0.5rem 1rem;
        font-weight: 600;
        text-align: center;
    }

    .receive-section {
        background-color: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .distribute-section {
        background-color: rgba(14, 165, 233, 0.1);
        color: #0284c7;
    }

    /* Dark mode adjustments */
    .dark .receipt-table tbody tr {
        background-color: #1f2937;
        border-color: #374151;
    }

    .dark .receipt-table td {
        border-color: #374151;
    }

    .dark .receipt-table td::before {
        color: #9ca3af;
    }

    /* Mobile Specific Spacing */
    .mobile-section-gap {
        margin-top: 1.5rem;
    }

    /* Responsive Grid for Stats */
    .stats-grid {
        grid-template-columns: 1fr;
    }

    @media (min-width: 640px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
}

/* Responsive Helper Classes */
.mobile-only {
    @media (min-width: 769px) {
        display: none;
    }
}

.desktop-only {
    @media (max-width: 768px) {
        display: none;
    }
}

/* Enhanced Table Styles */
.table-container {
    position: relative;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Fade indicators for horizontal scroll */
.table-container::before,
.table-container::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 20px;
    z-index: 2;
    pointer-events: none;
}

.table-container::before {
    left: 0;
    background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0));
}

.table-container::after {
    right: 0;
    background: linear-gradient(to left, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0));
}

.dark .table-container::before {
    background: linear-gradient(to right, rgba(31, 41, 55, 0.9), rgba(31, 41, 55, 0));
}

.dark .table-container::after {
    background: linear-gradient(to left, rgba(31, 41, 55, 0.9), rgba(31, 41, 55, 0));
}
</style>
