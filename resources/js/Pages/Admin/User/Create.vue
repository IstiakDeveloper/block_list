<template>

    <Head title="Create User" />
    <AdminLayout>
        <div class="container mx-auto py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300">Create User</h1>

                <form @submit.prevent="createUser">
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                        <TextInput type="text" v-model="form.name"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                            required />
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                        <TextInput type="email" v-model="form.email"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                            required />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label>
                        <TextInput type="password" v-model="form.password"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                            required />
                    </div>

                    <!-- Password Confirmation -->
                    <div class="mb-4">
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-400">Confirm Password</label>
                        <TextInput type="password" v-model="form.password_confirmation"
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                            required />
                    </div>

                    <!-- Branch Selection -->
                    <div class="mb-4">
                        <label for="branch_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Assign Branches
                        </label>
                        <BranchSelect v-model="form.branch_ids" :branches="branches" />
                    </div>


                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary inline-flex items-center">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import BranchSelect from '@/Components/BranchSelect.vue'; // Add this import


// Define props for branches
const props = defineProps({
    branches: Array,
});

// Set up the form with branch_ids as an array to handle multiple selections
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    branch_ids: [], // Changed to an array to handle multiple branch selections
});

// Method to handle user creation
function createUser() {
    form.post(route('admin.users.store'), {
        onSuccess: () => {
            form.reset(); // Reset form after success
        },
        onError: () => {
            // Handle any validation errors
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
