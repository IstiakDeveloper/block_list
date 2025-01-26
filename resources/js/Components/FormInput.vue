<template>
    <div class="space-y-1">
        <label
            v-if="label"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
        >
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>
        <input
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'w-full rounded-md border shadow-sm',
                error
                    ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500',
                'dark:bg-gray-700 dark:text-white'
            ]"
            :required="required"
        />
        <p v-if="error" class="text-sm text-red-600 mt-1">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    type: { type: String, default: 'text' },
    modelValue: [String, Number],
    error: String,
    required: Boolean
});

defineEmits(['update:modelValue']);
</script>
