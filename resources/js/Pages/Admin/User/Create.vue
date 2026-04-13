<template>
    <Head title="Create User" />
    <AdminLayout>
        <div class="container py-8 mx-auto">
            <div class="p-8 bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <h1 class="mb-6 text-3xl font-bold text-gray-800 dark:text-gray-300">Create User</h1>

                <form @submit.prevent="createUser">
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                        <TextInput type="text" v-model="form.name"
                            class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm dark:border-gray-600"
                            required />
                        <InputError v-if="form.errors.name" :message="form.errors.name" class="mt-1" />
                    </div>
                    <!-- Username -->
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Username</label>
                        <TextInput type="text" v-model="form.username"
                            class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm dark:border-gray-600"
                            required />
                        <InputError v-if="form.errors.username" :message="form.errors.username" class="mt-1" />
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                        <TextInput type="email" v-model="form.email"
                            class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm dark:border-gray-600"
                            required />
                        <InputError v-if="form.errors.email" :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Role</label>
                        <select v-model="form.role"
                            class="block w-full px-3 py-2 mt-1 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Role --</option>
                            <option v-for="r in props.roleOptions" :key="r" :value="r">{{ r }}</option>
                        </select>
                        <InputError v-if="form.errors.role" :message="form.errors.role" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label>
                        <TextInput type="password" v-model="form.password"
                            class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm dark:border-gray-600"
                            required />
                        <InputError v-if="form.errors.password" :message="form.errors.password" class="mt-1" />
                    </div>

                    <!-- Password Confirmation -->
                    <div class="mb-4">
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-400">Confirm Password</label>
                        <TextInput type="password" v-model="form.password_confirmation"
                            class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm dark:border-gray-600"
                            required />
                    </div>

                    <!-- Branch Selection -->
                    <div class="mb-4">
                        <label for="branch_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Assign Branches
                        </label>
                        <BranchSelect v-model="form.branch_ids" :branches="branches" />
                        <InputError v-if="form.errors.branch_ids" :message="form.errors.branch_ids" class="mt-1" />
                    </div>

                    <!-- Main Branch Selection -->
                    <div class="mb-4">
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Main Branch
                        </label>
                        <select v-model="form.branch_id"
                            class="block w-full px-3 py-2 mt-1 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Main Branch --</option>
                            <option v-for="branch in filteredBranches" :key="branch.id" :value="branch.id">
                                {{ branch.branch_name }}
                            </option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Select the primary branch for this user</p>
                        <InputError v-if="form.errors.branch_id" :message="form.errors.branch_id" class="mt-1" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="button" @click="$router.go(-1)" class="inline-flex items-center mr-2 btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center btn-primary" :disabled="form.processing">
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create User</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import BranchSelect from '@/Components/BranchSelect.vue';

const props = defineProps({
    branches: Array,
    roleOptions: {
        type: Array,
        default: () => [],
    },
});

// Set up the form with all required fields
const form = useForm({
    name: '',
    username: '',
    email: '',
    role: '',
    password: '',
    password_confirmation: '',
    branch_ids: [], // For multiple branch selections
    branch_id: '', // For the main branch
});

// Computed property to filter branches based on selected branch_ids
const filteredBranches = computed(() => {
    if (form.branch_ids.length === 0) {
        return props.branches || [];
    }
    return (props.branches || []).filter(branch => form.branch_ids.includes(branch.id));
});

// Method to handle user creation
function createUser() {
    form.post(route('admin.users.store'), {
        onSuccess: () => {
            form.reset(); // Reset form after success
        },
    });
}
</script>

<style scoped>
.btn-primary {
    @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700 transition duration-200;
}

.btn-secondary {
    @apply bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded shadow hover:bg-gray-400 transition duration-200;
}
</style>
