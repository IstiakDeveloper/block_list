<template>
    <div class="relative">
        <input
            type="date"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full"
            :required="required"
        />
        <input
            type="text"
            v-model="displayValue"
            :placeholder="placeholder"
            @input="handleManualInput"
            @blur="validateAndFormat"
            :class="[
                'block w-full rounded-md shadow-sm',
                'dark:bg-gray-700 dark:text-gray-300',
                'border-gray-300 dark:border-gray-600',
                'focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50',
                inputClass
            ]"
        />
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        required: true
    },
    required: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: 'dd/mm/yyyy'
    },
    inputClass: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

// Local display value for the input
const displayValue = ref('');

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        const [year, month, day] = newValue.split('-');
        displayValue.value = `${day}/${month}/${year}`;
    } else {
        displayValue.value = '';
    }
}, { immediate: true });

// Handle manual input
const handleManualInput = (event) => {
    let value = event.target.value;

    // Allow only numbers and forward slashes
    value = value.replace(/[^\d/]/g, '');

    // Automatically add slashes
    if (value.length === 2 && !value.includes('/')) {
        value += '/';
    } else if (value.length === 5 && value.charAt(2) === '/' && !value.includes('/', 3)) {
        value += '/';
    }

    // Limit the length
    if (value.length > 10) {
        value = value.slice(0, 10);
    }

    displayValue.value = value;
};

// Validate and format the date
const validateAndFormat = () => {
    if (!displayValue.value) {
        emit('update:modelValue', '');
        return;
    }

    const parts = displayValue.value.split('/');
    if (parts.length !== 3) {
        resetToModelValue();
        return;
    }

    let [day, month, year] = parts;

    // Pad single digits
    day = day.padStart(2, '0');
    month = month.padStart(2, '0');

    // Basic validation
    if (year.length !== 4 ||
        parseInt(month) < 1 || parseInt(month) > 12 ||
        parseInt(day) < 1 || parseInt(day) > 31) {
        resetToModelValue();
        return;
    }

    // Check valid date
    const date = new Date(year, month - 1, day);
    if (isNaN(date.getTime()) ||
        date.getDate() !== parseInt(day) ||
        date.getMonth() !== parseInt(month) - 1 ||
        date.getFullYear() !== parseInt(year)) {
        resetToModelValue();
        return;
    }

    // Format for v-model (YYYY-MM-DD)
    const formattedDate = `${year}-${month}-${day}`;
    emit('update:modelValue', formattedDate);

    // Update display value with consistent formatting
    displayValue.value = `${day}/${month}/${year}`;
};

// Reset to the current model value if invalid input
const resetToModelValue = () => {
    if (props.modelValue) {
        const [year, month, day] = props.modelValue.split('-');
        displayValue.value = `${day}/${month}/${year}`;
    } else {
        displayValue.value = '';
    }
};
</script>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    margin: 0;
    opacity: 0;
    cursor: pointer;
}
</style>
