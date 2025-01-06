<template>
    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Distribute Receipts to Officers
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Distribution Form -->
                <div class="p-6 bg-white rounded-lg shadow-md">
                    <form @submit.prevent="submitDistribution">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div v-if="!userBranch">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                                <select v-model="form.branch_id" @change="loadOfficers" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600
               dark:bg-gray-700 dark:text-gray-300 focus:ring-blue-500
               dark:focus:ring-blue-400">
                                    <option value="">Select Branch</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.branch_name }} (Available: {{ getBranchStock(branch.id) }})
                                    </option>
                                </select>
                            </div>
                            <div v-else class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                                <div class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ branches.find(b => b.id === userBranch)?.branch_name }}
                                    (Available: {{ getBranchStock(userBranch) }})
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Officer</label>
                                <select v-model="form.branch_officer_id"
                                    class="mt-1 block w-full rounded-md border-gray-300" :disabled="!form.branch_id">
                                    <option value="">Select Officer</option>
                                    <option v-for="officer in branchOfficers" :key="officer.id" :value="officer.id">
                                        {{ officer.name }} ({{ officer.pin_number }})
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input type="number" v-model="form.quantity" min="1" :max="maxAvailable"
                                    class="mt-1 block w-full rounded-md border-gray-300">
                                <p v-if="maxAvailable" class="mt-1 text-sm text-gray-500">
                                    Maximum available: {{ maxAvailable }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Distribution Date</label>
                                <input type="date" v-model="form.distribution_date"
                                    class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea v-model="form.notes" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" :disabled="form.processing || !isFormValid"
                                class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50">
                                {{ form.processing ? 'Processing...' : 'Distribute Receipts' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Recent Distributions -->
                <div class="mt-6">
                    <h3 class="text-lg font-medium">Recent Distributions</h3>
                    <div class="mt-4 overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Officer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Distributed By
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="distribution in recentDistributions.data" :key="distribution.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ formatDate(distribution.distribution_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ distribution.branch.branch_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ distribution.officer.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ distribution.quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ distribution.user.name }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :links="recentDistributions.links" class="mt-6" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    branches: Array,
    stocks: Array,
    recentDistributions: Object,
    userBranch: Number,
    flash: Object
})

const form = useForm({
    branch_id: props.userBranch || '',
    branch_officer_id: '',
    quantity: '',
    distribution_date: new Date().toISOString().split('T')[0],
    notes: '',
})

onMounted(() => {
    if (props.userBranch) {
        form.branch_id = props.userBranch
        loadOfficers() // Automatically load officers for the user's branch
    }
})

const branchOfficers = ref([])

const getBranchStock = (branchId) => {
    const stock = props.stocks.find(s => s.branch_id === branchId)
    return stock ? stock.available_receipts : 0
}

const maxAvailable = computed(() => {
    if (!form.branch_id) return 0
    return getBranchStock(form.branch_id)
})

const isFormValid = computed(() => {
    return form.branch_id &&
        form.branch_officer_id &&
        form.quantity > 0 &&
        form.quantity <= maxAvailable.value &&
        form.distribution_date
})

const loadOfficers = async () => {
    if (!form.branch_id) {
        branchOfficers.value = []
        return
    }

    try {
        const response = await axios.get(route('admin.branch-officers.by-branch', form.branch_id))
        branchOfficers.value = response.data
    } catch (error) {
        console.error('Error loading officers:', error)
        branchOfficers.value = []
    }
}

const submitDistribution = () => {
    form.post(route('admin.receipt-distributions.store'), {
        onSuccess: () => {
            form.reset()
            branchOfficers.value = []
        },
    })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

// Reset officer selection when branch changes
watch(() => form.branch_id, () => {
    form.branch_officer_id = ''
})

// Reset quantity if it exceeds max available
watch(() => maxAvailable.value, (newMax) => {
    if (form.quantity > newMax) {
        form.quantity = ''
    }
})

// Validation errors watcher
watch(() => form.errors, (newErrors) => {
    if (Object.keys(newErrors).length > 0) {
        // Handle validation errors here if needed
        console.log('Validation errors:', newErrors)
    }
}, { deep: true })

// Clean up when component unmounts
onUnmounted(() => {
    form.reset()
    branchOfficers.value = []
})
</script>

<style scoped>
.error-text {
    @apply text-red-500 text-sm mt-1;
}

input.has-error,
select.has-error,
textarea.has-error {
    @apply border-red-500;
}

.disabled-option {
    @apply text-gray-400 cursor-not-allowed;
}
</style>
