<template>
    <AdminLayout title="Payment Receipts - Super Admin">
        <div class="py-5 min-h-screen bg-slate-50/50 dark:bg-slate-950/20 text-slate-800 dark:text-slate-100">
            <div class="mx-auto space-y-5 max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <!-- Compact Header Section -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Payment Receipts Inventory
                        </h1>
                    </div>

                    <!-- Compact Quick Actions -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Add Stock Button -->
                        <button @click="showAddStockModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white transition-all duration-200 bg-purple-600 rounded-xl hover:bg-purple-700 active:scale-[0.98] shadow-sm shadow-purple-500/10">
                            <PackagePlus class="w-4 h-4" />
                            Add Stock
                        </button>

                        <!-- Distribute Button -->
                        <button @click="showDistributeModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white transition-all duration-200 bg-blue-600 rounded-xl hover:bg-blue-700 active:scale-[0.98] shadow-sm shadow-blue-500/10">
                            <SendIcon class="w-4 h-4" />
                            Distribute to Branch
                        </button>

                        <!-- Generate Report Button -->
                        <button @click="openReportModal"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white transition-all duration-200 bg-emerald-600 rounded-xl hover:bg-emerald-700 active:scale-[0.98] shadow-sm shadow-emerald-500/10">
                            <FileText class="w-4 h-4" />
                            Generate Report
                        </button>

                        <Link
                            :href="route('payment-receipts.search')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 transition-all duration-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 active:scale-[0.98] shadow-sm"
                        >
                            <Search class="w-4 h-4" />
                            Search Receipt
                        </Link>
                    </div>
                </div>

                <!-- Ultra Compact KPI Stats Cards -->
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                    <!-- Head Office Stock -->
                    <div class="p-4 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">HO Available</span>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-black tracking-tight text-indigo-600 dark:text-indigo-400">
                                    {{ formatNumber(currentStock / 100) }}
                                </span>
                                <span class="text-[10px] font-medium text-slate-400">books</span>
                            </div>
                            <div class="text-[9px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5">
                                {{ formatNumber(currentStock) }} receipts
                            </div>
                        </div>
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-950/50 rounded-xl shrink-0">
                            <Package class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </div>

                    <!-- Total Period Received -->
                    <div class="p-4 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Period Received</span>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-black tracking-tight text-emerald-600 dark:text-emerald-400">
                                    {{ formatNumber(totalPeriodReceivedRaw / 100) }}
                                </span>
                                <span class="text-[10px] font-medium text-slate-400">books</span>
                            </div>
                            <div class="text-[9px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5">
                                {{ formatNumber(totalPeriodReceivedRaw) }} receipts
                            </div>
                        </div>
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-950/50 rounded-xl shrink-0">
                            <TrendingUp class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>

                    <!-- Total Period Distributed -->
                    <div class="p-4 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Period Dist</span>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-black tracking-tight text-blue-600 dark:text-blue-400">
                                    {{ formatNumber(totalPeriodDistributedRaw / 100) }}
                                </span>
                                <span class="text-[10px] font-medium text-slate-400">books</span>
                            </div>
                            <div class="text-[9px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5">
                                {{ formatNumber(totalPeriodDistributedRaw) }} receipts
                            </div>
                        </div>
                        <div class="p-2 bg-blue-50 dark:bg-blue-950/50 rounded-xl shrink-0">
                            <TrendingDown class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>

                    <!-- Total Available in Branches -->
                    <div class="p-4 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Branch Stock</span>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-black tracking-tight text-pink-600 dark:text-pink-400">
                                    {{ formatNumber(totalAvailableRaw / 100) }}
                                </span>
                                <span class="text-[10px] font-medium text-slate-400">books</span>
                            </div>
                            <div class="text-[9px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5">
                                {{ formatNumber(totalAvailableRaw) }} receipts
                            </div>
                        </div>
                        <div class="p-2 bg-pink-50 dark:bg-pink-950/50 rounded-xl shrink-0">
                            <BarChart3 class="w-4.5 h-4.5 text-pink-600 dark:text-pink-400" />
                        </div>
                    </div>

                    <!-- Active Branches Count -->
                    <div class="p-4 bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm col-span-2 lg:col-span-1 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Active Branches</span>
                            <div class="mt-1 flex items-baseline gap-1">
                                <span class="text-2xl font-black tracking-tight text-purple-600 dark:text-purple-400">
                                    {{ branchSummaries.length }}
                                </span>
                                <span class="text-[10px] font-medium text-slate-400">units</span>
                            </div>
                        </div>
                        <div class="p-2 bg-purple-50 dark:bg-purple-950/50 rounded-xl shrink-0">
                            <LayoutDashboard class="w-4.5 h-4.5 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </div>

                <!-- Compact Filters Bar -->
                <div class="relative z-30 p-4 bg-white/60 dark:bg-slate-900/40 border border-slate-200/40 dark:border-slate-800/50 backdrop-blur-md rounded-2xl shadow-sm">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Start Date -->
                        <div class="flex flex-col gap-1">
                            <Label for="start_date" value="Start Date" class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500" />
                            <CustomDateInput v-model="filters.start_date" placeholder="dd/mm/yyyy"
                                class="block w-full mt-0.5" @update:modelValue="handleFilterChange" />
                        </div>

                        <!-- End Date -->
                        <div class="flex flex-col gap-1">
                            <Label for="end_date" value="End Date" class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500" />
                            <CustomDateInput v-model="filters.end_date" placeholder="dd/mm/yyyy" class="block w-full mt-0.5"
                                @update:modelValue="handleFilterChange" />
                        </div>

                        <!-- Branch Filter -->
                        <div class="flex flex-col gap-1" ref="branchDropdownRef">
                            <Label value="Branch Selection" class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500" />
                            <div class="relative">
                                <input
                                    type="text"
                                    v-model="branchSearch"
                                    placeholder="Search branch..."
                                    class="block w-full mt-0.5 pl-9 pr-4 py-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 rounded-xl dark:text-slate-100 focus:border-blue-500 focus:ring-blue-500 text-sm font-semibold"
                                    @focus="showBranchDropdown = true"
                                />
                                <Search class="absolute w-4 h-4 text-slate-400 left-3 top-3.5" />

                                <div v-if="showBranchDropdown && filteredBranches.length > 0"
                                    class="absolute z-50 w-full mt-1 overflow-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl max-h-48">
                                    <div class="py-1 text-xs">
                                        <div class="px-3 py-1.5 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium"
                                            @click="selectBranch('')">
                                            All Branches
                                        </div>
                                        <div v-for="branch in filteredBranches"
                                            :key="branch.id"
                                            @click="selectBranch(branch)"
                                            class="px-3 py-1.5 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 border-t border-slate-100 dark:border-slate-800/50">
                                            <div class="font-bold text-slate-900 dark:text-white">{{ branch.branch_code }}</div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400">{{ branch.branch_name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="selectedBranch" class="mt-1 flex items-center justify-between px-2 py-0.5 text-[10px] bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 rounded-lg">
                                <span class="truncate">Selected: <b>{{ selectedBranch.branch_code }}</b></span>
                                <button @click="selectBranch('')" class="ml-1 text-blue-500 hover:text-blue-700 font-bold">×</button>
                            </div>
                        </div>

                        <!-- Stock Status Filter -->
                        <div class="flex flex-col gap-1">
                            <Label value="Stock Status" class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500" />
                            <select v-model="stockStatusFilter"
                                class="block w-full mt-0.5 px-3 py-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 rounded-xl dark:text-slate-100 focus:border-blue-500 focus:ring-blue-500 text-sm font-semibold">
                                <option value="all">All Statuses</option>
                                <option value="good_stock">Good Stock (>= 500)</option>
                                <option value="moderate">Moderate (100 - 499)</option>
                                <option value="low_stock">Low Stock (&lt; 100)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Two-Column Grid Layout (Side-by-Side: Branches Summary & Recent Transactions) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start relative z-10">
                    
                    <!-- Left Column: Branches Summary (lg:col-span-5) -->
                    <div class="lg:col-span-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">
                                Branches Summary
                            </h2>
                            <span class="text-xs text-slate-400 font-medium dark:text-slate-500">
                                Showing {{ filteredBranchSummaries.length }} branches
                            </span>
                        </div>
                        
                        <!-- Scrollable 2-Column Grid Cards Container -->
                        <div class="max-h-[750px] overflow-y-auto pr-1.5 scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pb-2">
                                <div v-for="summary in filteredBranchSummaries" :key="summary.branch_id"
                                    class="relative overflow-hidden group bg-white dark:bg-slate-900/60 border border-slate-200/50 dark:border-slate-800/80 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between min-h-[160px]">
                                    
                                    <!-- Card Header -->
                                    <div class="p-3.5 flex items-start justify-between gap-2">
                                        <div class="space-y-0.5 overflow-hidden">
                                            <h3 class="font-extrabold text-xs text-slate-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate" :title="summary.branch_name">
                                                {{ summary.branch_name }}
                                            </h3>
                                            <span class="inline-block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                                Code: {{ summary.branch_code }}
                                            </span>
                                        </div>
                                        <button @click="viewBranchDetails(summary.branch_id)"
                                            class="p-1 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800/50 rounded-lg transition-all duration-200 shrink-0">
                                            <Eye class="w-4 h-4" />
                                        </button>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="px-3.5 pb-3.5 space-y-2.5 mt-auto">
                                        <!-- Stats Rows -->
                                        <div class="grid grid-cols-2 gap-2 text-[10px]">
                                            <div>
                                                <span class="text-slate-400 dark:text-slate-500 font-semibold block">Period (Rec/Dist)</span>
                                                <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">
                                                    {{ formatNumber(summary.period_received) }} / {{ formatNumber(summary.period_distributed) }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-slate-400 dark:text-slate-500 font-semibold block">Total (Rec/Dist)</span>
                                                <span class="font-bold text-slate-600 dark:text-slate-400 block truncate">
                                                    {{ formatNumber(summary.all_time_received) }} / {{ formatNumber(summary.all_time_distributed) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Available Stock -->
                                        <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 dark:border-slate-800/40">
                                            <div>
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Available</span>
                                                <div class="text-sm font-black text-slate-900 dark:text-white leading-tight">
                                                    {{ formatNumber(summary.current_available) }}
                                                </div>
                                            </div>
                                            
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold tracking-tight shrink-0"
                                                :class="getStockColorClass(summary.current_available)">
                                                <span class="w-1 h-1 rounded-full animate-pulse"
                                                    :class="summary.current_available < 100 ? 'bg-red-500' : (summary.current_available < 500 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                                {{ getStockStatus(summary.current_available) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="filteredBranchSummaries.length === 0" class="py-10 text-center text-sm text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-900/40 border border-slate-200/50 dark:border-slate-800/80 rounded-2xl">
                                No branches match the stock status filter.
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Recent Transactions (lg:col-span-7) -->
                    <div class="lg:col-span-7 space-y-3">
                        <h2 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">
                            Recent Transactions
                        </h2>
                        
                        <div class="bg-white dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
                            <!-- Flash messages inside panel -->
                            <div class="px-6 pt-4 space-y-3" v-if="$page.props.flash.success || $page.props.flash.error">
                                <div v-if="$page.props.flash.success"
                                    class="flex items-center gap-3 p-3 text-xs font-semibold text-emerald-800 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-900/50 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20">
                                    <CheckCircle class="w-4 h-4 text-emerald-500 shrink-0" />
                                    <span>{{ $page.props.flash.success }}</span>
                                </div>

                                <div v-if="$page.props.flash.error"
                                    class="flex items-center gap-3 p-3 text-xs font-semibold text-red-800 dark:text-red-300 border border-red-100 dark:border-red-900/50 rounded-xl bg-red-50/50 dark:bg-red-950/20">
                                    <AlertCircle class="w-4 h-4 text-red-500 shrink-0" />
                                    <span>{{ $page.props.flash.error }}</span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                                    <thead class="bg-slate-50/75 dark:bg-slate-900/40">
                                        <tr>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Date
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Lot
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Branch
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Type
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Books
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase">
                                                Receipts
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-left text-slate-400 dark:text-slate-500 uppercase font-medium">
                                                By / For
                                            </th>
                                            <th class="px-4 py-3 text-xs font-bold tracking-wider text-right text-slate-400 dark:text-slate-500 uppercase">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 bg-white dark:bg-slate-900/25">
                                        <tr v-for="transaction in receipts.data" :key="transaction.id"
                                            class="transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                            <td class="px-4 py-3 text-xs font-semibold text-slate-900 whitespace-nowrap dark:text-slate-200">
                                                {{ formatDate(transaction.transaction_date) }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap dark:text-slate-400">
                                                {{ transaction.lot?.lot_number }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-900 whitespace-nowrap dark:text-slate-200 font-medium">
                                                {{ transaction.branch?.branch_name || '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                    :class="getTransactionTypeClass(transaction.transaction_type)">
                                                    {{ getTransactionTypeLabel(transaction.transaction_type) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap dark:text-slate-400">
                                                {{ transaction.book_from }}-{{ transaction.book_to }}
                                            </td>
                                            <td class="px-4 py-3 text-xs font-bold text-slate-900 whitespace-nowrap dark:text-white">
                                                {{ formatNumber(transaction.total_receipts) }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap dark:text-slate-400 truncate max-w-[80px]">
                                                {{ transaction.given_to || transaction.received_by || '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs font-medium text-right whitespace-nowrap">
                                                <div class="flex justify-end gap-1">
                                                    <button @click="viewTransactionDetails(transaction.id)"
                                                        class="p-1 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-all duration-200">
                                                        <Eye class="w-4 h-4" />
                                                    </button>
                                                    <button @click="handleEditTransaction(transaction)"
                                                        class="p-1 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-all duration-200">
                                                        <Edit class="w-4 h-4" />
                                                    </button>
                                                    <button @click="confirmDelete(transaction.id)"
                                                        class="p-1 text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-all duration-200">
                                                        <Trash2 class="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="!receipts.data || receipts.data.length === 0">
                                            <td colspan="8" class="px-4 py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                                                No transactions found.
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
            </div>
        </div>

        <!-- Modals -->
        <AddStockModal :show="showAddStockModal" :active-lots="activeLots" @close="showAddStockModal = false"
            @success="refreshAfterMutation" />

        <DistributeToBranchModal :show="showDistributeModal" :branches="branches" :active-lots="activeLots"
            @close="showDistributeModal = false" @success="refreshAfterMutation" />

        <BranchTransactionsModal :show="showBranchModal" :branch="selectedBranch" :transactions="branchTransactions"
            :filters="filters" @close="closeBranchModal" @refresh="refreshAfterMutation"
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
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
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
    AlertCircle,
    TrendingUp,
    TrendingDown,
    BarChart3
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
const branchDropdownRef = ref(null);

const handleClickOutside = (event) => {
    if (branchDropdownRef.value && !branchDropdownRef.value.contains(event.target)) {
        showBranchDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Client-side Stock Status Filter
const stockStatusFilter = ref('all');

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

// Client-side filtering of branch summaries by stock status
const filteredBranchSummaries = computed(() => {
    return props.branchSummaries.filter(summary => {
        if (stockStatusFilter.value === 'all') return true;
        const status = getStockStatus(summary.current_available).toLowerCase().replace(' ', '_');
        return status === stockStatusFilter.value;
    });
});

const totalPeriodReceivedRaw = computed(() => {
    return props.branchSummaries.reduce((sum, branch) => sum + (Number(branch.period_received) || 0), 0);
});

const totalPeriodDistributedRaw = computed(() => {
    return props.branchSummaries.reduce((sum, branch) => sum + (Number(branch.period_distributed) || 0), 0);
});

const totalAvailableRaw = computed(() => {
    return props.branchSummaries.reduce((sum, branch) => sum + (Number(branch.current_available) || 0), 0);
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
    if (available < 100) return 'bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900/50';
    if (available < 500) return 'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900/50';
    return 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50';
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
        'stock_in': 'bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-950/20 dark:text-purple-400 dark:border-purple-900/30',
        'distribute_to_branch': 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30',
        'distribute_to_person': 'bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30'
    };
    return classes[type] || 'bg-slate-50 text-slate-700 border border-slate-100 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800';
};

const reloadProps = ['receipts', 'branchSummaries', 'currentStock', 'activeLots'];

const refreshAfterMutation = () => {
    router.reload({
        only: reloadProps,
        preserveScroll: true,
    });
};

const handleFilterChange = debounce(() => {
    router.get(route('payment-receipts.index'), {
        start_date: filters.value.start_date,
        end_date: filters.value.end_date,
        branch_id: filters.value.branch_id
    }, {
        preserveState: true,
        preserveScroll: true,
        only: reloadProps
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
            refreshAfterMutation();
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
    refreshAfterMutation();

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

<style scoped>
/* Custom thin scrollbar for scrollable panels */
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.3);
    border-radius: 20px;
}
.dark .scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: rgba(75, 85, 99, 0.4);
}
</style>
