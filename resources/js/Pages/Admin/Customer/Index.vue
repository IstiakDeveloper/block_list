<template>
    <Head title="Customers" />

    <AdminLayout>
        <div class="container mx-auto py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-300">Customers</h1>
                    <Link href="/admin/customers/create" class="btn-primary flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
                        </svg>
                        Add Customer
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">NID Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr v-for="customer in customers.data" :key="customer.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ customer.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ customer.nid_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ customer.branch.branch_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ formatDate(customer.created_at) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <!-- Show Button -->
                                <Link
                                    :href="route('admin.customers.show', customer.id)"
                                    class="inline-flex items-center text-green-600 hover:text-green-800 bg-green-100 hover:bg-green-200 dark:bg-green-600 dark:text-white dark:hover:bg-green-500 dark:hover:text-gray-100 px-3 py-2 rounded transition duration-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927C9.469 2.607 10 2.903 10 3.5v13c0 .597-.531.893-.951.573l-7.902-6.5a.75.75 0 010-1.146l7.902-6.5z" />
                                    </svg>
                                    Show
                                </Link>

                                <!-- Edit Button -->
                                <Link
                                    :href="route('admin.customers.edit', customer.id)"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500 dark:hover:text-gray-100 px-3 py-2 rounded transition duration-200 ml-4"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M12.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-9 9a1 1 0 01-.353.213l-3 1a1 1 0 01-1.293-1.293l1-3a1 1 0 01.213-.353l9-9z" />
                                    </svg>
                                    Edit
                                </Link>

                                <!-- Delete Button -->
                                <button
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

                <Pagination :links="customers.links" class="mt-6" />
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import { ref } from 'vue';

const props = defineProps({
    customers: Object,
});

const isDialogVisible = ref(false);
const customerToDelete = ref(null);
const form = useForm({});

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

function confirmDelete(customerId) {
    customerToDelete.value = customerId;
    isDialogVisible.value = true;
}

function deleteCustomer() {
    if (customerToDelete.value) {
        form.delete(route('admin.customers.destroy', customerToDelete.value), {
            onSuccess: () => {
                isDialogVisible.value = false; // Close dialog after successful deletion
            },
        });
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
</style>
