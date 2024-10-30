<template>
    <svg v-if="field === currentSort"
         class="w-4 h-4 ml-1"
         fill="none"
         stroke="currentColor"
         viewBox="0 0 24 24">
        <path v-if="direction === 'asc'"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 15l7-7 7 7" />
        <path v-else
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 9l-7 7-7-7" />
    </svg>
    <svg v-else
         class="w-4 h-4 ml-1 text-gray-400"
         fill="none"
         stroke="currentColor"
         viewBox="0 0 24 24">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
    </svg>
</template>

<script setup>
defineProps({
    field: String,
    currentSort: String,
    direction: String
});
</script>
