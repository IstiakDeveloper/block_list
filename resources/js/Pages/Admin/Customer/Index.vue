<template>
    <Head title="Customers" />

    <AdminLayout>
        <div class="container px-4 py-8 mx-auto">
            <div class="overflow-hidden bg-white shadow-xl rounded-2xl dark:bg-gray-800">
                <!-- Header Section -->
                <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex flex-col items-start justify-between lg:flex-row lg:items-center">
                            <!-- Title and Stats -->
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl dark:bg-blue-900">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 lg:text-3xl dark:text-white">Customer Management</h1>
                                    <div class="flex items-center mt-2 space-x-4 text-sm">
                                        <div class="flex items-center px-3 py-1 bg-white rounded-full shadow-sm dark:bg-gray-800">
                                            <div class="w-2 h-2 mr-2 bg-green-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ totalCustomers }} Total</span>
                                        </div>
                                        <div v-if="filteredCount !== totalCustomers" class="flex items-center px-3 py-1 bg-blue-100 rounded-full dark:bg-blue-900">
                                            <div class="w-2 h-2 mr-2 bg-blue-500 rounded-full"></div>
                                            <span class="font-medium text-blue-700 dark:text-blue-300">{{ filteredCount }} Filtered</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Customer Button -->
                            <Link v-if="user.name !== 'Super Admin'" href="/admin/customers/create"
                                class="flex items-center justify-center mt-6 btn-primary lg:mt-0">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
                                </svg>
                                Add New Customer
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="p-6 bg-gray-50 dark:bg-gray-900/50">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:gap-6">
                        <!-- Search Input -->
                        <div class="lg:col-span-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Search Customers</label>
                            <div class="relative">
                                <input
                                    type="text"
                                    v-model="search"
                                    placeholder="Name, NID, Phone..."
                                    @input="handleSearchInput"
                                    class="w-full px-4 py-3 text-gray-900 transition-all duration-200 bg-white border border-gray-300 shadow-sm pl-11 rounded-xl dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                                <div class="absolute left-4 top-3.5 text-gray-400 dark:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Branch Filter -->
                        <div v-if="user.name === 'Super Admin' || branches.length > 1" class="lg:col-span-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                            <div class="relative">
                                <select v-model="selectedBranch" @change="filterByBranch"
                                    class="w-full px-4 py-3 text-gray-900 transition-all duration-200 bg-white border border-gray-300 shadow-sm appearance-none rounded-xl dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Branches</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.branch_name }}
                                    </option>
                                </select>
                                <div class="absolute right-3 top-3.5 text-gray-400 pointer-events-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <!-- Show selected branch name when user has only one branch -->
                        <div v-else-if="branches.length === 1" class="lg:col-span-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                            <div class="flex items-center px-4 py-3 border border-blue-200 bg-blue-50 rounded-xl dark:bg-blue-900/30 dark:border-blue-800">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">{{ branches[0].branch_name }}</span>
                            </div>
                        </div>

                        <!-- Date Range Filters -->
                        <div class="lg:col-span-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Date Range</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="relative">
                                    <input
                                        type="date"
                                        v-model="dateFrom"
                                        @change="applyFilters"
                                        class="w-full px-4 py-3 text-gray-900 transition-all duration-200 bg-white border border-gray-300 shadow-sm rounded-xl dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    <label class="absolute px-1 text-xs text-gray-500 -top-1 left-3 bg-gray-50 dark:bg-gray-900 dark:text-gray-400">From</label>
                                </div>
                                <div class="relative">
                                    <input
                                        type="date"
                                        v-model="dateTo"
                                        @change="applyFilters"
                                        class="w-full px-4 py-3 text-gray-900 transition-all duration-200 bg-white border border-gray-300 shadow-sm rounded-xl dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    <label class="absolute px-1 text-xs text-gray-500 -top-1 left-3 bg-gray-50 dark:bg-gray-900 dark:text-gray-400">To</label>
                                </div>
                            </div>
                        </div>

                        <!-- Clear Filters Button -->
                        <div v-if="hasActiveFilters" class="lg:col-span-1 lg:flex lg:items-end">
                            <button
                                @click="clearFilters"
                                class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-red-600 transition-all duration-200 border border-red-200 bg-red-50 rounded-xl hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-2 mt-4">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Active filters:</span>

                        <span v-if="search" class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                            Search: "{{ search }}"
                            <button @click="search = ''; applyFilters()" class="ml-2 text-blue-500 hover:text-blue-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>

                        <span v-if="selectedBranch && branches.length > 1" class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                            Branch: {{ branches.find(b => b.id == selectedBranch)?.branch_name }}
                            <button @click="selectedBranch = ''; applyFilters()" class="ml-2 text-green-500 hover:text-green-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>

                        <span v-if="dateFrom || dateTo" class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-300">
                            Date: {{ dateFrom || '...' }} to {{ dateTo || '...' }}
                            <button @click="dateFrom = ''; dateTo = ''; applyFilters()" class="ml-2 text-purple-500 hover:text-purple-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                            <tr>
                                <th class="hidden p-4 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase sm:table-cell dark:text-gray-300">
                                    <div class="flex items-center space-x-1">
                                        <span>#</span>
                                    </div>
                                </th>
                                <th class="p-4 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase dark:text-gray-300">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Customer Name</span>
                                    </div>
                                </th>
                                <th class="hidden p-4 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase md:table-cell dark:text-gray-300">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-4 0a2 2 0 014 0z" />
                                        </svg>
                                        <span>NID Number</span>
                                    </div>
                                </th>
                                <th class="hidden p-4 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase lg:table-cell dark:text-gray-300">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Branch</span>
                                    </div>
                                </th>
                                <th class="hidden p-4 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase xl:table-cell dark:text-gray-300">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m0 0v6a1 1 0 01-1 1H9a1 1 0 01-1-1V7m0 0H5a1 1 0 00-1 1v10a1 1 0 001 1h14a1 1 0 001-1V8a1 1 0 00-1-1h-4" />
                                        </svg>
                                        <span>Created Date</span>
                                    </div>
                                </th>
                                <th class="p-4 text-xs font-semibold tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr v-for="(customer, index) in customers.data" :key="customer.id"
                                class="transition-colors duration-150 hover:bg-blue-50 dark:hover:bg-gray-700/50">
                                <td class="hidden p-4 text-sm font-medium text-gray-700 sm:table-cell whitespace-nowrap dark:text-gray-300">
                                    <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-500 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-400">
                                        {{ (customers.current_page - 1) * customers.per_page + index + 1 }}
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600">
                                                <span class="text-sm font-bold text-white">{{ customer.name.charAt(0).toUpperCase() }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate dark:text-gray-100">{{ customer.name }}</p>
                                            <!-- Mobile-only info -->
                                            <div class="mt-1 space-y-1 md:hidden">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="font-medium">NID:</span> {{ customer.nid_number }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 lg:hidden">
                                                    <span class="font-medium">Branch:</span> {{ customer.branch.branch_name }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 xl:hidden">
                                                    <span class="font-medium">Created:</span> {{ formatDate(customer.created_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden p-4 text-sm text-gray-900 md:table-cell whitespace-nowrap dark:text-gray-100">
                                    <div class="px-2 py-1 font-mono text-sm bg-gray-100 rounded dark:bg-gray-700">
                                        {{ customer.nid_number }}
                                    </div>
                                </td>
                                <td class="hidden p-4 text-sm text-gray-900 lg:table-cell whitespace-nowrap dark:text-gray-100">
                                    <div class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-2"></div>
                                        {{ customer.branch.branch_name }}
                                    </div>
                                </td>
                                <td class="hidden p-4 text-sm text-gray-500 xl:table-cell whitespace-nowrap dark:text-gray-400">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m0 0v6a1 1 0 01-1 1H9a1 1 0 01-1-1V7m0 0H5a1 1 0 00-1 1v10a1 1 0 001 1h14a1 1 0 001-1V8a1 1 0 00-1-1h-4" />
                                        </svg>
                                        <span class="text-sm">{{ formatDate(customer.created_at) }}</span>
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col items-end justify-end space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-3">
                                        <Link :href="route('admin.customers.show', customer.id)"
                                            class="btn-action btn-show group">
                                            <svg class="w-4 h-4 mr-2 transition-transform group-hover:scale-110" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>View</span>
                                        </Link>

                                        <Link v-if="user.name === 'Super Admin'"
                                            :href="route('admin.customers.edit', customer.id)"
                                            class="btn-action btn-edit group">
                                            <svg class="w-4 h-4 mr-2 transition-transform group-hover:scale-110" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            <span>Edit</span>
                                        </Link>

                                        <button v-if="user.name === 'Super Admin'" @click="confirmDelete(customer.id)"
                                            class="btn-action btn-delete group">
                                            <svg class="w-4 h-4 mr-2 transition-transform group-hover:scale-110" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- No customers found message -->
                            <tr v-if="customers.data.length === 0">
                                <td colspan="6" class="p-8 text-center">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <div class="flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full dark:bg-gray-700">
                                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">No customers found</h3>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ hasActiveFilters ? 'Try adjusting your search or filter criteria' : 'Get started by adding your first customer' }}
                                        </p>
                                        <Link v-if="!hasActiveFilters && user.name !== 'Super Admin'"
                                            href="/admin/customers/create"
                                            class="mt-4 btn-primary">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
                                            </svg>
                                            Add First Customer
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 dark:bg-gray-900/50 dark:border-gray-700">
                    <div class="flex flex-col items-center justify-between space-y-4 sm:flex-row sm:space-y-0">
                        <!-- Results Info -->
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ customers.from || 0 }} to {{ customers.to || 0 }} of {{ customers.total || 0 }} results
                        </div>

                        <!-- Pagination Links -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            <Link v-if="customers.prev_page_url"
                                :href="customers.prev_page_url"
                                :data="paginationParams"
                                class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </Link>
                            <span v-else class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 dark:border-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </span>

                            <!-- Page Numbers -->
                            <div class="hidden sm:flex sm:items-center sm:space-x-1">
                                <template v-for="(link, index) in paginationLinks" :key="index">
                                    <Link v-if="link.url && !link.active"
                                        :href="link.url"
                                        :data="paginationParams"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                                        v-html="link.label">
                                    </Link>
                                    <span v-else-if="link.active"
                                        class="px-3 py-2 text-sm font-medium text-blue-600 border border-blue-300 rounded-lg bg-blue-50 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-600"
                                        v-html="link.label">
                                    </span>
                                    <span v-else
                                        class="px-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-500"
                                        v-html="link.label">
                                    </span>
                                </template>
                            </div>

                            <!-- Current Page Info (Mobile) -->
                            <div class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 border border-blue-200 rounded-lg bg-blue-50 sm:hidden dark:bg-blue-900 dark:text-blue-300 dark:border-blue-600">
                                Page {{ customers.current_page }} of {{ customers.last_page }}
                            </div>

                            <!-- Next Button -->
                            <Link v-if="customers.next_page_url"
                                :href="customers.next_page_url"
                                :data="paginationParams"
                                class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                            <span v-else class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 dark:border-gray-600">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationDialog :show="isDialogVisible" @update:show="isDialogVisible = $event" @confirm="deleteCustomer" />
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, computed, onMounted, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    customers: Object,
    auth: Object,
    branches: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    totalCustomers: {
        type: Number,
        default: 0
    }
});

// State variables
const isDialogVisible = ref(false);
const customerToDelete = ref(null);
const selectedBranch = ref(
    props.branches.length === 1 ? props.branches[0].id : (props.filters.branch || '')
);
const search = ref(props.filters.search || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const form = useForm({});

// Computed properties
const user = computed(() => props.auth.user);

const hasMultipleBranches = computed(() =>
    props.auth.user.name === 'Super Admin' || props.userBranches?.length > 1
);

const filteredCount = computed(() => props.customers.total || 0);

const hasActiveFilters = computed(() => {
    return search.value ||
           (selectedBranch.value && props.branches.length > 1) ||
           dateFrom.value ||
           dateTo.value;
});

const paginationParams = computed(() => ({
    branch: selectedBranch.value,
    search: search.value,
    date_from: dateFrom.value,
    date_to: dateTo.value
}));

const paginationLinks = computed(() => {
    if (!props.customers.links) return [];
    return props.customers.links.filter(link => {
        // Filter out first and last links (they're usually "Previous" and "Next")
        return link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;';
    });
});

// Create debounced search function
const handleSearchInput = debounce(() => {
    applyFilters();
}, 300);

// Methods
const formatDate = (date) => {
    if (!date) return '';
    const dateObj = new Date(date);
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    return `${day}/${month}/${year}`;
};

function applyFilters() {
    const params = {};

    if (selectedBranch.value) {
        params.branch = selectedBranch.value;
    }

    if (search.value) {
        params.search = search.value;
    }

    if (dateFrom.value) {
        params.date_from = dateFrom.value;
    }

    if (dateTo.value) {
        params.date_to = dateTo.value;
    }

    router.get(route('admin.customers.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}

function filterByBranch() {
    applyFilters();
}

function clearFilters() {
    search.value = '';
    selectedBranch.value = props.branches.length === 1 ? props.branches[0].id : '';
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
}

function confirmDelete(customerId) {
    customerToDelete.value = customerId;
    isDialogVisible.value = true;
}

function deleteCustomer() {
    if (customerToDelete.value) {
        form.delete(route('admin.customers.destroy', customerToDelete.value), {
            onSuccess: () => {
                isDialogVisible.value = false;
                customerToDelete.value = null;
            },
            onError: () => {
                isDialogVisible.value = false;
                customerToDelete.value = null;
            }
        });
    }
}

// Lifecycle hooks
onMounted(() => {
    if (props.branches.length === 1 && !props.filters.branch) {
        filterByBranch();
    }
});

// Watch for filter changes
watch(() => props.filters, (newFilters) => {
    search.value = newFilters.search || '';
    selectedBranch.value = newFilters.branch || (props.branches.length === 1 ? props.branches[0].id : '');
    dateFrom.value = newFilters.date_from || '';
    dateTo.value = newFilters.date_to || '';
}, { deep: true });
</script>

<style scoped>
.btn-primary {
    @apply bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-200 focus:ring-4 focus:ring-blue-500/30;
}

.btn-secondary {
    @apply bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-xl shadow-lg hover:from-gray-200 hover:to-gray-300 transform hover:scale-105 transition-all duration-200 focus:ring-4 focus:ring-gray-500/30;
}

.btn-action {
    @apply inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 focus:ring-2 focus:ring-offset-2;
}

.btn-show {
    @apply text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 hover:text-emerald-800 focus:ring-emerald-500 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700 dark:hover:bg-emerald-900/50;
}

.btn-edit {
    @apply text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 hover:text-blue-800 focus:ring-blue-500 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-900/50;
}

.btn-delete {
    @apply text-red-700 bg-red-50 border border-red-200 hover:bg-red-100 hover:text-red-800 focus:ring-red-500 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-900/50;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    @apply bg-gray-100 dark:bg-gray-800;
}

::-webkit-scrollbar-thumb {
    @apply bg-gray-300 dark:bg-gray-600 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-400 dark:bg-gray-500;
}

/* Loading state animation */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.loading {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
