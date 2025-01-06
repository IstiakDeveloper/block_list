<template>
    <AdminLayout>
        <!-- Header Section -->
        <template #header>
            <div class="flex items-center space-x-4">
                <!-- Back Button -->
                <button @click="$router.back()"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <ChevronLeftIcon class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                </button>

                <div class="flex justify-between items-center flex-grow">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center space-x-2">
                        <ChartBarIcon class="w-6 h-6 text-blue-500" />
                        <span>Receipt Stock Management</span>
                    </h2>
                    <div class="bg-blue-50 dark:bg-blue-900/30 px-4 py-2 rounded-lg">
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-400">
                            Total Available Stock: {{ totalAvailable }}
                        </span>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Alert Messages -->
                <div class="space-y-2">
                    <AlertMessage v-if="errorMessage" type="error" :message="errorMessage" />
                    <AlertMessage v-if="successMessage" type="success" :message="successMessage" />
                    <AlertMessage v-if="flash.error" type="error" :message="flash.error" />
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Receipts Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Receipts</div>
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <DocumentDuplicateIcon class="w-5 h-5 text-blue-500 dark:text-blue-400" />
                            </div>
                        </div>
                        <div class="mt-2 flex items-baseline">
                            <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                {{ totalReceipts }}
                            </div>
                            <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">receipts</div>
                        </div>
                    </div>

                    <!-- Used Receipts Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Used Receipts</div>
                            <div class="p-2 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                <CheckCircleIcon class="w-5 h-5 text-green-500 dark:text-green-400" />
                            </div>
                        </div>
                        <div class="mt-2 flex items-baseline">
                            <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                {{ totalUsed }}
                            </div>
                            <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">used</div>
                        </div>
                    </div>

                    <!-- Available Receipts Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Available Receipts</div>
                            <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                                <ArchiveBoxIcon class="w-5 h-5 text-purple-500 dark:text-purple-400" />
                            </div>
                        </div>
                        <div class="mt-2 flex items-baseline">
                            <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                {{ totalAvailable }}
                            </div>
                            <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">available</div>
                        </div>
                    </div>
                </div>

                <!-- Add Stock Form -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center space-x-2 mb-6">
                            <PlusCircleIcon class="w-5 h-5 text-blue-500" />
                            <span>Add Stock to Branch</span>
                        </h3>
                        <form @submit.prevent="submitTransfer" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Branch Selection -->
                                <div v-if="!userBranch">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Branch
                                    </label>
                                    <select v-model="transferForm.to_branch_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                               dark:text-gray-300 focus:ring-blue-500 dark:focus:ring-blue-400
                                               focus:border-blue-500 dark:focus:border-blue-400 transition-colors">
                                        <option value="">Select Branch</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                            {{ branch.branch_name }}
                                        </option>
                                    </select>
                                    <p v-if="transferForm.errors.to_branch_id"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ transferForm.errors.to_branch_id }}
                                    </p>
                                </div>

                                <!-- Quantity Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Quantity
                                    </label>
                                    <div class="relative">
                                        <input type="number" v-model="transferForm.quantity" min="1"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                                   dark:text-gray-300 focus:ring-blue-500 dark:focus:ring-blue-400
                                                   focus:border-blue-500 dark:focus:border-blue-400 transition-colors" />
                                        <HashtagIcon
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    </div>
                                    <p v-if="transferForm.errors.quantity"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ transferForm.errors.quantity }}
                                    </p>
                                </div>

                                <!-- Date Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Date
                                    </label>
                                    <div class="relative">
                                        <input type="date" v-model="transferForm.transfer_date"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                                   dark:text-gray-300 focus:ring-blue-500 dark:focus:ring-blue-400
                                                   focus:border-blue-500 dark:focus:border-blue-400 transition-colors" />
                                        <CalendarIcon
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    </div>
                                    <p v-if="transferForm.errors.transfer_date"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ transferForm.errors.transfer_date }}
                                    </p>
                                </div>

                                <!-- Notes Textarea -->
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Notes
                                    </label>
                                    <textarea v-model="transferForm.notes" rows="2"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                               dark:text-gray-300 focus:ring-blue-500 dark:focus:ring-blue-400
                                               focus:border-blue-500 dark:focus:border-blue-400 transition-colors"></textarea>
                                    <p v-if="transferForm.errors.notes"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ transferForm.errors.notes }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" :disabled="transferForm.processing" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg
                                           text-sm font-medium text-white bg-blue-600 hover:bg-blue-700
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                           dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800
                                           disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    <PlusIcon v-if="!transferForm.processing" class="w-5 h-5 mr-2" />
                                    <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    {{ transferForm.processing ? 'Adding Stock...' : 'Add Stock' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Stock List -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center space-x-2">
                            <ClipboardDocumentListIcon class="w-5 h-5 text-blue-500" />
                            <span>Current Stock Status</span>
                        </h3>
                        <!-- Search Input -->
                        <div class="relative w-64">
                            <input type="text" placeholder="Search branches..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                       dark:text-gray-300 focus:ring-blue-500 dark:focus:ring-blue-400
                                       focus:border-blue-500 dark:focus:border-blue-400 transition-colors pl-10" />
                            <MagnifyingGlassIcon
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Branch
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Receipts
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Used
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Available
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="stock in stocks.data" :key="stock.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <BuildingOfficeIcon class="w-5 h-5 text-gray-400 mr-2" />
                                            <span class="text-sm text-gray-900 dark:text-gray-300">
                                                {{ stock.branch.branch_name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-300">
                                            {{ stock.total_receipts }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-300">
                                            {{ stock.used_receipts }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                            stock.available_receipts > 0
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                        ]">
                                            {{ stock.available_receipts }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex justify-end space-x-3">
                                            <button @click="showHistory(stock.branch_id)"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-900
                                                           dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                <ClockIcon class="w-4 h-4 mr-1" />
                                                History
                                            </button>
                                            <Link :href="route('admin.receipt-distributions.create')"
                                                class="inline-flex items-center text-green-600 hover:text-green-900
                                                         dark:text-green-400 dark:hover:text-green-300 transition-colors">
                                            <ArrowDownTrayIcon class="w-4 h-4 mr-1" />
                                            Distribute
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="stocks.data.length === 0">
                                    <td colspan="5"
                                        class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center py-6">
                                            <InboxIcon class="w-12 h-12 text-gray-400 mb-2" />
                                            <span>No stock records found</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <Pagination :data="stocks" />
                    </div>
                </div>

                <!-- History Modal -->
                <Modal :show="historyModal" @close="closeHistoryModal" maxWidth="2xl">
                    <div class="bg-white dark:bg-gray-800">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    <ClockIcon class="w-5 h-5 text-blue-500 mr-2" />
                                    Stock History
                                </h3>
                                <button @click="closeHistoryModal"
                                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                                    <XMarkIcon class="w-6 h-6" />
                                </button>
                            </div>
                        </div>

                        <!-- History Tabs -->
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="-mb-px flex">
                                <button @click="activeTab = 'in'" :class="[
                                    'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                                    activeTab === 'in'
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                                ]">
                                    <div class="flex items-center space-x-2">
                                        <ArrowDownTrayIcon class="w-4 h-4" />
                                        <span>Stock In</span>
                                    </div>
                                </button>
                                <button @click="activeTab = 'out'" :class="[
                                    'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                                    activeTab === 'out'
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                                ]">
                                    <div class="flex items-center space-x-2">
                                        <ArrowUpTrayIcon class="w-4 h-4" />
                                        <span>Stock Out</span>
                                    </div>
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="p-6">
                            <!-- Stock In History -->
                            <div v-if="activeTab === 'in'" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Date
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                From
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Quantity
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Added By
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Notes
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="transfer in stockInHistory" :key="transfer.id"
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                                {{ formatDate(transfer.transfer_date) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                                Head Office
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                    {{ transfer.quantity }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                                {{ transfer.user.name }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                                {{ transfer.notes || '-' }}
                                            </td>
                                        </tr>
                                        <tr v-if="stockInHistory.length === 0">
                                            <td colspan="5"
                                                class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No stock in records found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Stock Out History -->
                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Date
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Officer
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Quantity
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Distributed By
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Notes
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="dist in stockOutHistory" :key="dist.id"
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                                {{ formatDate(dist.distribution_date) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                                {{ dist.officer.name }} ({{ dist.officer.pin_number }})
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    {{ dist.quantity }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                                {{ dist.user.name }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-300">
                                                {{ dist.notes || '-' }}
                                            </td>
                                        </tr>
                                        <tr v-if="stockOutHistory.length === 0">
                                            <td colspan="5"
                                                class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No distributions found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </Modal>
            </div>
        </div>
    </AdminLayout>
</template>


<script setup>
import { ref, computed, onMounted  } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import AlertMessage from '@/Components/AlertMessage.vue'
import {
    ChevronLeftIcon,
    PlusIcon,
    ChartBarIcon,
    DocumentDuplicateIcon,
    CheckCircleIcon,
    ArchiveBoxIcon,
    PlusCircleIcon,
    HashtagIcon,
    CalendarIcon,
    ClipboardDocumentListIcon,
    BuildingOfficeIcon,
    ClockIcon,
    ArrowDownTrayIcon,
    ArrowUpTrayIcon,
    XMarkIcon,
    MagnifyingGlassIcon,
    InboxIcon
} from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
    stocks: Object,
    branches: Array,
    flash: Object,
    userBranch: Number

})

const errorMessage = ref('')
const successMessage = ref('')
const transferHistory = ref([])
const activeTab = ref('in')
const distributionHistory = ref([])
const historyModal = ref(false)
const stockHistory = ref([])
const stockInHistory = ref([])
const stockOutHistory = ref([])

const transferForm = useForm({
    to_branch_id: props.userBranch || '', // Initialize with userBranch if available
    quantity: '',
    transfer_date: new Date().toISOString().split('T')[0],
    notes: '',
})

// Computed properties for summary statistics
const totalReceipts = computed(() => {
    return props.stocks.data.reduce((sum, stock) => sum + stock.total_receipts, 0)
})

const totalUsed = computed(() => {
    return props.stocks.data.reduce((sum, stock) => sum + stock.used_receipts, 0)
})

const totalAvailable = computed(() => {
    return props.stocks.data.reduce((sum, stock) => sum + stock.available_receipts, 0)
})

const showHistory = async (branchId) => {
    try {
        // Fetch both histories simultaneously
        const [inResponse, outResponse] = await Promise.all([
            axios.get(route('admin.receipt-stocks.history', branchId)),
            axios.get(route('admin.receipt-stocks.distributions', branchId))
        ])

        stockInHistory.value = inResponse.data
        stockOutHistory.value = outResponse.data
        historyModal.value = true
    } catch (error) {
        console.error('Error fetching history:', error)
        errorMessage.value = 'Failed to load history'
    }
}

const closeHistoryModal = () => {
    historyModal.value = false
    stockInHistory.value = []
    stockOutHistory.value = []
    activeTab.value = 'in'  // Reset to default tab
}



const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })
}

const submitTransfer = () => {
    errorMessage.value = ''
    successMessage.value = ''

    // Client-side validation
    if (!transferForm.to_branch_id) {
        errorMessage.value = 'Please select a branch'
        return
    }

    transferForm.post(route('admin.receipt-stocks.transfer'), {
        onSuccess: () => {
            successMessage.value = 'Stock added successfully'
            transferForm.reset()
            transferForm.transfer_date = new Date().toISOString().split('T')[0]
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0]
            errorMessage.value = firstError || 'Failed to add stock'
        }
    })
}

// Lifecycle hooks
onMounted(() => {
    if (props.flash?.message) {
        successMessage.value = props.flash.message
    }
    if (props.flash?.error) {
        errorMessage.value = props.flash.error
    }
})
</script>

<style scoped>
.dark-mode-transition {
    @apply transition-colors duration-200;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    @apply w-2 h-2;
}

::-webkit-scrollbar-track {
    @apply bg-gray-100 dark:bg-gray-700;
}

::-webkit-scrollbar-thumb {
    @apply bg-gray-300 dark:bg-gray-600 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-400 dark:bg-gray-500;
}

.fade-enter-active,
.fade-leave-active {
    @apply transition-all duration-200;
}

.fade-enter-from,
.fade-leave-to {
    @apply opacity-0 transform scale-95;
}

.fade-enter-to,
.fade-leave-from {
    @apply opacity-100 transform scale-100;
}

/* Custom scrollbar styling */
::-webkit-scrollbar {
    @apply w-2 h-2;
}

::-webkit-scrollbar-track {
    @apply bg-gray-100 dark:bg-gray-700 rounded-full;
}

::-webkit-scrollbar-thumb {
    @apply bg-gray-300 dark:bg-gray-600 rounded-full hover:bg-gray-400 dark:hover:bg-gray-500;
}

/* Table hover effects */
.table-row-hover {
    @apply transition-colors duration-150;
}

</style>
