<template>
    <div class="flex flex-col min-h-screen bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-800 dark:to-purple-900">
      <!-- Header -->
      <header class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 shadow-md">
        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Mousumi</h1>
        <div class="flex items-center">
          <button @click="switchTheme" class="text-gray-500 dark:text-white hover:text-gray-600">
            <i class="fa-solid fa-circle-half-stroke"></i>
          </button>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 flex items-center justify-center p-6">
        <LoadingProgress />
        <div class="w-full overflow-hidden bg-white dark:bg-gray-900 px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
          <slot></slot>
        </div>
      </main>

      <!-- Footer -->
      <footer class="p-4 text-center bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-200">
        <p>&copy; {{ new Date().getFullYear() }} Mousumi NGO. All rights reserved.</p>
      </footer>
    </div>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { switchTheme } from '@/theme'; // Assume the same theme switching function
import LoadingProgress from '@/Components/LoadingProgress.vue';

  // Additional setup logic can be placed here
  </script>

  <style scoped>
  /* Add any specific styles for the GuestLayout here */
  </style>
