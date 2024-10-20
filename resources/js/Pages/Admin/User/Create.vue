<template>
    <Head title="Create User" />
    <AdminLayout>
      <div class="container mx-auto py-8">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
          <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300">Create User</h1>

          <form @submit.prevent="createUser">
            <!-- Name -->
            <div class="mb-4">
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
              <TextInput
                type="text"
                v-model="form.name"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                required
              />
            </div>

            <!-- Email -->
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
              <TextInput
                type="email"
                v-model="form.email"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                required
              />
            </div>

            <!-- Password -->
            <div class="mb-4">
              <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label>
              <TextInput
                type="password"
                v-model="form.password"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                required
              />
            </div>

            <!-- Password Confirmation -->
            <div class="mb-4">
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Confirm Password</label>
              <TextInput
                type="password"
                v-model="form.password_confirmation"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm"
                required
              />
            </div>

            <!-- Branch -->
            <div class="mb-4">
                <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Assign Branch
                </label>
                <select
                    v-model="form.branch_id"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option :value="null">Select a Branch</option>
                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                    {{ branch.branch_name }}
                    </option>
                </select>
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


  // Define props for branches
  const props = defineProps({
    branches: Array,
  });

  // Set up the form
  const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    branch_id: null,
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
