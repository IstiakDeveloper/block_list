<template>
  <Head title="Edit User" />

  <AdminLayout>
    <div class="container mx-auto py-8">
      <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300">Edit User</h1>
        <form @submit.prevent="updateUser">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
            <input
              type="text"
              v-model="form.name"
              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
              required
            />
          </div>

          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
            <input
              type="email"
              v-model="form.email"
              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
              required
            />
          </div>

          <div class="mb-4">
            <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Branch</label>
            <select
              v-model="form.branch_id"
              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
            >
              <option value="" disabled>Select Branch</option>
              <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.branch_name }}</option>
            </select>
          </div>

          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Password</label>
            <input
              type="password"
              v-model="form.password"
              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
            />
          </div>

          <div class="flex justify-end">
            <button type="submit" class="btn-primary inline-flex items-center">
              Update User
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2a1 1 0 00-1 1v6H3a1 1 0 100 2h6v6a1 1 0 002 0v-6h6a1 1 0 100-2h-6V3a1 1 0 00-1-1z" />
              </svg>
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

const props = defineProps({
  user: Object,
  branches: Array,
});

const form = useForm({
  name: '',
  email: '',
  password: '',
  branch_id: null,
});

const successMessage = ref('');
const errorMessage = ref('');

onMounted(() => {
  form.name = props.user.name;
  form.email = props.user.email;
  form.branch_id = props.user.branch_id;
});

function updateUser() {
  form.put(route('admin.users.update', props.user.id), {
    onSuccess: () => {
      successMessage.value = 'User updated successfully!';
      errorMessage.value = ''; // Clear any previous error messages
    },
    onError: () => {
      errorMessage.value = 'Error updating user. Please try again.';
      successMessage.value = ''; // Clear any previous success messages
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
