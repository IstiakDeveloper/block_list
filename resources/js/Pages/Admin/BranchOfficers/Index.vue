<template>
    <AdminLayout>
        <template #header>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <!-- Breadcrumb/Back Navigation -->
                    <div class="mb-6">
                        <Link :href="route('admin.payment-receipt-dashboard')"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-white bg-gray-100 hover:bg-blue-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:text-white rounded-lg transition-all duration-200 group">
                        <ChevronLeftIcon
                            class="w-5 h-5 mr-1.5 transition-transform duration-200 group-hover:-translate-x-0.5" />
                        Back
                        </Link>
                    </div>

                    <!-- Header Content -->
                    <div class="flex items-center justify-between">
                        <!-- Title Section -->
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <BuildingOfficeIcon class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    Branch Officers
                                </h1>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Manage your branch personnel and their responsibilities
                                </p>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button @click="toggleForm"
                            class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            <PlusIcon class="h-5 w-5 mr-2" />
                            Add New Officer
                        </button>
                    </div>
                </div>
                <!-- Alert Messages -->
                <TransitionGroup enter-active-class="transform ease-out duration-300 transition"
                    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0"
                    enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <AlertMessage v-if="errorMessage" :key="'error'" type="error" :message="errorMessage" class="mb-4"
                        @close="errorMessage = ''" />
                    <AlertMessage v-if="successMessage" :key="'success'" type="success" :message="successMessage"
                        class="mb-4" @close="successMessage = ''" />
                </TransitionGroup>

                <!-- Add Officer Form -->
                <Transition enter-active-class="transform ease-out duration-300"
                    enter-from-class="translate-y-2 opacity-0" enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <div v-if="showForm" class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add New Officer</h3>
                            <form @submit.prevent="submitForm" class="relative">
                                <div v-if="form.hasErrors" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                                    <div v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600">
                                        {{ error }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <!-- Branch Selection (if not pre-selected) -->
                                    <div v-if="!userBranch" class="space-y-2">
                                        <label
                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                                        <div class="relative">
                                            <BuildingOfficeIcon
                                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />

                                            <select v-model="form.branch_id"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 text-gray-900 dark:text-gray-100">
                                                <option value="">Select Branch</option>
                                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                                    {{ branch.branch_name }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.branch_id" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.branch_id }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Officer Name -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Officer
                                            Name</label>
                                        <div class="relative">
                                            <UserIcon
                                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                            <input type="text" v-model="form.name"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 text-gray-900 dark:text-gray-100"
                                                placeholder="Enter name">
                                            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.name }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PIN -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">PIN</label>
                                        <div class="relative">
                                            <KeyIcon
                                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                            <input type="text" v-model="form.pin_number"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 text-gray-900 dark:text-gray-100"
                                                placeholder="Enter PIN">
                                            <div v-if="form.errors.pin_number" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.pin_number }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone
                                            Number</label>
                                        <div class="relative">
                                            <PhoneIcon
                                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                            <input type="text" v-model="form.phone_number"
                                                class="pl-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 text-gray-900 dark:text-gray-100"
                                                placeholder="Enter phone number">
                                            <div v-if="form.errors.phone_number" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.phone_number }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end space-x-3">
                                    <button type="button" @click="toggleForm"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button type="submit" :disabled="form.processing"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                                        {{ form.processing ? 'Saving...' : 'Save Officer' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </Transition>

                <!-- Officers Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Officer
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Branch
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Contact
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="officer in officers.data" :key="officer.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                    <UserIcon class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ officer.name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    PIN: {{ officer.pin_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ officer.branch.branch_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ officer.phone_number }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            officer.is_active
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                        ]">
                                            {{ officer.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-3">
                                            <button @click="editOfficer(officer)"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                <PencilIcon class="h-5 w-5" />
                                            </button>
                                            <button @click="confirmDelete(officer)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <Pagination :data="officers" class="mt-6" />
            </div>
        </div>

        <!-- Edit Modal -->
        <Modal :show="editModal" @close="closeEditModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Edit Officer</h3>
                <form @submit.prevent="updateOfficer">
                    <!-- Edit form fields -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" v-model="editForm.name"
                                class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">PIN</label>
                            <input type="text" v-model="editForm.pin_number"
                                class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <input type="text" v-model="editForm.phone_number"
                                class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="editForm.is_active"
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:bg-gray-700">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeEditModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button type="submit" :disabled="editForm.processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="deleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Confirm Delete</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Are you sure you want to delete this officer? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="closeDeleteModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button @click="deleteOfficer" :disabled="deleteForm.processing"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
                        {{ deleteForm.processing ? 'Deleting...' : 'Delete Officer' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import {
    UserIcon,
    BuildingOfficeIcon,
    PhoneIcon,
    KeyIcon,
    PlusIcon,
    PencilIcon,
    ChevronLeftIcon,
    TrashIcon
} from '@heroicons/vue/24/outline';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import AlertMessage from '@/Components/AlertMessage.vue';

// Then, props
const props = defineProps({
    officers: Object,
    branches: Array,
    userBranch: Number
});

// Then, refs
const showForm = ref(false);
const editModal = ref(false);
const deleteModal = ref(false);
const editingOfficer = ref(null);
const deletingOfficer = ref(null);
const errorMessage = ref('');
const successMessage = ref('');

// Then, forms
const form = useForm({
    branch_id: props.userBranch || '',
    name: '',
    pin_number: '',
    phone_number: '',
    details: '',
});

const editForm = useForm({
    name: '',
    pin_number: '',
    phone_number: '',
    details: '',
    is_active: true,
});

const deleteForm = useForm({});

// Then, functions
const toggleForm = () => {
    showForm.value = !showForm.value;
    if (!showForm.value) {
        form.reset();
    }

};


const submitForm = () => {
    form.post(route('admin.branch-officers.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
            successMessage.value = 'Officer added successfully';
            form.reset();
        },
        onError: (errors) => {
            errorMessage.value = errors.error || 'Failed to add officer';
        },
    });
};

const editOfficer = (officer) => {
    editingOfficer.value = officer;
    editForm.name = officer.name;
    editForm.pin_number = officer.pin_number;
    editForm.phone_number = officer.phone_number;
    editForm.details = officer.details || '';
    editForm.is_active = officer.is_active;
    editModal.value = true;
};

const updateOfficer = () => {
    if (!editingOfficer.value) return;

    editForm.put(route('admin.branch-officers.update', editingOfficer.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = 'Officer updated successfully';
            closeEditModal();
        },
        onError: (errors) => {
            errorMessage.value = errors.error || 'Failed to update officer';
        },
    });
};

const confirmDelete = (officer) => {
    deletingOfficer.value = officer;
    deleteModal.value = true;
};

const deleteOfficer = () => {
    if (!deletingOfficer.value) return;

    deleteForm.delete(route('admin.branch-officers.destroy', deletingOfficer.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = 'Officer deleted successfully';
            closeDeleteModal();
        },
        onError: (errors) => {
            errorMessage.value = errors.error || 'Failed to delete officer';
        },
    });
};

// Modal management
const closeEditModal = () => {
    editModal.value = false;
    editingOfficer.value = null;
    editForm.reset();
};

const closeDeleteModal = () => {
    deleteModal.value = false;
    deletingOfficer.value = null;
    deleteForm.reset();
};

// Message auto-hide
watch(successMessage, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            successMessage.value = '';
        }, 5000);
    }
});

watch(errorMessage, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            errorMessage.value = '';
        }, 5000);
    }
});

// Lifecycle hooks
onMounted(() => {
    if (props.userBranch) {
        form.branch_id = props.userBranch;
    }
});

onUnmounted(() => {
    form.reset();
    editForm.reset();
    deleteForm.reset();
});
</script>

<style scoped>
.status-badge {
    @apply px-2 py-1 text-xs rounded-full;
}

.status-badge-active {
    @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300;
}

.status-badge-inactive {
    @apply bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300;
}
</style>
