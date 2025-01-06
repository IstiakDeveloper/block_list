// /resources/js/Components/DateRangePicker.vue
<template>
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <form @submit.prevent="applyRange" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    From Date
                </label>
                <div class="relative">
                    <input
                        type="date"
                        v-model="localRange.from"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        :max="localRange.to"
                    />
                </div>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    To Date
                </label>
                <div class="relative">
                    <input
                        type="date"
                        v-model="localRange.to"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        :min="localRange.from"
                    />
                </div>
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    :disabled="!isValidRange || isLoading"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span :class="{ 'opacity-0': isLoading }" class="mr-2">Apply</span>
                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>

                <button
                    type="button"
                    @click="resetRange"
                    :disabled="isLoading"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 disabled:opacity-50"
                >
                    Reset
                </button>
            </div>
        </form>

        <div v-if="error"
            class="mt-2 text-sm text-red-600 dark:text-red-400"
            role="alert"
        >
            {{ error }}
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    initialRange: {
        type: Object,
        required: true,
        default: () => ({
            from: '',
            to: ''
        })
    }
})

const emit = defineEmits(['update:range'])

const localRange = ref({
    from: props.initialRange.from,
    to: props.initialRange.to
})

const error = ref('')
const isLoading = ref(false)

const isValidRange = computed(() => {
    if (!localRange.value.from || !localRange.value.to) return false
    return new Date(localRange.value.from) <= new Date(localRange.value.to)
})

const applyRange = async () => {
    if (!isValidRange.value) {
        error.value = 'Please select a valid date range'
        return
    }

    error.value = ''
    isLoading.value = true

    try {
        await emit('update:range', {
            from: localRange.value.from,
            to: localRange.value.to
        })
    } finally {
        isLoading.value = false
    }
}

const resetRange = () => {
    localRange.value = {
        from: props.initialRange.from,
        to: props.initialRange.to
    }
    error.value = ''
    emit('update:range', localRange.value)
}

// Watch for external changes
watch(() => props.initialRange, (newRange) => {
    if (newRange.from !== localRange.value.from || newRange.to !== localRange.value.to) {
        localRange.value = {
            from: newRange.from,
            to: newRange.to
        }
    }
}, { deep: true })
</script>
