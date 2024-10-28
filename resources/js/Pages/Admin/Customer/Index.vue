<template>
    <Head title="Customers" />

    <AdminLayout>
        <div class="container mx-auto py-4 sm:py-8 px-4">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-300">Customers</h1>
                    <div class="flex items-center space-x-4">
                        <!-- Branch Filter Dropdown for Super Admin -->
                        <div v-if="user.name === 'Super Admin'" class="relative">
                            <select
                                v-model="selectedBranch"
                                @change="filterByBranch"
                                class="block w-48 px-4 py-2 text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">All Branches</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.branch_name }}
                                </option>
                            </select>
                        </div>

                        <Link
                            v-if="user.name !== 'Super Admin'"
                            href="/admin/customers/create"
                            class="btn-primary flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
                            </svg>
                            Add Customer
                        </Link>
                    </div>
                </div>

                <!-- Rest of the template remains the same until the table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Table headers remain the same -->
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Serial Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">NID Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <!-- Table body remains the same -->
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr v-for="(customer, index) in customers.data" :key="customer.id">
                                <!-- Table rows remain the same -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ customer.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ customer.nid_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ customer.branch.branch_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ formatDate(customer.created_at) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <!-- Action buttons remain the same -->
                                    <Link
                                        :href="route('admin.customers.show', customer.id)"
                                        class="inline-flex items-center text-green-600 hover:text-green-800 bg-green-100 hover:bg-green-200 dark:bg-green-600 dark:text-white dark:hover:bg-green-500 dark:hover:text-gray-100 px-3 py-2 rounded transition duration-200"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927C9.469 2.607 10 2.903 10 3.5v13c0 .597-.531.893-.951.573l-7.902-6.5a.75.75 0 010-1.146l7.902-6.5z" />
                                        </svg>
                                        Show
                                    </Link>

                                    <Link
                                        v-if="user.name === 'Super Admin'"
                                        :href="route('admin.customers.edit', customer.id)"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500 dark:hover:text-gray-100 px-3 py-2 rounded transition duration-200 ml-4"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M12.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-9 9a1 1 0 01-.353.213l-3 1a1 1 0 01-1.293-1.293l1-3a1 1 0 01.213-.353l9-9z" />
                                        </svg>
                                        Edit
                                    </Link>

                                    <button
                                        v-if="user.name === 'Super Admin'"
                                        @click="confirmDelete(customer.id)"
                                        class="inline-flex items-center text-red-600 hover:text-red-800 bg-red-100 hover:bg-red-200 dark:bg-red-600 dark:text-white dark:hover:bg-red-500 dark:hover:text-gray-100 px-3 py-2 rounded transition duration-200 ml-4"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M6 2a1 1 0 00-1 1v1h12V3a1 1 0 00-1-1H6z" />
                                            <path fill-rule="evenodd" d="M5 4h10a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1zm1 1v13h8V5H6z" clip-rule="evenodd" />
                                        </svg>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination section remains the same -->
                <div class="mt-4 flex justify-between items-center">
                    <div class="flex justify-center items-center">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ customers.from }} to {{ customers.to }} of {{ customers.total }} customers
                        </span>
                    </div>
                    <div class="flex justify-center items-center">
                        <button
                            @click="previousPage"
                            :disabled="!customers.prev_page_url"
                            class="btn-secondary px-4 py-2"
                            :class="{'opacity-50 cursor-not-allowed': !customers.prev_page_url}"
                        >
                            Previous
                        </button>
                        <button
                            @click="nextPage"
                            :disabled="!customers.next_page_url"
                            class="btn-secondary px-4 py-2 ml-2"
                            :class="{'opacity-50 cursor-not-allowed': !customers.next_page_url}"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationDialog
            :show="isDialogVisible"
            @update:show="isDialogVisible = $event"
            @confirm="deleteCustomer"
        />
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import { ref, computed, onMounted } from 'vue';

// Props
const props = defineProps({
    customers: Object,
    auth: Object,
    branches: {
        type: Array,
        default: () => []
    }
});

// State variables
const isDialogVisible = ref(false);
const customerToDelete = ref(null);
const selectedBranch = ref('');
const form = useForm({});

// Computed
const user = computed(() => props.auth.user);
const isSuperAdmin = computed(() => user.value.name === 'Super Admin');

// Methods
function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

function filterByBranch() {
    const params = new URLSearchParams(window.location.search);
    if (selectedBranch.value) {
        params.set('branch', selectedBranch.value);
    } else {
        params.delete('branch');
    }

    router.get(`/admin/customers?${params.toString()}`, {}, {
        preserveState: true,
        preserveScroll: true,
    });
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
            },
        });
    }
}

function previousPage() {
    if (props.customers.prev_page_url) {
        router.get(props.customers.prev_page_url);
    }
}

function nextPage() {
    if (props.customers.next_page_url) {
        router.get(props.customers.next_page_url);
    }
}

// Initialize selected branch from URL params if present
onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const branchParam = params.get('branch');
    if (branchParam) {
        selectedBranch.value = branchParam;
    }
});
</script>

<style scoped>
.btn-primary {
    @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700 transition duration-200;
}

.btn-secondary {
    @apply bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded shadow hover:bg-gray-400 transition duration-200;
}
</style>
