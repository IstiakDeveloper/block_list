<template>
    <div v-if="visible"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div
                class="bg-gray-100 dark:bg-gray-700 p-6 border-b dark:border-gray-600 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    Full Customer Profile
                </h2>
                <button @click="$emit('close')"
                    class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 space-y-4">
                <!-- NID Images -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">NID Part 1</p>
                        <img v-if="customer.nid_part_1" :src="getImageUrl(customer.nid_part_1)" alt="NID Part 1"
                            class="w-full rounded-lg shadow-md" />
                    </div>
                    <div>
                        <p class="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">NID Part 2</p>
                        <img v-if="customer.nid_part_2" :src="getImageUrl(customer.nid_part_2)" alt="NID Part 2"
                            class="w-full rounded-lg shadow-md" />
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <ProfileDetailItem label="Full Name" :value="customer.name" />
                    <ProfileDetailItem label="Name (Bangla)" :value="customer.name_bn" />
                    <ProfileDetailItem label="Father's Name" :value="customer.father_name" />
                    <ProfileDetailItem label="Mother's Name" :value="customer.mother_name" />
                    <ProfileDetailItem label="Spouse Name" :value="customer.spouse_name" />
                    <ProfileDetailItem label="Date of Birth" :value="formatDate(customer.dob)" />
                </div>

                <!-- Identification Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <ProfileDetailItem label="NID Number" :value="customer.nid_number" />
                    <ProfileDetailItem label="Phone Number" :value="customer.phone_number" />
                    <ProfileDetailItem label="Rejected By" :value="customer.rejected_by" />
                </div>

                <!-- Organizational Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <ProfileDetailItem label="Branch" :value="customer.branch?.branch_name" />
                    <ProfileDetailItem label="Created By" :value="customer.user?.name" />
                </div>

                <!-- Additional Information -->
                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                        Additional Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Address</p>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ customer.address || 'No address provided' }}
                            </p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Details</p>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ customer.details || 'No additional details' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 border-t dark:border-gray-600 flex justify-end space-x-3">
                <button @click="printProfile"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Print Profile
                </button>
                <button @click="$emit('close')"
                    class="bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { formatDate, getImageUrl, printCustomerProfile } from '@/Utils/customerHelpers';
import ProfileDetailItem from '@/Components/Modals/ProfileDetailItem.vue';

const props = defineProps({
    customer: {
        type: Object,
        required: true
    },
    visible: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

function printProfile() {
    printCustomerProfile(props.customer);
}
</script>
