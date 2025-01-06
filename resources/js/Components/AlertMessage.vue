<template>
    <div v-if="message"
         class="mb-4 p-4 rounded-md"
         :class="typeClasses">
        {{ message }}
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'error'
    },
    message: {
        type: String,
        default: ''
    }
});

const typeClasses = computed(() => {
    switch (props.type) {
        case 'success':
            return 'bg-green-50 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'error':
            return 'bg-red-50 text-red-800 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
});
</script>
