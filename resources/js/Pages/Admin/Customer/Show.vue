<template>

    <Head :title="customer ? customer.name : 'Customer Details'" />

    <AdminLayout>
        <div class="container px-4 py-8 mx-auto">
            <div class="overflow-hidden bg-white shadow-2xl dark:bg-gray-900 rounded-xl">
                <!-- Header Section -->
                <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white">{{ customer.name }}</h1>
                            <p class="mt-2 text-blue-100">Customer Detailed Profile</p>
                        </div>
                        <div class="flex space-x-3">
                            <button @click="openProfileModal" class="btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                View Full Profile
                            </button>
                            <a :href="route('admin.customers.download-pdf', customer.id)" class="btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                        clip-rule="evenodd" />
                                </svg>
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- NID Images Section -->
                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
                    <div class="overflow-hidden bg-gray-100 rounded-lg shadow-md dark:bg-gray-800">
                        <div class="p-4 bg-gray-200 dark:bg-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">NID Part 1</h3>
                        </div>
                        <img v-if="customer.nid_part_1" :src="getImageUrl(customer.nid_part_1)" alt="NID Part 1"
                            class="object-cover w-full h-64" @error="handleImageError" />
                    </div>
                    <div class="overflow-hidden bg-gray-100 rounded-lg shadow-md dark:bg-gray-800">
                        <div class="p-4 bg-gray-200 dark:bg-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">NID Part 2</h3>
                        </div>
                        <img v-if="customer.nid_part_2" :src="getImageUrl(customer.nid_part_2)" alt="NID Part 2"
                            class="object-cover w-full h-64" @error="handleImageError" />
                    </div>
                    <div class="px-4 pt-4">
                        <p class="font-bold text-gray-600 text-md dark:text-gray-400">
                            Created: {{ formatDate(customer.created_at) }}
                        </p>
                    </div>
                </div>

                <!-- Customer Details Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <DetailCard label="Name (Bangla)" :value="customer.name_bn" />
                        <DetailCard label="Father's Name" :value="customer.father_name" />
                        <DetailCard label="Mother's Name" :value="customer.mother_name" />
                        <DetailCard label="Spouse Name" :value="customer.spouse_name" />
                        <DetailCard label="Date of Birth" :value="formatDate(customer.dob)" />
                        <DetailCard label="NID Number" :value="customer.nid_number" />
                        <DetailCard label="Phone Number" :value="customer.phone_number" />
                        <DetailCard label="Branch" :value="customer.branch?.branch_name" />
                        <DetailCard label="Created By" :value="customer.user?.name" />
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="grid grid-cols-2 gap-6 p-6">
                    <div
                        class="p-6 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700">
                        <h3 class="pb-2 mb-3 text-lg font-semibold text-gray-800 border-b dark:text-gray-200">
                            Address Information
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 min-h-[100px]">
                            {{ customer.address || 'No address provided' }}
                        </p>
                    </div>

                    <div
                        class="p-6 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700">
                        <h3 class="pb-2 mb-3 text-lg font-semibold text-gray-800 border-b dark:text-gray-200">
                            Details
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 min-h-[100px]">
                            {{ customer.details || 'No additional details' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Profile Modal -->
        <ProfileModal :customer="customer" :visible="isProfileModalVisible" @close="closeProfileModal" />
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ProfileModal from '@/Components/Modals/ProfileModal.vue';
import DetailCard from '@/Components/Cards/DetailCard.vue';

const props = defineProps({
    customer: {
        type: Object,
        required: true
    }
});

const isProfileModalVisible = ref(false);

function openProfileModal() {
    isProfileModalVisible.value = true;
}

function closeProfileModal() {
    isProfileModalVisible.value = false;
}

function getImageUrl(imagePath) {
    return imagePath ? `${location.origin}/storage/app/public/${imagePath}` : '';
}

function handleImageError(event) {
    event.target.src = '/default-image.png';
}

const formatDate = (date) => {
    if (!date) return '';
    const dateObj = new Date(date);
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    return `${day}/${month}/${year}`;
};

function printProfile() {
    window.print();
}
</script>

<style scoped>
@media print {
    body * {
        visibility: hidden;
    }

    #printable-area,
    #printable-area * {
        visibility: visible;
    }

    #printable-area {
        position: absolute;
        left: 0;
        top: 0;
    }
}

.btn-primary {
    @apply flex items-center bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 transition duration-200 ease-in-out;
}

.btn-secondary {
    @apply flex items-center bg-gray-100 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-gray-200 transition duration-200 ease-in-out;
}
</style>
