<template>
    <div class="space-y-1">
        <label
            v-if="label"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
        >
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>
        <textarea
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'w-full rounded-md border shadow-sm',
                error
                    ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500',
                'dark:bg-gray-700 dark:text-white'
            ]"
            :rows="rows"
        ></textarea>
        <p v-if="error" class="text-sm text-red-600 mt-1">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: ''
    },
    modelValue: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    rows: {
        type: Number,
        default: 3
    },
    required: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);
</script>
