<template>
    <Head title="Create Branch" />

    <AdminLayout>
      <div class="container mx-auto py-8">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
          <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300">Create Branch</h1>
          <form @submit.prevent="createBranch">
            <div class="mb-4">
              <label for="branch_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Branch Name</label>
              <input
                type="text"
                v-model="form.branch_name"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                required
              />
            </div>

            <div class="mb-4">
              <label for="branch_code" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Branch Code</label>
              <input
                type="text"
                v-model="form.branch_code"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                required
              />
            </div>

            <div class="mb-4">
              <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
              <textarea
                v-model="form.address"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
              ></textarea>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="btn-primary inline-flex items-center">
                Create Branch
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10 2a1 1 0 00-1 1v6H3a1 1 0 100 2h6v6a1 1 0 002 0v-6h6a1 1 0 100-2h-6V3a1 1 0 00-1-1z" />
                </svg>
              </button>
              <Link href="/admin/branches" class="btn-secondary inline-flex items-center ml-4">
                Back to Branches
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
import { ref } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const form = useForm({
  branch_name: '',
  branch_code: '',
  address: '',
});

const successMessage = ref('');
const errorMessage = ref('');

function createBranch() {
  form.post(route('admin.branches.store'), {
    onSuccess: () => {
      successMessage.value = 'Branch created successfully!';
      errorMessage.value = ''; // Clear any previous error messages
      form.reset(); // Reset form fields
    },
    onError: () => {
      errorMessage.value = 'Error creating branch. Please try again.';
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
