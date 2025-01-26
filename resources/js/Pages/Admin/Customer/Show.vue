<template>
    <Head :title="customer ? customer.name : 'Customer Details'" />

    <AdminLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white">{{ customer.name }}</h1>
                            <p class="text-blue-100 mt-2">Customer Detailed Profile</p>
                        </div>
                        <div class="flex space-x-3">
                            <button @click="openProfileModal" class="btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                View Full Profile
                            </button>
                        </div>
                    </div>
                </div>

                <!-- NID Images Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-md">
                        <div class="p-4 bg-gray-200 dark:bg-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">NID Part 1</h3>
                        </div>
                        <img
                            v-if="customer.nid_part_1"
                            :src="getImageUrl(customer.nid_part_1)"
                            alt="NID Part 1"
                            class="w-full h-64 object-cover"
                            @error="handleImageError"
                        />
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-md">
                        <div class="p-4 bg-gray-200 dark:bg-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">NID Part 2</h3>
                        </div>
                        <img
                            v-if="customer.nid_part_2"
                            :src="getImageUrl(customer.nid_part_2)"
                            alt="NID Part 2"
                            class="w-full h-64 object-cover"
                            @error="handleImageError"
                        />
                    </div>
                </div>

                <!-- Customer Details Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                <div class="p-6 bg-gray-50 dark:bg-gray-800 border-t">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-200">Address</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ customer.address || 'No address provided' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-200">Additional Details</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ customer.details || 'No additional details' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Profile Modal -->
        <ProfileModal
            :customer="customer"
            :visible="isProfileModalVisible"
            @close="closeProfileModal"
        />
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
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

function formatDate(dateString) {
    return dateString
        ? new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
        : 'N/A';
}

function printProfile() {
    window.print();
}
</script>

<style scoped>
@media print {
    body * {
        visibility: hidden;
    }
    #printable-area, #printable-area * {
        visibility: visible;
    }
    #printable-area {
        position: absolute;
        left: 0;
        top: 0;
    }
}

.btn-primary {
    @apply flex items-center bg-blue-600 text-white font-semibold py-2 px-4
           rounded-lg shadow-md hover:bg-blue-700 transition duration-200 ease-in-out;
}

.btn-secondary {
    @apply flex items-center bg-gray-100 text-gray-800 font-semibold py-2 px-4
           rounded-lg shadow-md hover:bg-gray-200 transition duration-200 ease-in-out;
}
</style>
