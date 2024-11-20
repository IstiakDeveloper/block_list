<template>

    <Head title="Create Customer" />

    <AdminLayout>
        <div class="container mx-auto py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300">Create Customer</h1>
                <form @submit.prevent="createCustomer">
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
                                    <!-- Adjust this to the appropriate field in your branch model -->
                                </option>
                            </select>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Select the branch for this customer.</p>
                        </div>


                        <!-- NID Part 1 -->
                        <div class="sm:col-span-3">
                            <label for="nid_part_1"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Part 1</label>
                            <input type="file" id="nid_part_1" @input="handleFileUpload('nid_part_1', $event)"
                                accept="image/*"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Upload the first part of your NID.</p>
                            <img v-if="form.nid_part_1_url" :src="form.nid_part_1_url" alt="NID Part 1 Preview"
                                class="mt-2 w-32 h-32 object-cover rounded">
                        </div>

                        <!-- NID Part 2 -->
                        <div class="sm:col-span-3">
                            <label for="nid_part_2"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Part 2</label>
                            <input type="file" id="nid_part_2" @input="handleFileUpload('nid_part_2', $event)"
                                accept="image/*"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Upload the second part of your NID.</p>
                            <img v-if="form.nid_part_2_url" :src="form.nid_part_2_url" alt="NID Part 2 Preview"
                                class="mt-2 w-32 h-32 object-cover rounded">
                        </div>

                        <!-- Customer Name -->
                        <div class="sm:col-span-3">
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                            <input type="text" id="name" v-model="form.name" required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Enter the customer's full name.</p>
                        </div>

                        <!-- Other Fields -->
                        <div class="sm:col-span-3">
                            <label for="name_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name
                                (Bangla)</label>
                            <input type="text" id="name_bn" v-model="form.name_bn"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="father_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Father's Name</label>
                            <input type="text" id="father_name" v-model="form.father_name"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="spouse_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Spouse Name</label>
                            <input type="text" id="spouse_name" v-model="form.spouse_name"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="mother_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Mother's Name</label>
                            <input type="text" id="mother_name" v-model="form.mother_name"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="rejected_by"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Rejected By</label>
                            <input type="text" id="rejected_by" v-model="form.rejected_by"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="dob" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Date of
                                Birth</label>
                            <input type="date" id="dob" v-model="form.dob"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="nid_number"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Number</label>
                            <input type="text" id="nid_number" v-model="form.nid_number"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="phone_number"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Phone Number</label>
                            <input type="text" id="phone_number" v-model="form.phone_number"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="sm:col-span-6">
                            <label for="address"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
                            <textarea id="address" v-model="form.address" rows="3"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="details"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Details</label>
                            <textarea id="details" v-model="form.details" rows="3"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="btn-primary inline-flex items-center" :disabled="loading">
                            <span v-if="loading">Creating...</span>
                            <span v-else>Create Customer</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10 2a1 1 0 00-1 1v6H3a1 1 0 100 2h6v6a1 1 0 002 0v-6h6a1 1 0 100-2h-6V3a1 1 0 00-1-1z" />
                            </svg>
                        </button>
                        <Link :href="route('admin.customers.index')"
                            class="btn-secondary inline-flex items-center ml-4">
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
    branches: Array, // Receive branches from the server
});

const form = useForm({
    branch_id: '', // Add branch_id to the form
    nid_part_1: null,
    nid_part_2: null,
    nid_part_1_url: '', // URL for the image preview
    nid_part_2_url: '', // URL for the image preview
    name: '',
    name_bn: '',
    father_name: '',
    spouse_name: '',
    mother_name: '',
    rejected_by: '',
    dob: '',
    nid_number: '',
    phone_number: '',
    address: '',
    details: '',
});

const successMessage = ref('');
const errorMessage = ref('');
const loading = ref(false); // New loading state

function handleFileUpload(field, event) {
    const file = event.target.files[0];
    form[field] = file;

    // Create a URL for the image preview
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            if (field === 'nid_part_1') {
                form.nid_part_1_url = e.target.result;
            } else if (field === 'nid_part_2') {
                form.nid_part_2_url = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
}

function createCustomer() {
    loading.value = true; // Start loading
    form.post(route('admin.customers.store'), {
        forceFormData: true,
        onSuccess: () => {
            successMessage.value = 'Customer created successfully!';
            errorMessage.value = ''; // Clear any previous error messages
            form.reset(); // Reset form fields
            loading.value = false; // Stop loading
        },
        onError: () => {
            errorMessage.value = 'Error creating customer. Please try again.';
            successMessage.value = ''; // Clear any previous success messages
            loading.value = false; // Stop loading
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
    /* Optional styling for the image */
}
</style>
