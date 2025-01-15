<template>
    <AdminLayout>
        <template #header>

        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('admin.payment-receipt-dashboard')"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-white bg-white dark:bg-gray-900 hover:bg-blue-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:text-white rounded-lg transition-all duration-200 group">
                    <ChevronLeftIcon
                        class="w-5 h-5 mr-1.5 transition-transform duration-200 group-hover:-translate-x-0.5" />
                    Back
                    </Link>
                </div>
                <div class="flex justify-between items-center p-4 bg-white dark:bg-gray-900 rounded-lg mb-2 shadow-lg">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 bg-blue-100 dark:bg-blue-500 rounded-lg">
                            <ClipboardDocumentListIcon class="h-6 w-6 text-blue-600 dark:text-white" />
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Receipt Distribution
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Manage and track receipt distributions to officers
                            </p>
                        </div>
                    </div>
                </div>


                <!-- Distribution Form Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">

                    <form @submit.prevent="submitDistribution" class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Branch Selection -->
                            <div v-if="!userBranch" class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Branch
                                </label>
                                <div class="relative">
                                    <BuildingOfficeIcon
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <select v-model="form.branch_id" @change="loadOfficers"
                                        class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 dark:focus:ring-blue-400">
                                        <option value="">Select Branch</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id"
                                            :class="getBranchStock(branch.id) === 0 ? 'text-gray-400' : ''">
                                            {{ branch.branch_name }} ({{ getBranchStock(branch.id) }} available)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Officer Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Officer
                                </label>
                                <div class="relative">
                                    <UserIcon
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <select v-model="form.branch_officer_id"
                                        class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        :disabled="!form.branch_id">
                                        <option value="">Select Officer</option>
                                        <option v-for="officer in branchOfficers" :key="officer.id" :value="officer.id">
                                            {{ officer.name }} (PIN: {{ officer.pin_number }})
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Quantity Input -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Quantity
                                </label>
                                <div class="relative">
                                    <HashtagIcon
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input type="number" v-model="form.quantity" min="1" :max="maxAvailable"
                                        class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        placeholder="Enter quantity">
                                </div>
                                <p v-if="maxAvailable" class="text-sm text-gray-500 dark:text-gray-400">
                                    Available: {{ maxAvailable }}
                                </p>
                            </div>

                            <!-- Date Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Distribution Date
                                </label>
                                <div class="relative">
                                    <CalendarIcon
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                    <input type="date" v-model="form.distribution_date"
                                        class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                </div>
                            </div>

                            <!-- Notes Textarea -->
                            <div class="col-span-full space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Notes
                                </label>
                                <div class="relative">
                                    <DocumentTextIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                    <textarea v-model="form.notes" rows="3"
                                        class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        placeholder="Add any additional notes here..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-4">
                            <button type="submit" :disabled="form.processing || !isFormValid" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700
                                       dark:bg-blue-500 dark:hover:bg-blue-600 disabled:opacity-50
                                       transition-colors duration-200 flex items-center space-x-2">
                                <DocumentPlusIcon v-if="!form.processing" class="h-5 w-5" />
                                <ArrowPathIcon v-else class="h-5 w-5 animate-spin" />
                                <span>{{ form.processing ? 'Processing...' : 'Distribute Receipts' }}</span>
                            </button>
                        </div>
                    </form>
                </div>



                <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Recent Distributions
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    View and track recent receipt distributions
                                </p>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Total Records: {{ recentDistributions.total }}
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Distribution Date
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Officer Info
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Branch
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Notes
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Distributed By
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-if="recentDistributions.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No distributions found
                                    </td>
                                </tr>
                                <tr v-for="distribution in recentDistributions.data" :key="distribution.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ formatDate(distribution.distribution_date) }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatTime(distribution.created_at) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div
                                                    class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                    <UserIcon class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ distribution.officer.name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    PIN: {{ distribution.officer.pin_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ distribution.branch.branch_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2.5 py-1 text-sm rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ distribution.quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900 dark:text-gray-100 truncate max-w-xs">
                                            {{ distribution.notes || '-' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ distribution.user.name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ formatDate(distribution.created_at) }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <Pagination :links="recentDistributions.links" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

import {
    UserIcon,
    BuildingOfficeIcon,
    HashtagIcon,
    CalendarIcon,
    DocumentTextIcon,
    DocumentPlusIcon,
    ArrowPathIcon,
    ClipboardDocumentListIcon,
    ChevronLeftIcon
} from '@heroicons/vue/24/outline';

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


const formatTime = (datetime) => {
    return new Date(datetime).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

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
.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    @apply ring-2 ring-blue-500 dark:ring-blue-400 border-transparent;
}

.form-input.error,
.form-select.error,
.form-textarea.error {
    @apply border-red-500 dark:border-red-500;
}

.error-message {
    @apply text-red-500 dark:text-red-400 text-sm mt-1;
}

/* Add smooth transitions for dark mode */
* {
    @apply transition-colors duration-200;
}
</style>
