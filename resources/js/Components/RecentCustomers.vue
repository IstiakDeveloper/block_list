<script setup>
defineProps({
    customers: { type: Array, required: true }
});
</script>

<template>
    <div class="chart-card mt-8">
        <h3 class="chart-title">Recent Customers</h3>
        <div class="overflow-x-auto">
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
                            <span v-if="customer.name_bn" class="text-xs text-gray-500 dark:text-gray-400 block">
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
    @apply bg-white dark:bg-gray-800 shadow rounded-lg p-6;
}

.chart-title {
    @apply text-lg font-medium text-gray-900 dark:text-white mb-4;
}

.table-header {
    @apply px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
}

.table-cell {
    @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white;
}
</style>
