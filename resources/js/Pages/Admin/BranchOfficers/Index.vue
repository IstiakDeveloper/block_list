<template>
    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Branch Officers
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <AlertMessage v-if="errorMessage" type="error" :message="errorMessage" />

                <AlertMessage v-if="successMessage" type="success" :message="successMessage" />

                <!-- Add Officer Form -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <form @submit.prevent="submitForm">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div v-if="!userBranch">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                                <select v-model="form.branch_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600
                                               dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">Select Branch</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.branch_name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.branch_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.branch_id }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-gray-300">
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">PIN</label>
                                <input type="text" v-model="form.pin_number" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-gray-300">
                                <div v-if="form.errors.pin_number" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.pin_number }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                <input type="text" v-model="form.phone_number" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700 dark:text-gray-300">
                                <div v-if="form.errors.phone_number" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.phone_number }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700
                                           dark:bg-blue-500 dark:hover:bg-blue-600 disabled:opacity-50">
                                {{ form.processing ? 'Adding...' : 'Add Officer' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Officers List -->
                <div class="mt-6">
                    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Branch
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        PIN
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Phone
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="officer in officers.data" :key="officer.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-300">
                                        {{ officer.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-300">
                                        {{ officer.branch.branch_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-300">
                                        {{ officer.pin_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-300">
                                        {{ officer.phone_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            officer.is_active
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                        ]">
                                            {{ officer.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button @click="editOfficer(officer)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :data="officers" class="mt-6" />
                </div>
            </div>
        </div>
        <!-- Edit Modal -->
        <Modal :show="editModal" @close="closeEditModal">
            <div class="p-6 bg-white dark:bg-gray-800">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Officer</h3>
                <form @submit.prevent="updateOfficer" class="mt-4">
                    <div class="space-y-4">
                        <!-- Add edit form error message -->
                        <div v-if="editForm.errors.error" class="text-sm text-red-600 dark:text-red-400">
                            {{ editForm.errors.error }}
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" v-model="editForm.name"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <div v-if="editForm.errors.name" class="mt-1 text-sm text-red-600">
                                {{ editForm.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">PIN</label>
                            <input type="text" v-model="editForm.pin_number"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <div v-if="editForm.errors.pin_number" class="mt-1 text-sm text-red-600">
                                {{ editForm.errors.pin_number }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <input type="text" v-model="editForm.phone_number"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <div v-if="editForm.errors.phone_number" class="mt-1 text-sm text-red-600">
                                {{ editForm.errors.phone_number }}
                            </div>
                        </div>

                        <!-- Add details field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Details</label>
                            <textarea v-model="editForm.details" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    </textarea>
                            <div v-if="editForm.errors.details" class="mt-1 text-sm text-red-600">
                                {{ editForm.errors.details }}
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="editForm.is_active"
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:bg-gray-700">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" @click="closeEditModal"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" :disabled="editForm.processing"
                            class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 disabled:opacity-50">
                            {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import AlertMessage from '@/Components/AlertMessage.vue'

const props = defineProps({
    officers: Object,
    branches: Array,
    userBranch: Number
})

const form = useForm({
    branch_id: props.userBranch || '',
    name: '',
    pin_number: '',
    phone_number: '',
    details: '',
})

const editForm = useForm({
    name: '',
    pin_number: '',
    phone_number: '',
    details: '',
    is_active: true,
})

const editOfficer = (officer) => {
    editingOfficer.value = officer
    editForm.name = officer.name
    editForm.pin_number = officer.pin_number
    editForm.phone_number = officer.phone_number
    editForm.details = officer.details || ''
    editForm.is_active = officer.is_active
    editModal.value = true
}

const updateOfficer = () => {
    if (!editingOfficer.value) return

    editForm.put(route('admin.branch-officers.update', editingOfficer.value.id), {
        onSuccess: () => {
            successMessage.value = 'Officer updated successfully'
            closeEditModal()
        },
        onError: (errors) => {
            console.error('Update errors:', errors)
            errorMessage.value = errors.error || 'Failed to update officer'
        }
    })
}

const editModal = ref(false)
const editingOfficer = ref(null)
const toastMessage = ref('')
const toastType = ref('success')
const errorMessage = ref('')
const successMessage = ref('')

// Updated submitForm with better error handling
const submitForm = () => {
    errorMessage.value = '' // Clear previous errors
    successMessage.value = '' // Clear previous success message

    form.post(route('admin.branch-officers.store'), {
        onSuccess: () => {
            successMessage.value = 'Officer added successfully'
            form.reset()
        },
        onError: (errors) => {
            console.error('Form errors:', errors)
            if (errors.error) {
                errorMessage.value = errors.error
            } else {
                errorMessage.value = 'Failed to add officer. Please check the form for errors.'
            }
        }
    })
}

// Auto-hide messages after 5 seconds
watch(successMessage, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            successMessage.value = ''
        }, 5000)
    }
})

watch(errorMessage, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            errorMessage.value = ''
        }, 5000)
    }
})

// Clear messages on unmount
onUnmounted(() => {
    errorMessage.value = ''
    successMessage.value = ''
})

// Add form initialization for branch_id
onMounted(() => {
    if (props.userBranch) {
        form.branch_id = props.userBranch
        console.log('Setting branch_id:', props.userBranch) // Debug log
    }
})





const closeEditModal = () => {
    editModal.value = false
    editingOfficer.value = null
    editForm.reset()
}

const showSuccessMessage = (message) => {
    toastMessage.value = message
    toastType.value = 'success'
}

const showErrorMessage = (message) => {
    toastMessage.value = message
    toastType.value = 'error'
}

onMounted(() => {
    if (props.userBranch) {
        form.branch_id = props.userBranch
    }
})

onUnmounted(() => {
    form.reset()
    editForm.reset()
    editingOfficer.value = null
    editModal.value = false
})
</script>

<style scoped>
/* Add any component-specific styles here */
.status-badge {
    @apply px-2 py-1 text-xs rounded-full;
}

.status-badge-active {
    @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300;
}

.status-badge-inactive {
    @apply bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300;
}

/* Add any custom dark mode transitions */
.dark-mode-transition {
    @apply transition-colors duration-200;
}
</style>
