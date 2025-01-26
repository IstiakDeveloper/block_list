<template>
    <div class="space-y-2">
        <FormLabel class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Select Branch
            <span class="text-red-500 ml-1">*</span>
        </FormLabel>

        <!-- Multiple Branch Selection -->
        <template v-if="multipleBranches && branches.length > 1">
            <div class="relative">
                <select
                    v-model="form.branch_id"
                    class="
                        w-full
                        px-3 py-2
                        border
                        rounded-md
                        shadow-sm
                        focus:outline-none
                        focus:ring-2
                        transition-all
                        duration-200
                        text-gray-900
                        dark:text-gray-100
                        bg-white
                        dark:bg-gray-700
                        border-gray-300
                        dark:border-gray-600
                        focus:ring-blue-500
                        dark:focus:ring-blue-400
                        focus:border-blue-500
                        dark:focus:border-blue-400
                        appearance-none
                    "
                    :class="errors.branch_id ? 'border-red-500 focus:ring-red-500' : ''"
                    required
                >
                    <option value="" disabled>Select a branch</option>
                    <option
                        v-for="branch in branches"
                        :key="branch.id"
                        :value="branch.id"
                        class="
                            bg-white
                            dark:bg-gray-700
                            text-gray-900
                            dark:text-gray-100
                            hover:bg-gray-100
                            dark:hover:bg-gray-600
                        "
                    >
                        {{ branch.branch_name }}
                    </option>
                </select>

            </div>
        </template>

        <!-- Single Branch (Predefined or Only One Available) -->
        <template v-else>
            <input
                type="text"
                :value="singleBranchName"
                class="
                    w-full
                    px-3 py-2
                    border
                    rounded-md
                    shadow-sm
                    cursor-not-allowed
                    bg-gray-100
                    dark:bg-gray-700
                    text-gray-600
                    dark:text-gray-300
                    border-gray-300
                    dark:border-gray-600
                "
                readonly
            />
            <input
                type="hidden"
                v-model="form.branch_id"
            />
        </template>

        <FormError
            :message="errors.branch_id"
            class="mt-1 text-sm text-red-600 dark:text-red-400"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import FormLabel from '@/Components/FormLabel.vue';
import FormError from '@/Components/FormError.vue';

const props = defineProps({
    branches: {
        type: Array,
        default: () => []
    },
    form: {
        type: Object,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

// Determine if multiple branches are available
const multipleBranches = computed(() => {
    return props.branches.length > 1;
});

// Get the single branch name when only one is available
const singleBranchName = computed(() => {
    if (props.branches.length === 1) {
        // Automatically set the branch_id if only one branch exists
        props.form.branch_id = props.branches[0].id;
        return props.branches[0].branch_name;
    }
    return '';
});
</script>
