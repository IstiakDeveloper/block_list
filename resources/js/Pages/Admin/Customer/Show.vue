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
          <div class="space-y-6">

            <!-- NID Images -->
            <div class="flex gap-6 items-center">
              <div class="flex-1">
                <p class="font-semibold text-gray-700 dark:text-gray-400">NID Part 1:</p>
                <img v-if="customer.nid_part_1" :src="'/storage/' + customer.nid_part_1" alt="NID Part 1" class="w-full rounded-lg mt-2 shadow-lg"/>
              </div>
              <div class="flex-1">
                <p class="font-semibold text-gray-700 dark:text-gray-400">NID Part 2:</p>
                <img v-if="customer.nid_part_2" :src="'/storage/' + customer.nid_part_2" alt="NID Part 2" class="w-full rounded-lg mt-2 shadow-lg"/>
              </div>
            </div>

            <!-- Details Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">Name (Bangla):</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.name_bn || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">Father's Name:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.father_name || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">Mother's Name:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.mother_name || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">Date of Birth:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.dob ? new Date(customer.dob).toLocaleDateString() : 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">NID Number:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.nid_number }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">Address:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.address || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">Details:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.details || 'N/A' }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">Branch:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.branch.branch_name }}</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700 dark:text-gray-400">Created By:</p>
                <p class="text-gray-600 dark:text-gray-400">{{ customer.user.name }}</p>
              </div>
            </div>

          </div>

          <!-- Print Button -->
          <div class="mt-6 flex justify-end">
            <button @click="printPage" class="btn-primary">
              Print
            </button>
          </div>

        </div>

        <!-- Confirmation Dialog -->
        <ConfirmationDialog
          :show="isDialogVisible"
          @update:show="isDialogVisible = $event"
          @confirm="deleteCustomer"
        />
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { Head, Link, useForm } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  import ConfirmationDialog from '@/Components/ConfirmationDialog.vue';

  const props = defineProps({
    customer: Object,
  });

  const isDialogVisible = ref(false);
  const form = useForm({});

  function confirmDelete(customerId) {
    isDialogVisible.value = true;
  }

  function deleteCustomer() {
    form.delete(route('admin.customers.destroy', props.customer.id), {
      onSuccess: () => {
        isDialogVisible.value = false;
        window.location.href = route('admin.customers.index');
      },
    });
  }

  function printPage() {
    window.print();
  }
  </script>

  <style scoped>
  /* Buttons */
  .btn-primary {
    @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 transition duration-200 ease-in-out;
  }

  .btn-secondary {
    @apply bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-red-700 transition duration-200 ease-in-out;
  }

  /* Image Styling */
  img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  /* Print-Style Overrides */
  @media print {
    body {
      font-size: 12px;
      line-height: 1.5;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 100%;
      padding: 0;
    }
    .bg-white, .dark\:bg-gray-900 {
      background: none;
    }
    .shadow-xl {
      box-shadow: none;
    }
    .btn-primary, .btn-secondary {
      display: none; /* Hide buttons on print */
    }
    .border-b {
      border-bottom: none; /* Remove border for cleaner print */
    }
    .space-y-6, .space-y-8 {
      margin-bottom: 0;
    }
    img {
      width: 100%;
      max-width: none; /* Remove restrictions for print */
    }
    .text-lg, .text-xl, .text-4xl {
      font-size: 14px; /* Adjust font sizes for printing */
    }
  }
  </style>
