<template>
    <Head title="Users" />

    <AdminLayout>
      <div class="container mx-auto py-8">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-300">Users</h1>
            <Link href="/admin/users/create" class="btn-primary flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
              </svg>
              Add User
            </Link>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch Name</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700">
                    <tr v-for="(user, index) in users.data" :key="user.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ user.branch && user.branch.branch_name ? user.branch.branch_name : 'No Branch' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <Link :href="route('admin.users.edit', user.id)" class="inline-flex items-center text-blue-600 hover:text-blue-800 bg-blue-100 hover:bg-blue-200 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500 dark:hover:text-gray-100 px-3 py-2 rounded transition duration-200 ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M12.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-9 9a1 1 0 01-.353.213l-3 1a1 1 0 01-1.293-1.293l1-3a1 1 0 01.213-.353l9-9z" />
                        </svg>
                        Edit
                        </Link>

                        <button @click="confirmDelete(user.id)" class="inline-flex items-center text-red-600 hover:text-red-800 bg-red-100 hover:bg-red-200 dark:bg-red-600 dark:text-white dark:hover:bg-red-500 dark:hover:text-gray-100 px-3 py-2 rounded transition duration-200 ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 2a1 1 0 00-1 1v1h12V3a1 1 0 00-1-1H6z" />
                            <path fill-rule="evenodd" d="M5 4h10a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1zm1 1v13h8V5H6z" clip-rule="evenodd" />
                        </svg>
                        Delete
                        </button>
                    </td>
                    </tr>
                </tbody>
            </table>

          </div>

          <!-- Pagination -->
          <div class="mt-4 flex justify-between items-center">
          <div class="flex justify-center items-center">
            <span class="text-sm text-gray-700 dark:text-gray-300">
              Showing {{ users.from }} to {{ users.to }} of {{ users.total }} users
            </span>
          </div>
          <div class="flex justify-center items-center">
            <button @click="previousPage" :disabled="!users.prev_page_url"
                    class="btn-secondary px-4 py-2"
                    :class="{'opacity-50 cursor-not-allowed': !users.prev_page_url}">
              Previous
            </button>
            <button @click="nextPage" :disabled="!users.next_page_url"
                    class="btn-secondary px-4 py-2 ml-2"
                    :class="{'opacity-50 cursor-not-allowed': !users.next_page_url}">
              Next
            </button>
          </div>
        </div>
        </div>
      </div>

      <ConfirmationDialog
        :show="isDialogVisible"
        @update:show="isDialogVisible = $event"
        @confirm="deleteUser"
      />
    </AdminLayout>
  </template>

<script setup>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import { ref } from 'vue';

const props = defineProps({
  users: Object,
});

const isDialogVisible = ref(false);
const userToDelete = ref(null);
const form = useForm({});

function confirmDelete(userId) {
  userToDelete.value = userId;
  isDialogVisible.value = true;
}

function deleteUser() {
  if (userToDelete.value) {
    form.delete(route('admin.users.destroy', userToDelete.value), {
      onSuccess: () => {
        isDialogVisible.value = false; // Close dialog after successful deletion
      },
    });
  }
}

function previousPage() {
  if (props.users.prev_page_url) {
    router.get(props.users.prev_page_url);
  }
}

function nextPage() {
  if (props.users.next_page_url) {
    router.get(props.users.next_page_url);
  }
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
