<script setup>
import { ref, watch } from 'vue';
import CustomDateInput from '@/Components/CustomDateInput.vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: 'all'
    },
    startDate: {
        type: String,
        default: null
    },
    endDate: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update:modelValue', 'update:startDate', 'update:endDate', 'filter']);

const selectedRange = ref(props.modelValue);
const selectedStartDate = ref(props.startDate);
const selectedEndDate = ref(props.endDate);

// Watch for prop changes
watch(() => props.modelValue, (newValue) => {
    selectedRange.value = newValue;
});

watch(() => props.startDate, (newValue) => {
    selectedStartDate.value = newValue;
});

watch(() => props.endDate, (newValue) => {
    selectedEndDate.value = newValue;
});

// Handle changes
const handleRangeChange = (value) => {
    selectedRange.value = value;
    emit('update:modelValue', value);

    if (value !== 'custom') {
        selectedStartDate.value = null;
        selectedEndDate.value = null;
        emit('update:startDate', null);
        emit('update:endDate', null);
    }

    emit('filter');
};

const handleDateChange = (type, value) => {
    if (type === 'start') {
        selectedStartDate.value = value;
        emit('update:startDate', value);
    } else {
        selectedEndDate.value = value;
        emit('update:endDate', value);
    }
    emit('filter');
};
</script>

<template>
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date Range:</label>
                    <select :value="selectedRange" @change="handleRangeChange($event.target.value)"
                        class="filter-select">
                        <option value="all">All Time</option>
                        <option value="month">This Month</option>
                        <option value="week">Last 7 Days</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <template v-if="selectedRange === 'custom'">
                    <div class="flex items-center gap-3">
                        <CustomDateInput v-model="selectedStartDate"
                            @update:modelValue="handleDateChange('start', $event)" :max="selectedEndDate"
                            placeholder="Start Date" class="w-36" />
                        <span class="text-gray-500 dark:text-gray-400">to</span>
                        <CustomDateInput v-model="selectedEndDate" @update:modelValue="handleDateChange('end', $event)"
                            :min="selectedStartDate" placeholder="End Date" class="w-36" />
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.filter-select {
    @apply rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500 min-w-[150px] transition-colors duration-200;
}
</style>
