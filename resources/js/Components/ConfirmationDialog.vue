<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center z-50 bg-gray-500 bg-opacity-75">
      <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg w-80 max-w-xs">
        <div class="flex items-center justify-between mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 text-red-500">
              <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
          </svg>
          <button @click="close" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <h3 class="text-lg font-semibold mb-4">Confirm Deletion</h3>
        <p class="mb-6">Are you sure you want to delete this item? This action cannot be undone.</p>
        <div class="flex justify-end space-x-4">
          <button @click="confirm" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
          <button @click="close" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      show: {
        type: Boolean,
        required: true,
      },
    },
    methods: {
      close() {
        this.$emit('update:show', false);
      },
      confirm() {
        this.$emit('confirm');
      },
    },
  };
  </script>

  <style scoped>
  /* You can add any additional styles here if necessary */
  </style>
