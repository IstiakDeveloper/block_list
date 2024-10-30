<template>
    <Head title="Branches" />

    <AdminLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <!-- Header, Search, and Add Branch Button -->
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-300">Branches</h1>

                        <Link href="/admin/branches/create" class="btn-primary w-full sm:w-auto flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a1 1 0 011 1v6h6a1 1 0 110 2h-6v6a1 1 0 11-2 0v-6H3a1 1 0 110-2h6V3a1 1 0 011-1z" />
                            </svg>
                            Add Branch
                        </Link>
                    </div>

                    <!-- Search and Filters -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Search -->
                        <div class="relative flex-grow">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search branches..."
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

                        <!-- Items per page -->
                        <select
                            v-model="perPage"
                            @change="updatePerPage"
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
                                    Serial Number
                                </th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                    @click="sort('branch_name')">
                                    <div class="flex items-center">
                                        Branch Name
                                        <SortIcon :field="'branch_name'" :currentSort="sortField" :direction="sortDirection" />
                                    </div>
                                </th>
                                <th class="p-4 hidden md:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                    @click="sort('branch_code')">
                                    <div class="flex items-center">
                                        Branch Code
                                        <SortIcon :field="'branch_code'" :currentSort="sortField" :direction="sortDirection" />
                                    </div>
                                </th>
                                <th class="p-4 hidden lg:table-cell text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                    @click="sort('address')">
                                    <div class="flex items-center">
                                        Location
                                        <SortIcon :field="'address'" :currentSort="sortField" :direction="sortDirection" />
                                    </div>
                                </th>
                                <th class="p-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <tr v-for="(branch, index) in branches.data" :key="branch.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-4 hidden sm:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ (branches.current_page - 1) * branches.per_page + index + 1 }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ branch.branch_name }}
                                        </span>
                                        <!-- Mobile-only info -->
                                        <span class="text-xs text-gray-500 md:hidden mt-1">
                                            Code: {{ branch.branch_code }}
                                        </span>
                                        <span class="text-xs text-gray-500 lg:hidden mt-1">
                                            Location: {{ branch.address }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 hidden md:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ branch.branch_code }}
                                </td>
                                <td class="p-4 hidden lg:table-cell whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ branch.address }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                                        <Link :href="route('admin.branches.edit', branch.id)"
                                              class="btn-action btn-edit w-full sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M12.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-9 9a1 1 0 01-.353.213l-3 1a1 1 0 01-1.293-1.293l1-3a1 1 0 01.213-.353l9-9z" />
                                            </svg>
                                            <span>Edit</span>
                                        </Link>

                                        <button @click="confirmDelete(branch.id)"
                                                class="btn-action btn-delete w-full sm:w-auto">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 2a1 1 0 00-1 1v1h12V3a1 1 0 00-1-1H6z" />
                                                <path fill-rule="evenodd"
                                                      d="M5 4h10a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1zm1 1v13h8V5H6z"
                                                      clip-rule="evenodd" />
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
                    :data="branches"
                    :item-name="'branches'"
                    :extra-params="{
                        search: search,
                        sort: sortField,
                        direction: sortDirection,
                        per_page: perPage
                    }"
                />
            </div>
        </div>

        <ConfirmationDialog
            :show="isDialogVisible"
            @update:show="isDialogVisible = $event"
            @confirm="deleteBranch"
        />
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';
import Pagination from '@/Components/Pagination.vue';
import SortIcon from '@/Components/SortIcon.vue';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    branches: Object,
    filters: Object,
});

// State
const isDialogVisible = ref(false);
const branchToDelete = ref(null);
const form = useForm({});
const search = ref(props.filters?.search || '');
const sortField = ref(props.filters?.sort || 'created_at');
const sortDirection = ref(props.filters?.direction || 'desc');
const perPage = ref(props.filters?.per_page || '10');

// Debounced search
const debouncedSearch = debounce(() => {
    router.get(
        route('admin.branches.index'),
        {
            search: search.value,
            sort: sortField.value,
            direction: sortDirection.value,
            per_page: perPage.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
}, 300);

// Methods
function sort(field) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    router.get(
        route('admin.branches.index'),
        {
            search: search.value,
            sort: sortField.value,
            direction: sortDirection.value,
            per_page: perPage.value
        },
        {
            preserveState: true,
            preserveScroll: true
        }
    );
}

function updatePerPage() {
    router.get(
        route('admin.branches.index'),
        {
            search: search.value,
            sort: sortField.value,
            direction: sortDirection.value,
            per_page: perPage.value
        },
        {
            preserveState: true,
            preserveScroll: true
        }
    );
}

function confirmDelete(branchId) {
    branchToDelete.value = branchId;
    isDialogVisible.value = true;
}

function deleteBranch() {
    if (branchToDelete.value) {
        form.delete(route('admin.branches.destroy', branchToDelete.value), {
            onSuccess: () => {
                isDialogVisible.value = false;
            },
        });
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

.btn-action {
    @apply inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded transition duration-200;
}

.btn-edit {
    @apply text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500;
}

.btn-delete {
    @apply text-red-600 bg-red-100 hover:bg-red-200 dark:bg-red-600 dark:text-white dark:hover:bg-red-500;
}
</style>
