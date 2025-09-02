<script setup>
defineProps({
    branches: { type: Array, required: true }
});

const emit = defineEmits(['download']);

const formatNumber = (num) => {
    return new Intl.NumberFormat().format(num);
};
</script>

<template>
    <div class="flex-1 bg-white rounded-lg shadow dark:bg-gray-800 lg:w-1/2">
        <div class="flex flex-col px-4 py-4 space-y-2 border-b sm:px-6 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 dark:border-gray-700">
            <h3 class="text-base font-medium text-gray-900 dark:text-white">Branch-wise Reports</h3>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden">
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div v-for="branch in branches" :key="branch.id" class="p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ branch.name }}</h4>
                            <p class="text-sm text-gray-500">Code: {{ branch.code }}</p>
                        </div>
                        <button @click="emit('download', branch.id)" class="download-btn-mobile">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ formatNumber(branch.total_customers) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">This Month</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ formatNumber(branch.this_month) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">7 Days</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ formatNumber(branch.last_7_days) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden overflow-x-auto sm:block">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="th-cell">Branch Name</th>
                        <th class="th-cell">Total Customers</th>
                        <th class="th-cell">This Month</th>
                        <th class="th-cell">Last 7 Days</th>
                        <th class="text-right th-cell">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="branch in branches" :key="branch.id"
                        class="transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="font-medium td-cell">
                            {{ branch.name }}
                        </td>
                        <td class="text-gray-500 td-cell">
                            {{ formatNumber(branch.total_customers) }}
                        </td>
                        <td class="text-gray-500 td-cell">
                            {{ formatNumber(branch.this_month) }}
                        </td>
                        <td class="text-gray-500 td-cell">
                            {{ formatNumber(branch.last_7_days) }}
                        </td>
                        <td class="text-right td-cell">
                            <button @click="emit('download', branch.id)" class="download-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <td class="font-medium td-cell">Total</td>
                        <td class="font-semibold text-gray-500 td-cell">
                            {{ formatNumber(branches.reduce((acc, branch) => acc + branch.total_customers, 0)) }}
                        </td>
                        <td class="font-semibold text-gray-500 td-cell">
                            {{ formatNumber(branches.reduce((acc, branch) => acc + branch.this_month, 0)) }}
                        </td>
                        <td class="font-semibold text-gray-500 td-cell">
                            {{ formatNumber(branches.reduce((acc, branch) => acc + branch.last_7_days, 0)) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<style scoped>
.th-cell {
    @apply px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
}

.td-cell {
    @apply px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white;
}

.download-btn {
    @apply text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300
           transition-colors duration-150 inline-flex items-center text-sm;
}

.download-btn-mobile {
    @apply text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300
           transition-colors duration-150 p-2 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900;
}
</style>
