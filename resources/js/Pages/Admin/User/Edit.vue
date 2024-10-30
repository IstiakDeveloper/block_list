<template>
    <Head title="Edit User" />

    <AdminLayout>
      <div class="container mx-auto py-8">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
          <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300">Edit User</h1>

          <form @submit.prevent="updateUser">
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
              />
              <small class="text-gray-500 dark:text-gray-400">Leave blank to keep the current password</small>
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
                Update User
              </button>
              <Link href="/admin/users" class="btn-secondary inline-flex items-center ml-4">
                Back to Users
              </Link>
            </div>
          </form>

          <div v-if="successMessage" class="mt-4 text-green-600">{{ successMessage }}</div>
          <div v-if="errorMessage" class="mt-4 text-red-600">{{ errorMessage }}</div>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue';
  import { useForm, Link, Head } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  import TextInput from '@/Components/TextInput.vue';
  import BranchSelect from '@/Components/BranchSelect.vue';

  const props = defineProps({
    user: Object,
    branches: Array,
  });

  const form = useForm({
    name: '',
    email: '',
    password: '',
    branch_ids: [], // Array to handle multiple branch selection
  });

  const successMessage = ref('');
  const errorMessage = ref('');

  onMounted(() => {
    form.name = props.user.name;
    form.email = props.user.email;
    form.branch_ids = props.user.branches.map(branch => branch.id); // Pre-fill with user's branches
  });

  function updateUser() {
    form.put(route('admin.users.update', props.user.id), {
      onSuccess: () => {
        successMessage.value = 'User updated successfully!';
        errorMessage.value = '';
      },
      onError: () => {
        errorMessage.value = 'Error updating user. Please try again.';
        successMessage.value = '';
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
