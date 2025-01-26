<script setup>
defineProps({
    dateRange: String,
    startDate: String,
    endDate: String
});

const emit = defineEmits(['update:dateRange', 'update:startDate', 'update:endDate']);
</script>

<template>
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-4">
                <select
                    :value="dateRange"
                    @change="emit('update:dateRange', $event.target.value)"
                    class="filter-select"
                >
                    <option value="all">All Time</option>
                    <option value="month">This Month</option>
                    <option value="week">Last 7 Days</option>
                    <option value="custom">Custom Range</option>
                </select>

                <template v-if="dateRange === 'custom'">
                    <div class="flex items-center gap-3">
                        <input
                            type="date"
                            :value="startDate"
                            @input="emit('update:startDate', $event.target.value)"
                            class="date-input"
                        >
                        <span class="text-gray-500 dark:text-gray-400">to</span>
                        <input
                            type="date"
                            :value="endDate"
                            @input="emit('update:endDate', $event.target.value)"
                            class="date-input"
                        >
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.filter-select {
    @apply rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
           text-sm focus:border-blue-500 focus:ring-blue-500 min-w-[150px];
}

.date-input {
    @apply rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
           text-sm focus:border-blue-500 focus:ring-blue-500;
}
</style>
