<template>
    <Head title="Customers" />

    <AdminLayout>
        <div class="container px-4 py-8 mx-auto">
            <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <!-- Header and Filters -->
                <div
                    class="flex flex-col items-start justify-between p-4 space-y-4 sm:p-6 sm:flex-row sm:items-center sm:space-y-0">
                    <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl dark:text-gray-300">Customers</h1>

                    <div
                        class="flex flex-col items-start w-full space-y-4 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4 sm:w-auto">
                        <!-- Search Input -->
                        <div class="relative w-full sm:w-64">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Search by name, NID..."
                                @input="handleSearchInput"
                                class="w-full px-4 py-2 pl-10 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500"
                            />
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Branch Filter -->
                        <div v-if="user.name === 'Super Admin' || branches.length > 1" class="w-full sm:w-48">
                            <select v-model="selectedBranch" @change="filterByBranch"
                                class="w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                                <option value="">All Branches</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.branch_name }}
                                </option>
                            </select>
                        </div>
                        <!-- Show selected branch name when user has only one branch -->
                        <div v-else-if="branches.length === 1" class="text-sm text-gray-600 dark:text-gray-400">
                            Branch: {{ branches[0].branch_name }}
                        </div>
                        <!-- Add Customer Button -->
                        <Link v-if="user.name !== 'Super Admin'" href="/admin/customers/create"
                            class="flex items-center justify-center w-full btn-primary sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
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
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase sm:table-cell dark:text-gray-300">
                                    Serial Number
                                </th>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Name
                                </th>
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase md:table-cell dark:text-gray-300">
                                    NID Number
                                </th>
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase lg:table-cell dark:text-gray-300">
                                    Branch
                                </th>
                                <th
                                    class="hidden p-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase xl:table-cell dark:text-gray-300">
                                    Created At
                                </th>
                                <th
                                    class="p-4 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr v-for="(customer, index) in customers.data" :key="customer.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td
                                    class="hidden p-4 text-sm text-gray-900 sm:table-cell whitespace-nowrap dark:text-gray-100">
                                    {{ index + 1 }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{
                                            customer.name }}</span>
                                        <!-- Mobile-only info -->
                                        <span class="mt-1 text-xs text-gray-500 md:hidden">NID: {{ customer.nid_number
                                            }}</span>
                                        <span class="mt-1 text-xs text-gray-500 lg:hidden">Branch: {{
                                            customer.branch.branch_name }}</span>
                                    </div>
                                </td>
                                <td
                                    class="hidden p-4 text-sm text-gray-900 md:table-cell whitespace-nowrap dark:text-gray-100">
                                    {{ customer.nid_number }}
                                </td>
                                <td
                                    class="hidden p-4 text-sm text-gray-900 lg:table-cell whitespace-nowrap dark:text-gray-100">
                                    {{ customer.branch.branch_name }}
                                </td>
                                <td
                                    class="hidden p-4 text-sm text-gray-500 xl:table-cell whitespace-nowrap dark:text-gray-400">
                                    {{ formatDate(customer.created_at) }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div
                                        class="flex flex-col items-end justify-end space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2">
                                        <Link :href="route('admin.customers.show', customer.id)"
                                            class="w-full btn-action btn-show sm:w-auto">
                                        <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M9.049 2.927C9.469 2.607 10 2.903 10 3.5v13c0 .597-.531.893-.951.573l-7.902-6.5a.75.75 0 010-1.146l7.902-6.5z" />
                                        </svg>
                                        <span>Show</span>
                                        </Link>

                                        <Link v-if="user.name === 'Super Admin'"
                                            :href="route('admin.customers.edit', customer.id)"
                                            class="w-full btn-action btn-edit sm:w-auto">
                                        <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M12.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-9 9a1 1 0 01-.353.213l-3 1a1 1 0 01-1.293-1.293l1-3a1 1 0 01.213-.353l9-9z" />
                                        </svg>
                                        <span>Edit</span>
                                        </Link>

                                        <button v-if="user.name === 'Super Admin'" @click="confirmDelete(customer.id)"
                                            class="w-full btn-action btn-delete sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 2a1 1 0 00-1 1v1h12V3a1 1 0 00-1-1H6z" />
                                                <path fill-rule="evenodd"
                                                    d="M5 4h10a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1zm1 1v13h8V5H6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- No customers found message -->
                            <tr v-if="customers.data.length === 0">
                                <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                    <div class="py-6">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 text-lg font-medium">No customers found</p>
                                        <p class="mt-1">Try adjusting your search or filter criteria</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :data="customers" item-name="customers" :extra-params="paginationParams" />
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
    }
});

// State variables
const isDialogVisible = ref(false);
const customerToDelete = ref(null);
const selectedBranch = ref(
    props.branches.length === 1 ? props.branches[0].id : (props.filters.branch || '')
);
const search = ref(props.filters.search || '');
const form = useForm({});

// Computed
const user = computed(() => props.auth.user);

const hasMultipleBranches = computed(() =>
    props.auth.user.name === 'Super Admin' || props.userBranches?.length > 1
);

const paginationParams = computed(() => ({
    branch: selectedBranch.value,
    search: search.value
}));

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
    const params = new URLSearchParams();

    if (selectedBranch.value) {
        params.set('branch', selectedBranch.value);
    }

    if (search.value) {
        params.set('search', search.value);
    }

    router.get(`/admin/customers?${params.toString()}`, {}, {
        preserveState: true,
        preserveScroll: true,
    });
}

function filterByBranch() {
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
            },
        });
    }
}

onMounted(() => {
    if (props.branches.length === 1 && !props.filters.branch) {
        filterByBranch();
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
