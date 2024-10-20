<template>
    <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
      <p class="font-semibold text-gray-700 dark:text-gray-400">{{ label }}:</p>
      <p class="text-gray-600 dark:text-gray-400">{{ value }}</p>
    </div>
  </template>

  <script setup>
  defineProps({
    label: String,
    value: String,
  });
  </script>

  <style scoped>
  /* Optional styles for the DetailItem component */
  </style>
