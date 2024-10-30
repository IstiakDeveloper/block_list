<template>
    <div class="w-full">
      <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <!-- Search and Filter Section -->
        <div class="p-4 bg-white dark:bg-gray-800 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
          <slot name="filters"></slot>
        </div>

        <!-- Responsive Table -->
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <slot name="header"></slot>
            </tr>
          </thead>
          <tbody>
            <slot name="body"></slot>
          </tbody>
        </table>

        <!-- Pagination Section -->
        <div class="p-4 bg-white dark:bg-gray-800">
          <slot name="pagination"></slot>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: 'BaseTable'
  }
  </script>
