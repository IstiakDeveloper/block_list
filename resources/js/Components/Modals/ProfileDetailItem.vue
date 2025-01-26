
<template>
    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
            {{ label }}
        </p>
        <p class="text-base font-semibold text-gray-800 dark:text-gray-200">
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
