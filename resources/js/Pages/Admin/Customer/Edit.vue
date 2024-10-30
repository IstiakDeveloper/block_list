<template>
    <Head title="Edit Customer" />

    <AdminLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 sm:p-8">
                <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300">Edit Customer</h1>
                <form @submit.prevent="updateCustomer">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Branch Selection -->
                        <div v-if="branches.length > 0" class="sm:col-span-3">
                            <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Select Branch
                            </label>
                            <select id="branch_id" v-model="form.branch_id"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                                <option value="" disabled>Select a branch</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.branch_name }}
                                </option>
                            </select>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Select the branch for this customer.</p>
                        </div>

                        <!-- NID Part 1 -->
                        <div class="sm:col-span-3">
                            <label for="nid_part_1" class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Part 1</label>
                            <input type="file" id="nid_part_1" @input="handleFileUpload('nid_part_1', $event)" accept="image/*"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Upload the first part of your NID.</p>
                            <img v-if="form.nid_part_1_url" :src="form.nid_part_1_url" alt="NID Part 1 Preview" class="mt-2 w-32 h-32 object-cover rounded">
                            <p v-else-if="customer.nid_part_1" class="text-sm text-gray-500 dark:text-gray-400">Current file: {{ customer.nid_part_1 }}</p>
                        </div>

                        <!-- NID Part 2 -->
                        <div class="sm:col-span-3">
                            <label for="nid_part_2" class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Part 2</label>
                            <input type="file" id="nid_part_2" @input="handleFileUpload('nid_part_2', $event)" accept="image/*"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Upload the second part of your NID.</p>
                            <img v-if="form.nid_part_2_url" :src="form.nid_part_2_url" alt="NID Part 2 Preview" class="mt-2 w-32 h-32 object-cover rounded">
                            <p v-else-if="customer.nid_part_2" class="text-sm text-gray-500 dark:text-gray-400">Current file: {{ customer.nid_part_2 }}</p>
                        </div>

                        <!-- Customer Name -->
                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                            <input type="text" id="name" v-model="form.name" required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Enter the customer's full name.</p>
                        </div>

                        <!-- Name (Bangla) -->
                        <div class="sm:col-span-3">
                            <label for="name_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name (Bangla)</label>
                            <input type="text" id="name_bn" v-model="form.name_bn"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Father's Name -->
                        <div class="sm:col-span-3">
                            <label for="father_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Father's Name</label>
                            <input type="text" id="father_name" v-model="form.father_name"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Mother's Name -->
                        <div class="sm:col-span-3">
                            <label for="mother_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Mother's Name</label>
                            <input type="text" id="mother_name" v-model="form.mother_name"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Rejected By -->
                        <div class="sm:col-span-3">
                            <label for="rejected_by" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Rejected By</label>
                            <input type="text" id="rejected_by" v-model="form.rejected_by"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Date of Birth -->
                        <div class="sm:col-span-3">
                            <label for="dob" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Date of Birth</label>
                            <input type="date" id="dob" v-model="form.dob"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- NID Number -->
                        <div class="sm:col-span-3">
                            <label for="nid_number" class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Number</label>
                            <input type="text" id="nid_number" v-model="form.nid_number" required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Phone Number -->
                        <div class="sm:col-span-3">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Number</label>
                            <input type="text" id="phone_number" v-model="form.phone_number" required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
                            <textarea id="address" v-model="form.address" rows="3"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>

                        <!-- Details -->
                        <div class="sm:col-span-6">
                            <label for="details" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Details</label>
                            <textarea id="details" v-model="form.details" rows="3"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="btn-primary inline-flex items-center" :disabled="loading">
                            <span v-if="loading">Updating...</span>
                            <span v-else>Update Customer</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <Link :href="route('admin.customers.index')" class="btn-secondary inline-flex items-center ml-4">
                            Back to Customers
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

const props = defineProps({
    customer: Object,
    branches: Array,
});

const form = useForm({
    branch_id: props.customer.branch_id,
    name: props.customer.name,
    name_bn: props.customer.name_bn,
    father_name: props.customer.father_name,
    mother_name: props.customer.mother_name,
    rejected_by: props.customer.rejected_by,
    dob: props.customer.dob,
    nid_number: props.customer.nid_number,
    phone_number: props.customer.phone_number,
    address: props.customer.address,
    details: props.customer.details,
    nid_part_1: null,
    nid_part_2: null,
});

const successMessage = ref('');
const errorMessage = ref('');
const loading = ref(false);

function handleFileUpload(field, event) {
    const file = event.target.files[0];
    form[field] = file;
}

function updateCustomer() {
    loading.value = true;
    form.put(route('admin.customers.update', props.customer.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = 'Customer updated successfully!';
            errorMessage.value = '';
            loading.value = false;
        },
        onError: (errors) => {
            errorMessage.value = 'Error updating customer. Please try again.';
            console.error(errors);
            successMessage.value = '';
            loading.value = false;
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

img {
    border: 1px solid #ccc;
}
</style>
