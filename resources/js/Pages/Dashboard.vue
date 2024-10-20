<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import CustomerSearchResults from './Admin/CustomerSearchResults.vue';
</script>

<template>
    <Head title="Dashboard" />


        <CustomerSearchResults />
</template>
