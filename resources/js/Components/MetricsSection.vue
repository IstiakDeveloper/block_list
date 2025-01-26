<script setup>
const props = defineProps({
    demographics: { type: Array, required: true },
    stats: { type: Object, required: true }
});
</script>

<template>
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Demographics -->
        <div class="metric-card">
            <h3 class="metric-title">Customer Demographics</h3>
            <div class="space-y-4">
                <div v-for="(count, ageGroup) in demographics" :key="ageGroup" class="flex items-center justify-between">
                    <span class="metric-label">{{ ageGroup }}</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                            <div class="bg-blue-600 h-2 rounded-full"
                                :style="{ width: `${(count.count / stats.totalCustomers * 100)}%` }">
                            </div>
                        </div>
                        <span class="metric-value">{{ Math.round(count.count / stats.totalCustomers * 100) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Growth -->
        <div class="metric-card">
            <h3 class="metric-title">Monthly Growth</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="metric-label">Growth Rate</span>
                    <span class="text-sm font-medium text-green-600 dark:text-green-400">
                        {{ stats.growthRate }}%
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="metric-label">This Month</span>
                    <span class="metric-value">{{ stats.currentMonthCustomers }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="metric-label">Last Month</span>
                    <span class="metric-value">{{ stats.lastMonthCustomers }}</span>
                </div>
            </div>
        </div>

        <!-- Branch Performance -->
        <div class="metric-card">
            <h3 class="metric-title">Branch Performance</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="metric-label">Top Performing</span>
                    <span class="metric-value">{{ stats.topPerformingBranch?.name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="metric-label">Most Recent Growth</span>
                    <span class="metric-value">{{ stats.mostRecentGrowthBranch?.name }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.metric-card {
    @apply bg-white dark:bg-gray-800 shadow rounded-lg p-6;
}

.metric-title {
    @apply text-lg font-medium text-gray-900 dark:text-white mb-4;
}

.metric-label {
    @apply text-sm text-gray-500 dark:text-gray-400;
}

.metric-value {
    @apply text-sm font-medium text-gray-900 dark:text-white;
}
</style>
