<script setup>
defineProps({
    stats: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="grid grid-cols-1 gap-4 mt-6 sm:mt-8 sm:grid-cols-2 lg:grid-cols-4 sm:gap-5">
        <!-- Total Customers -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="bg-blue-100 stat-icon dark:bg-blue-900">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-blue-600 sm:h-6 sm:w-6 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0 ml-4 sm:ml-5">
                    <p class="stat-label">Total Customers</p>
                    <p class="stat-value">
                        {{ stats.totalCustomers }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Branches -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="bg-green-100 stat-icon dark:bg-green-900">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-green-600 sm:h-6 sm:w-6 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0 ml-4 sm:ml-5">
                    <p class="stat-label">Total Branches</p>
                    <p class="stat-value">
                        {{ stats.totalBranches }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Growth Rate -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="bg-yellow-100 stat-icon dark:bg-yellow-900">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-yellow-600 sm:h-6 sm:w-6 dark:text-yellow-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0 ml-4 sm:ml-5">
                    <p class="stat-label">Growth Rate</p>
                    <p class="stat-value">
                        {{ stats.growthRate }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Current Month -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="bg-purple-100 stat-icon dark:bg-purple-900">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-purple-600 sm:h-6 sm:w-6 dark:text-purple-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0 ml-4 sm:ml-5">
                    <p class="stat-label">This Month</p>
                    <p class="stat-value">
                        {{ stats.currentMonthCustomers }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.stat-card {
    @apply bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-5;
}

.stat-icon {
    @apply flex-shrink-0 p-2 sm:p-3 rounded-full;
}

.stat-label {
    @apply text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 truncate;
}

.stat-value {
    @apply text-lg sm:text-2xl font-semibold text-gray-900 dark:text-white;
}
</style>
