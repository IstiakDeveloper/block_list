<script setup>
defineProps({
    branches: { type: Array, required: true },
    getFilteredTotal: { type: Function, required: true }
});

const downloadPdf = (branchId = null) => {
    window.location.href = route('admin.reports.branch-users-pdf', {
        branch_id: branchId,
        dateRange: window.route().params.dateRange,
        startDate: window.route().params.startDate,
        endDate: window.route().params.endDate
    });
};
</script>

<template>
    <div class="flex-1 bg-white dark:bg-gray-800 shadow rounded-lg w-1/2">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Branch User Distribution</h3>
            <button @click="downloadPdf()" class="download-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download PDF
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="th-cell">Branch Name</th>
                        <th class="th-cell text-center">Total Entries</th>
                        <th class="th-cell">User Distribution</th>
                        <th class="th-cell text-right">Actions</th>
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
                        <td class="td-cell text-right">
                            <button @click="downloadPdf(branch.id)" class="action-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </button>
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

.download-btn {
    @apply inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700
           transition-colors duration-200 text-sm font-medium;
}

.action-btn {
    @apply text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200;
}
</style>
