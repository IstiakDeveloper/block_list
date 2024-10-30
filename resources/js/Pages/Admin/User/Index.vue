<template>
    <Head title="Users" />

    <AdminLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <!-- Header, Search, and Add User Button -->
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-300">Users</h1>

                        <Link href="/admin/users/create"
                              class="btn-primary w-full sm:w-auto flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
                            </svg>
                            Add User
                        </Link>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Search -->
                        <div class="relative flex-grow">
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Search users..."
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500
                                       dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                @input="debouncedSearch"
                            >
                            <div class="absolute right-3 top-2.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Per Page -->
                        <select
                            v-model="filters.per_page"
                            @change="getUsers"
                            class="w-full sm:w-32 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        >
                            <option value="10">10 / page</option>
                            <option value="25">25 / page</option>
                            <option value="50">50 / page</option>
                            <option value="100">100 / page</option>
                        </select>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="p-4 hidden sm:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    #
                                </th>
                                <th @click="sort('name')"
                                    class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Name
                                        <SortIcon :field="'name'" :current-sort="filters.sort" :direction="filters.direction" />
                                    </div>
                                </th>
                                <th @click="sort('email')"
                                    class="p-4 hidden md:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Email
                                        <SortIcon :field="'email'" :current-sort="filters.sort" :direction="filters.direction" />
                                    </div>
                                </th>
                                <th @click="sort('branch_name')"
                                    class="p-4 hidden lg:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer">
                                    <div class="flex items-center">
                                        Branch
                                        <SortIcon :field="'branch_name'" :current-sort="filters.sort" :direction="filters.direction" />
                                    </div>
                                </th>
                                <th class="p-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr v-for="(user, index) in users.data" :key="user.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-4 hidden sm:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</span>
                                        <!-- Mobile-only info -->
                                        <span class="text-xs text-gray-500 md:hidden mt-1">{{ user.email }}</span>
                                        <span class="text-xs text-gray-500 lg:hidden mt-1">
                                            {{ displayBranchNames(user) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 hidden md:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ user.email }}
                                </td>
                                <td
                                    class="p-4 hidden lg:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <template v-if="getBranches(user).length > 0">
                                        {{ displayedBranchNames(user) }}
                                        <button v-if="getBranches(user).length > 2" @click="showAllBranches(user)"
                                            class="ml-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            +{{ getBranches(user).length - 2 }} more
                                        </button>
                                    </template>
                                    <span v-else>No Branch</span>

                                    <!-- Branch Modal -->
                                    <div v-if="showBranchModal"
                                        class="fixed inset-0 z-50 overflow-hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
                                        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md relative">
                                            <!-- Fixed Header -->
                                            <div
                                                class="sticky top-0 bg-white dark:bg-gray-800 p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All
                                                    Branches</h3>
                                                <button @click="closeBranchModal"
                                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Scrollable Content -->
                                            <div class="max-h-[60vh] overflow-y-auto p-4">
                                                <div class="space-y-2">
                                                    <div v-for="branch in selectedUserBranches"
                                                        :key="branch.branch_name"
                                                        class="p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                                        <div class="flex items-center">
                                                            <!-- Branch Icon -->
                                                            <div class="mr-3 text-gray-400 dark:text-gray-500">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                                </svg>
                                                            </div>
                                                            <!-- Branch Name -->
                                                            <span
                                                                class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                                                {{ branch.branch_name }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Fixed Footer -->
                                            <div
                                                class="sticky bottom-0 bg-white dark:bg-gray-800 p-4 border-t border-gray-200 dark:border-gray-700">
                                                <button @click="closeBranchModal" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600
                       text-gray-700 dark:text-gray-200 font-medium py-2 px-4 rounded-lg
                       transition-colors duration-150">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                                        <Link :href="route('admin.users.edit', user.id)"
                                              class="btn-action btn-edit w-full sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            <span>Edit</span>
                                        </Link>

                                        <button @click="confirmDelete(user.id)"
                                                class="btn-action btn-delete w-full sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    :data="users"
                    :item-name="'users'"
                    :extra-params="filters"
                />
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <ConfirmationDialog
            :show="showDeleteDialog"
            title="Delete User"
            message="Are you sure you want to delete this user? This action cannot be undone."
            @close="showDeleteDialog = false"
            @confirm="deleteUser"
        />
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import debounce from 'lodash/debounce';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import Pagination from '@/Components/Pagination.vue';
import SortIcon from '@/Components/SortIcon.vue';

// Props
const props = defineProps({
    users: Object,
    filters: Object
});

// State
const showDeleteDialog = ref(false);
const userToDelete = ref(null);
const filters = ref({
    search: props.filters?.search || '',
    sort: props.filters?.sort || 'created_at',
    direction: props.filters?.direction || 'desc',
    per_page: props.filters?.per_page || '10'
});

// Methods
const displayBranchNames = (user) => {
    if (user.branches?.length > 0) {
        return user.branches.map(branch => branch.branch_name).join(', ');
    }
    return user.branch?.branch_name || 'No Branch';
};

const getUsers = debounce(() => {
    router.get(route('admin.users.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

const sort = (field) => {
    filters.value.direction = filters.value.sort === field && filters.value.direction === 'asc'
        ? 'desc'
        : 'asc';
    filters.value.sort = field;
    getUsers();
};

const confirmDelete = (id) => {
    userToDelete.value = id;
    showDeleteDialog.value = true;
};

const deleteUser = () => {
    router.delete(route('admin.users.destroy', userToDelete.value), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            userToDelete.value = null;
        }
    });
};

const showBranchModal = ref(false);
const selectedUserBranches = ref(null);

// Add these methods
function getBranches(user) {
    if (user.branches?.length > 0) {
        return user.branches;
    } else if (user.branch) {
        return [user.branch];
    }
    return [];
}

function displayedBranchNames(user) {
    const branches = getBranches(user);
    if (branches.length === 0) return 'No Branch';
    return branches
        .slice(0, 2)
        .map(branch => branch.branch_name)
        .join(', ');
}

function showAllBranches(user) {
    selectedUserBranches.value = getBranches(user);
    showBranchModal.value = true;
    document.body.style.overflow = 'hidden';
}

function closeBranchModal() {
    showBranchModal.value = false;
    selectedUserBranches.value = null;
    document.body.style.overflow = '';
}

// Watch for search changes
watch(() => filters.value.search, getUsers);
</script>

<style scoped>
.btn-primary {
    @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow
           hover:bg-blue-700 transition duration-200;
}

.btn-action {
    @apply inline-flex items-center justify-center px-3 py-2 text-sm font-medium
           rounded transition duration-200;
}

.btn-edit {
    @apply text-blue-600 bg-blue-100 hover:bg-blue-200
           dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500;
}

.btn-delete {
    @apply text-red-600 bg-red-100 hover:bg-red-200
           dark:bg-red-600 dark:text-white dark:hover:bg-red-500;
}
</style>
