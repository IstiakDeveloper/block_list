<template>
    <Head title="Customers" />

    <AdminLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <!-- Header and Filters -->
                <div class="p-4 sm:p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-300">Customers</h1>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                        <!-- Branch Filter -->
                        <div v-if="user.name === 'Super Admin'" class="w-full sm:w-48">
                            <select
                                v-model="selectedBranch"
                                @change="filterByBranch"
                                class="w-full px-4 py-2 text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">All Branches</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.branch_name }}
                                </option>
                            </select>
                        </div>

                        <!-- Add Customer Button -->
                        <Link
                            v-if="user.name !== 'Super Admin'"
                            href="/admin/customers/create"
                            class="btn-primary w-full sm:w-auto flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
                            </svg>
                            Add Customer
                        </Link>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="p-4 hidden sm:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Serial Number
                                </th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="p-4 hidden md:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    NID Number
                                </th>
                                <th class="p-4 hidden lg:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Branch
                                </th>
                                <th class="p-4 hidden xl:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Created At
                                </th>
                                <th class="p-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr v-for="(customer, index) in customers.data"
                                :key="customer.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-4 hidden sm:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ index + 1 }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ customer.name }}</span>
                                        <!-- Mobile-only info -->
                                        <span class="text-xs text-gray-500 md:hidden mt-1">NID: {{ customer.nid_number }}</span>
                                        <span class="text-xs text-gray-500 lg:hidden mt-1">Branch: {{ customer.branch.branch_name }}</span>
                                    </div>
                                </td>
                                <td class="p-4 hidden md:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ customer.nid_number }}
                                </td>
                                <td class="p-4 hidden lg:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ customer.branch.branch_name }}
                                </td>
                                <td class="p-4 hidden xl:table-cell whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatDate(customer.created_at) }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                                        <Link :href="route('admin.customers.show', customer.id)"
                                              class="btn-action btn-show w-full sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927C9.469 2.607 10 2.903 10 3.5v13c0 .597-.531.893-.951.573l-7.902-6.5a.75.75 0 010-1.146l7.902-6.5z" />
                                            </svg>
                                            <span>Show</span>
                                        </Link>

                                        <Link v-if="user.name === 'Super Admin'"
                                              :href="route('admin.customers.edit', customer.id)"
                                              class="btn-action btn-edit w-full sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M12.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-9 9a1 1 0 01-.353.213l-3 1a1 1 0 01-1.293-1.293l1-3a1 1 0 01.213-.353l9-9z" />
                                            </svg>
                                            <span>Edit</span>
                                        </Link>

                                        <button v-if="user.name === 'Super Admin'"
                                                @click="confirmDelete(customer.id)"
                                                class="btn-action btn-delete w-full sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 2a1 1 0 00-1 1v1h12V3a1 1 0 00-1-1H6z" />
                                                <path fill-rule="evenodd" d="M5 4h10a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1zm1 1v13h8V5H6z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <Pagination
                    :data="customers"
                    item-name="customers"
                    :extra-params="{ branch: selectedBranch }"
                />
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
import Pagination from '@/Components/Pagination.vue';  // Add this import
import { ref, computed } from 'vue';

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
</script>

<style scoped>
.btn-primary {
    @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700 transition duration-200;
}

.btn-secondary {
    @apply bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded shadow hover:bg-gray-400 transition duration-200;
}

.btn-action {
    @apply inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded transition duration-200;
}

.btn-show {
    @apply text-green-600 bg-green-100 hover:bg-green-200 dark:bg-green-600 dark:text-white dark:hover:bg-green-500;
}

.btn-edit {
    @apply text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500;
}

.btn-delete {
    @apply text-red-600 bg-red-100 hover:bg-red-200 dark:bg-red-600 dark:text-white dark:hover:bg-red-500;
}
</style>
