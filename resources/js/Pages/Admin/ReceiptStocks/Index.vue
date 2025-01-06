<template>
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Receipt Stock Management
                </h2>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total Available Stock: {{ totalAvailableStock }}
                </span>
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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Receipts</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ totalReceipts }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Used Receipts</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ totalUsed }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Available Receipts</div>
                        <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ totalAvailable }}
                        </div>
                    </div>
                </div>

                <!-- Add Stock Form -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Add Stock to Branch
                        </h3>
                        <form @submit.prevent="submitTransfer">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Branch Selection -->
                                <div v-if="!userBranch"> <!-- Only show if user doesn't have a branch -->
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Branch
                                    </label>
                                    <div class="mt-1">
                                        <select v-model="transferForm.to_branch_id"
                                            :class="{ 'border-red-500 dark:border-red-600': transferForm.errors.to_branch_id }"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600
                   dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500
                   dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
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
                                </div>

                                <div v-else class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Branch
                                    </label>
                                    <div class="text-gray-900 dark:text-gray-100">
                                        {{ branches.find(b => b.id === userBranch)?.branch_name }}
                                    </div>
                                </div>

                                <!-- Quantity Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Quantity
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" v-model="transferForm.quantity" min="1"
                                            :class="{ 'border-red-500 dark:border-red-600': transferForm.errors.quantity }"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600
                                                   dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500
                                                   dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400" />
                                        <p v-if="transferForm.errors.quantity"
                                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ transferForm.errors.quantity }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Date Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Date
                                    </label>
                                    <div class="mt-1">
                                        <input type="date" v-model="transferForm.transfer_date"
                                            :class="{ 'border-red-500 dark:border-red-600': transferForm.errors.transfer_date }"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600
                                                   dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500
                                                   dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400" />
                                        <p v-if="transferForm.errors.transfer_date"
                                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ transferForm.errors.transfer_date }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Notes Textarea -->
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Notes
                                    </label>
                                    <div class="mt-1">
                                        <textarea v-model="transferForm.notes" rows="2"
                                            :class="{ 'border-red-500 dark:border-red-600': transferForm.errors.notes }"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600
                                                   dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500
                                                   dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400"></textarea>
                                        <p v-if="transferForm.errors.notes"
                                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ transferForm.errors.notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" :disabled="transferForm.processing"
                                    class="inline-flex justify-center items-center px-4 py-2 border border-transparent
                                           text-sm font-medium rounded-md shadow-sm text-white bg-blue-600
                                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2
                                           focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600
                                           dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg v-if="transferForm.processing"
                                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Current Stock Status
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500
                                                   dark:text-gray-300 uppercase tracking-wider">
                                            Branch
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500
                                                   dark:text-gray-300 uppercase tracking-wider">
                                            Total Receipts
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500
                                                   dark:text-gray-300 uppercase tracking-wider">
                                            Used
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500
                                                   dark:text-gray-300 uppercase tracking-wider">
                                            Available
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500
                                                   dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="stock in stocks.data" :key="stock.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ stock.branch.branch_name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ stock.total_receipts }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ stock.used_receipts }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 py-1 text-sm rounded-full',
                                                stock.available_receipts > 0
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                            ]">
                                                {{ stock.available_receipts }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="showHistory(stock.branch_id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400
                                                       dark:hover:text-blue-300 focus:outline-none focus:underline">
                                                View History
                                            </button>

                                            <Link :href="route('admin.receipt-distributions.create')" class="text-green-600 hover:text-green-900 dark:text-green-400
               dark:hover:text-green-300 focus:outline-none focus:underline">
                                            Distribute
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="stocks.data.length === 0">
                                        <td colspan="5"
                                            class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No stock records found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            <Pagination :data="stocks" />
                        </div>
                    </div>
                </div>

                <!-- History Modal -->
                <!-- History Modal -->
                <Modal :show="historyModal" @close="closeHistoryModal">
                    <div class="bg-white dark:bg-gray-800 px-6 py-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Stock History
                            </h3>
                            <!-- ... close button ... -->
                        </div>

                        <!-- Add tabs for Stock In/Out -->
                        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex -mb-px">
                                <button @click="activeTab = 'in'" :class="[
                                    'px-4 py-2 text-sm font-medium',
                                    activeTab === 'in'
                                        ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                                ]">
                                    Stock In
                                </button>
                                <button @click="activeTab = 'out'" :class="[
                                    'px-4 py-2 text-sm font-medium',
                                    activeTab === 'out'
                                        ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
                                ]">
                                    Stock Out (Distributions)
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <!-- Stock In History -->
                            <table v-if="activeTab === 'in'"
                                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Date
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Quantity Added
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Added By
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Notes
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- ... existing transfer history rows ... -->
                                </tbody>
                            </table>

                            <!-- Stock Out History -->
                            <table v-else class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Date
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Officer
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Quantity
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Distributed By
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Notes
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="dist in distributionHistory" :key="dist.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td
                                            class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ formatDate(dist.distribution_date) }}
                                        </td>
                                        <td
                                            class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ dist.officer.name }}
                                        </td>
                                        <td
                                            class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ dist.quantity }}
                                        </td>
                                        <td
                                            class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ dist.user.name }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                                            {{ dist.notes || '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Modal>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import AlertMessage from '@/Components/AlertMessage.vue'
import axios from 'axios'

const props = defineProps({
    stocks: Object,
    branches: Array,
    flash: Object,
    userBranch: Number

})

const errorMessage = ref('')
const successMessage = ref('')
const historyModal = ref(false)
const transferHistory = ref([])
const activeTab = ref('in')
const distributionHistory = ref([])

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

// Methods
const showHistory = async (branchId) => {
    try {
        const [transfersResponse, distributionsResponse] = await Promise.all([
            axios.get(route('admin.receipt-stocks.history', branchId)),
            axios.get(route('admin.receipt-stocks.distributions', branchId))
        ])

        transferHistory.value = transfersResponse.data
        distributionHistory.value = distributionsResponse.data
        historyModal.value = true
    } catch (error) {
        console.error('Error fetching history:', error)
        errorMessage.value = 'Failed to load history'
    }
}

const closeHistoryModal = () => {
    historyModal.value = false
    transferHistory.value = []
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
</style>
