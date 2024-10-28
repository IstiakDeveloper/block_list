<template>
    <Head title="Edit Customer" />

    <AdminLayout>
        <div class="container mx-auto py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300">Edit Customer</h1>
                <form @submit.prevent="updateCustomer">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- <div class="sm:col-span-3">
                            <label for="nid_part_1" class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Part 1</label>
                            <input
                                type="file"
                                id="nid_part_1"
                                @input="handleFileUpload('nid_part_1', $event)"
                                accept="image/*"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                            <p v-if="form.nid_part_1">Current File: {{ form.nid_part_1 }}</p>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="nid_part_2" class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Part 2</label>
                            <input
                                type="file"
                                id="nid_part_2"
                                @input="handleFileUpload('nid_part_2', $event)"
                                accept="image/*"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                            <p v-if="form.nid_part_2">Current File: {{ form.nid_part_2 }}</p>
                        </div> -->

                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <div class="sm:col-span-3">
                            <label for="name_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Name (Bangla)</label>
                            <input
                                type="text"
                                id="name_bn"
                                v-model="form.name_bn"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <div class="sm:col-span-3">
                            <label for="father_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Father's Name</label>
                            <input
                                type="text"
                                id="father_name"
                                v-model="form.father_name"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <div class="sm:col-span-3">
                            <label for="mother_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Mother's Name</label>
                            <input
                                type="text"
                                id="mother_name"
                                v-model="form.mother_name"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <div class="sm:col-span-3">
                            <label for="rejected_by" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Rejected By</label>
                            <input
                                type="text"
                                id="rejected_by"
                                v-model="form.rejected_by"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <div class="sm:col-span-3">
                            <label for="dob" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Date of Birth</label>
                            <input
                                type="date"
                                id="dob"
                                v-model="form.dob"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <div class="sm:col-span-3">
                            <label for="nid_number" class="block text-sm font-medium text-gray-700 dark:text-gray-400">NID Number</label>
                            <input
                                type="text"
                                id="nid_number"
                                v-model="form.nid_number"
                                required
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <div class="sm:col-span-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                rows="3"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            ></textarea>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="details" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Details</label>
                            <textarea
                                id="details"
                                v-model="form.details"
                                rows="3"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 dark:bg-gray-700 dark:text-white"
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md"
                        >
                            Update Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3'


export default {
    components: { Head, AdminLayout },
    props: {
        customer: Object
    },
    setup(props) {
        const form = useForm({
            ...props.customer,
            nid_part_1: '',
            nid_part_2: ''
        })

        function updateCustomer() {
            form.put(route('admin.customers.update', props.customer.id))
        }

        function handleFileUpload(field, event) {
            form[field] = event.target.files[0]
        }

        return {
            form,
            updateCustomer,
            handleFileUpload
        }
    }
}
</script>
