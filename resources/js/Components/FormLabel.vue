<template>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        <slot>{{ label }}</slot>
        <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    }
});
</script>
