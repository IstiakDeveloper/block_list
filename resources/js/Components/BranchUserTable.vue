<script setup>
defineProps({
    branches: { type: Array, required: true },
    getFilteredTotal: { type: Function, required: true }
});
</script>

<template>
    <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="th-cell">Branch Name</th>
                        <th class="th-cell text-center">Total Entries</th>
                        <th class="th-cell">User Distribution</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="branch in branches" :key="branch.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="td-cell">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ branch.name }}</div>
                            <div class="text-xs text-gray-500">Code: {{ branch.code }}</div>
                        </td>
                        <td class="td-cell">
                            <div class="text-center">
                                <span class="total-badge">{{ getFilteredTotal(branch) }}</span>
                            </div>
                        </td>
                        <td class="td-cell">
                            <div class="space-y-2">
                                <div v-for="user in branch.users" :key="user.name" class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ user.name }}</span>
                                    <div class="flex items-center">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill"
                                                :style="{ width: `${(user.entries / getFilteredTotal(branch) * 100)}%` }">
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500 ml-2">{{ user.entries }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.th-cell {
    @apply px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
}

.td-cell {
    @apply px-6 py-4;
}

.total-badge {
    @apply px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200;
}

.progress-bar-bg {
    @apply w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full mr-2;
}

.progress-bar-fill {
    @apply h-2 bg-blue-600 rounded-full transition-all duration-300;
}
</style>
