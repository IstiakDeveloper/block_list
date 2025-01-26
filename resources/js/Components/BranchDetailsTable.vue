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
    <div class="flex-1 bg-white dark:bg-gray-800 shadow rounded-lg w-1/2">
        <div class="px-4 py-4 sm:px-6 flex justify-between items-center border-b dark:border-gray-700">
            <h3 class="text-base font-medium text-gray-900 dark:text-white">Branch-wise Reports</h3>
        </div>


        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="th-cell">Branch Name</th>
                        <th class="th-cell">Total Customers</th>
                        <th class="th-cell">This Month</th>
                        <th class="th-cell">Last 7 Days</th>
                        <th class="th-cell text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="branch in branches" :key="branch.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <td class="td-cell font-medium">
                            {{ branch.name }}
                        </td>
                        <td class="td-cell text-gray-500">
                            {{ formatNumber(branch.total_customers) }}
                        </td>
                        <td class="td-cell text-gray-500">
                            {{ formatNumber(branch.this_month) }}
                        </td>
                        <td class="td-cell text-gray-500">
                            {{ formatNumber(branch.last_7_days) }}
                        </td>
                        <td class="td-cell text-right">
                            <button @click="emit('download', branch.id)" class="download-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
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
                        <td class="td-cell font-medium">Total</td>
                        <td class="td-cell text-gray-500 font-semibold">
                            {{ formatNumber(branches.reduce((acc, branch) => acc + branch.total_customers, 0)) }}
                        </td>
                        <td class="td-cell text-gray-500 font-semibold">
                            {{ formatNumber(branches.reduce((acc, branch) => acc + branch.this_month, 0)) }}
                        </td>
                        <td class="td-cell text-gray-500 font-semibold">
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
    @apply px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
}

.td-cell {
    @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white;
}

.download-btn {
    @apply text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300
           transition-colors duration-150 inline-flex items-center;
}


</style>
