<script setup>
defineProps({
    customers: { type: Array, required: true }
});
</script>

<template>
    <div class="mt-8 chart-card">
        <h3 class="chart-title">Recent Customers</h3>

        <!-- Mobile Card View -->
        <div class="block sm:hidden">
            <div class="space-y-3">
                <div v-for="customer in customers" :key="customer.id"
                     class="p-3 border border-gray-200 rounded-lg dark:border-gray-700">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                {{ customer.name }}
                            </h4>
                            <p v-if="customer.name_bn" class="text-xs text-gray-500 dark:text-gray-400">
                                {{ customer.name_bn }}
                            </p>
                        </div>
                        <span class="flex-shrink-0 ml-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ new Date(customer.created_at).toLocaleDateString() }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">{{ customer.branch.branch_name }}</span>
                        <span class="text-gray-500 dark:text-gray-400">{{ customer.phone_number }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden overflow-x-auto sm:block">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="table-header">Name</th>
                        <th class="table-header">Branch</th>
                        <th class="table-header">Phone</th>
                        <th class="table-header">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="customer in customers" :key="customer.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="table-cell">
                            {{ customer.name }}
                            <span v-if="customer.name_bn" class="block text-xs text-gray-500 dark:text-gray-400">
                                {{ customer.name_bn }}
                            </span>
                        </td>
                        <td class="table-cell text-gray-500">{{ customer.branch.branch_name }}</td>
                        <td class="table-cell text-gray-500">{{ customer.phone_number }}</td>
                        <td class="table-cell text-gray-500">
                            {{ new Date(customer.created_at).toLocaleDateString() }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.chart-card {
    @apply bg-white dark:bg-gray-800 shadow rounded-lg p-4 sm:p-6;
}

.chart-title {
    @apply text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-4;
}

.table-header {
    @apply px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
}

.table-cell {
    @apply px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white;
}
</style>
