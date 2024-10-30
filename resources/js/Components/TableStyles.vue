
<template>
    <slot></slot>
  </template>

  <style>
  /* Base table styles */
  .responsive-table-container {
    @apply w-full overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0;
  }

  .responsive-table {
    @apply min-w-full divide-y divide-gray-200 dark:divide-gray-700;
  }

  /* Mobile card view */
  .mobile-card-view {
    @apply block lg:hidden bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4;
  }

  .mobile-card-row {
    @apply flex justify-between py-2 border-b dark:border-gray-700 last:border-0;
  }

  .mobile-card-label {
    @apply text-sm font-medium text-gray-500 dark:text-gray-400;
  }

  .mobile-card-value {
    @apply text-sm text-gray-900 dark:text-gray-100 text-right;
  }

  /* Desktop view enhancements */
  .table-header {
    @apply hidden lg:table-header-group bg-gray-50 dark:bg-gray-700;
  }

  .table-header th {
    @apply px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider;
  }

  .table-row {
    @apply hidden lg:table-row;
  }

  .table-cell {
    @apply px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100;
  }

  /* Action buttons container */
  .action-buttons {
    @apply flex flex-wrap gap-2 justify-end mt-4 lg:mt-0;
  }

  /* Responsive pagination */
  .pagination-container {
    @apply mt-4 flex flex-col sm:flex-row justify-between items-center gap-4;
  }

  .pagination-info {
    @apply text-sm text-gray-700 dark:text-gray-300;
  }

  .pagination-buttons {
    @apply flex justify-center items-center gap-2;
  }

  /* Common button styles */
  .table-btn {
    @apply inline-flex items-center px-3 py-2 rounded transition duration-200 text-sm;
  }

  .table-btn-blue {
    @apply text-blue-600 hover:text-blue-800 bg-blue-100 hover:bg-blue-200
           dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500 dark:hover:text-gray-100;
  }

  .table-btn-red {
    @apply text-red-600 hover:text-red-800 bg-red-100 hover:bg-red-200
           dark:bg-red-600 dark:text-white dark:hover:bg-red-500 dark:hover:text-gray-100;
  }

  .table-btn-green {
    @apply text-green-600 hover:text-green-800 bg-green-100 hover:bg-green-200
           dark:bg-green-600 dark:text-white dark:hover:bg-green-500 dark:hover:text-gray-100;
  }
  </style>
