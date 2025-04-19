<template>
    <AdminLayout>
      <div class="p-4">
        <h2 class="mb-4 text-xl font-bold">Edit Voluntary Saving</h2>
        <VoluntarySavingForm :voluntarySaving="voluntarySaving" />
      </div>
    </AdminLayout>
  </template>

  <script setup>
  defineProps(['voluntarySaving'])
  import AdminLayout from '@/Layouts/AdminLayout.vue'
  import VoluntarySavingForm from './Partials/VoluntarySavingForm.vue'
  </script>
