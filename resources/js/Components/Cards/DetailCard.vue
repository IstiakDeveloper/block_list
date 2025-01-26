<template>
    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-4 border dark:border-gray-600">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
            {{ label }}
        </p>
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ value || 'N/A' }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        default: null
    }
});
</script>
