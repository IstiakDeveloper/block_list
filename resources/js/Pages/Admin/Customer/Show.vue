<template>
    <Head :title="customer ? customer.name : 'Loading...'" />

    <AdminLayout>
      <div class="container mx-auto py-8">
        <div class="bg-white dark:bg-gray-900 shadow-xl rounded-lg p-8 space-y-8">
          <!-- Customer Name -->
          <div class="border-b pb-4">
            <h1 v-if="customer" class="text-4xl font-bold text-gray-900 dark:text-gray-100">{{ customer.name }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-lg">Customer Details</p>
          </div>

          <!-- Customer Details -->
          <div class="space-y-8">
            <div class="flex gap-6 items-center">
              <div class="flex-1">
                <p class="font-semibold text-gray-800 dark:text-gray-200 text-lg">NID Part 1:</p>
                <img
                  v-if="customer.nid_part_1"
                  :src="getImageUrl(customer.nid_part_1)"
                  alt="NID Part 1"
                  class="w-full rounded-lg mt-2 shadow-lg"
                  @error="handleImageError"
                />
              </div>
              <div class="flex-1">
                <p class="font-semibold text-gray-800 dark:text-gray-200 text-lg">NID Part 2:</p>
                <img
                  v-if="customer.nid_part_2"
                  :src="getImageUrl(customer.nid_part_2)"
                  alt="NID Part 2"
                  class="w-full rounded-lg mt-2 shadow-lg"
                  @error="handleImageError"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Name (Bangla):</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.name_bn || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Father's Name:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.father_name || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Spouse's Name:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.spouse_name || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Mother's Name:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.mother_name || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Date of Birth:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.dob ? new Date(customer.dob).toLocaleDateString() : 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">NID Number:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.nid_number }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Phone Number:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.phone_number}}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Address:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.address || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Details:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.details || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Branch:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.branch?.branch_name || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Created By:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.user?.name || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center p-4 border rounded-lg shadow-md bg-gray-50 dark:bg-gray-800">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Rejected By:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.rejected_by || 'N/A' }}</p>
              </div>
            </div>
          </div>

          <!-- View Details Button -->
          <div class="mt-6 flex justify-end">
            <button @click="openProfileModal" class="btn-primary">View Profile</button>
          </div>
        </div>
      </div>

      <!-- Modal for Customer Profile -->
      <div v-if="isProfileModalVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 w-full max-w-4xl">
          <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200 text-center">Customer Profile</h2>
          <div class="space-y-4">
            <div class="flex justify-between mb-4">
              <div class="flex-1 text-center">
                <p class="font-semibold">NID Part 1:</p>
                <img
                  v-if="customer.nid_part_1"
                  :src="getImageUrl(customer.nid_part_1)"
                  alt="Customer NID Part 1"
                  class="w-32 h-auto rounded-lg object-cover"
                  @error="handleImageError"
                />
              </div>
              <div class="flex-1 text-center">
                <p class="font-semibold">NID Part 2:</p>
                <img
                  v-if="customer.nid_part_2"
                  :src="getImageUrl(customer.nid_part_2)"
                  alt="Customer NID Part 2"
                  class="w-32 h-auto rounded-lg object-cover"
                  @error="handleImageError"
                />
              </div>
            </div>
            <p class="text-gray-700 dark:text-gray-300"><strong>Name (Bangla):</strong> {{ customer.name_bn || 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Father's Name:</strong> {{ customer.father_name || 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Spouse's Name:</strong> {{ customer.spouse_name || 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Mother's Name:</strong> {{ customer.mother_name || 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Date of Birth:</strong> {{ customer.dob ? new Date(customer.dob).toLocaleDateString() : 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>NID Number:</strong> {{ customer.nid_number }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>NID Number:</strong> {{ customer.phone_number }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Address:</strong> {{ customer.address || 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Details:</strong> {{ customer.details || 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Branch:</strong> {{ customer.branch?.branch_name || 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Created By:</strong> {{ customer.user?.name || 'N/A' }}</p>
            <p class="text-gray-700 dark:text-gray-300"><strong>Rejected By:</strong> {{ customer.rejected_by || 'N/A' }}</p>
          </div>

          <div class="flex justify-center mt-6 space-x-4">
            <button @click="printProfile" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">Print Profile</button>
            <button @click="closeProfileModal" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow">Close</button>
          </div>
        </div>
      </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  customer: Object,
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
  event.target.src = '/image.jpg'; // Set a placeholder image on error
}

function printProfile() {
  const printWindow = window.open('', '_blank', 'width=800,height=600');
  printWindow.document.write(`
    <html>
      <head>
        <title>Print Profile</title>
        <style>
          body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; margin: 0; padding: 20px; }
          img { max-width: 50%; height: auto; }
        </style>
      </head>
      <body>
        <h2>Customer Profile</h2>
        <p><strong>Name (Bangla):</strong> ${props.customer.name_bn || 'N/A'}</p>
        <p><strong>Father's Name:</strong> ${props.customer.father_name || 'N/A'}</p>
        <p><strong>Father's Name:</strong> ${props.customer.spouse_name || 'N/A'}</p>
        <p><strong>Mother's Name:</strong> ${props.customer.mother_name || 'N/A'}</p>
        <p><strong>Date of Birth:</strong> ${props.customer.dob ? new Date(props.customer.dob).toLocaleDateString() : 'N/A'}</p>
        <p><strong>NID Number:</strong> ${props.customer.nid_number}</p>
        <p><strong>NID Number:</strong> ${props.customer.phone_number}</p>
        <p><strong>Address:</strong> ${props.customer.address || 'N/A'}</p>
        <p><strong>Details:</strong> ${props.customer.details || 'N/A'}</p>
        <p><strong>Branch:</strong> ${props.customer.branch?.branch_name || 'N/A'}</p>
        <p><strong>Created By:</strong> ${props.customer.user?.name || 'N/A'}</p>
          <p><strong>Rejected By:</strong> ${props.customer.rejected_by || 'N/A'}</p>
        <img src="${getImageUrl(props.customer.nid_part_1)}" alt="Customer NID Part 1" />
        <img src="${getImageUrl(props.customer.nid_part_2)}" alt="Customer NID Part 2" />
      </body>
    </html>
  `);

  printWindow.document.close();
  printWindow.print();
}
</script>

<style scoped>
.btn-primary {
  @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 transition duration-200 ease-in-out;
}

/* Modal Styles */
.fixed {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
