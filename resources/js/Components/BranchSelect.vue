<!-- BranchSelect.vue -->
<template>
    <div class="relative" ref="dropdownRef">
        <!-- Selected items display -->
        <div class="min-h-[42px] w-full border rounded-md bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
            <div class="flex flex-wrap gap-1.5 p-1.5" @click="isOpen = true">
                <template v-if="modelValue.length > 0">
                    <span v-for="branchId in modelValue" :key="branchId"
                        class="inline-flex items-center px-2 py-1 rounded-md text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        {{ getBranchName(branchId) }}
                        <button @click.stop="removeBranch(branchId)"
                            class="ml-1 hover:text-blue-600 dark:hover:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                </template>
                <div v-else class="px-2 py-1.5 text-gray-500 dark:text-gray-400">
                    Select branches...
                </div>
            </div>
            <button type="button"
                @click="isOpen = !isOpen"
                class="absolute right-0 top-0 h-full px-2 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                    :class="['h-5 w-5 transition-transform duration-200', { 'transform rotate-180': isOpen }]"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Dropdown -->
        <div v-show="isOpen"
            class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg">
            <!-- Search input -->
            <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                <input type="text"
                    v-model="searchTerm"
                    class="w-full px-3 py-1.5 text-sm border rounded-md bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search branches...">
            </div>

            <!-- Options list -->
            <div class="max-h-60 overflow-y-auto">
                <template v-if="filteredBranches.length">
                    <div v-for="branch in filteredBranches"
                        :key="branch.id"
                        @click="toggleBranch(branch.id)"
                        class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="flex items-center justify-center w-5 h-5 border rounded mr-2 border-gray-300 dark:border-gray-600">
                            <svg v-if="isSelected(branch.id)" xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-200">
                            {{ branch.branch_name }}
                        </span>
                    </div>
                </template>
                <div v-else class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    No branches found
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        required: true
    },
    branches: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchTerm = ref('');
const dropdownRef = ref(null);

const filteredBranches = computed(() => {
    return props.branches.filter(branch =>
        branch.branch_name.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

const getBranchName = (branchId) => {
    const branch = props.branches.find(b => b.id === branchId);
    return branch ? branch.branch_name : '';
};

const isSelected = (branchId) => {
    return props.modelValue.includes(branchId);
};

const toggleBranch = (branchId) => {
    const newSelection = isSelected(branchId)
        ? props.modelValue.filter(id => id !== branchId)
        : [...props.modelValue, branchId];
    emit('update:modelValue', newSelection);
};

const removeBranch = (branchId) => {
    emit('update:modelValue', props.modelValue.filter(id => id !== branchId));
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
