<template>

    <Head title="Create Customer" />
    <AdminLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8 max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-300 border-b pb-3">
                    Create New Customer
                </h1>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Branch Selection with Validation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <BranchSelectCustomer :branches="branches" :form="form" :errors="errors" />
                        </div>

                        <!-- NID File Uploads with Preview and Validation -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <FormLabel>NID Part 1</FormLabel>
                                <FileUpload :file="form.nid_part_1"
                                    @update:file="handleFileUpload('nid_part_1', $event)" :error="errors.nid_part_1" />
                            </div>
                            <div>
                                <FormLabel>NID Part 2</FormLabel>
                                <FileUpload :file="form.nid_part_2"
                                    @update:file="handleFileUpload('nid_part_2', $event)" :error="errors.nid_part_2" />
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <FormInput label="Full Name" v-model="form.name" :error="errors.name" required />
                        <FormInput label="Name (Bangla)" v-model="form.name_bn" :error="errors.name_bn" />
                        <FormInput label="Father's Name" v-model="form.father_name" :error="errors.father_name" />
                        <FormInput label="Mother's Name" v-model="form.mother_name" :error="errors.mother_name" />
                        <FormInput label="Spouse Name" v-model="form.spouse_name" :error="errors.spouse_name" />
                        <FormInput label="Date of Birth" type="date" v-model="form.dob" :error="errors.dob" />
                    </div>

                    <!-- Contact & Identification Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <FormInput label="NID Number" v-model="form.nid_number" :error="errors.nid_number" required />
                        <FormInput label="Phone Number" v-model="form.phone_number" :error="errors.phone_number"
                            required />
                    </div>

                    <!-- Address and Details -->
                    <div class="space-y-6">
                        <FormTextarea label="Address" v-model="form.address" :error="errors.address" />
                        <FormTextarea label="Details" v-model="form.details" :error="errors.details" />
                    </div>

                    <!-- Form Submission -->
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" @click="resetForm" class="btn-secondary">
                            Reset
                        </button>
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Customer</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormInput from '@/Components/FormInput.vue';
import FormLabel from '@/Components/FormLabel.vue';
import FormError from '@/Components/FormError.vue';
import FormTextarea from '@/Components/FormTextarea.vue';
import FileUpload from '@/Components/FileUpload.vue';
import BranchSelectCustomer from '@/Components/BranchSelectCustomer.vue';

const props = defineProps({
    branches: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    branch_id: '',
    nid_part_1: null,
    nid_part_2: null,
    name: '',
    name_bn: '',
    father_name: '',
    mother_name: '',
    spouse_name: '',
    dob: '',
    nid_number: '',
    phone_number: '',
    address: '',
    details: ''
});

const errors = ref({});

// Automatically select branch if only one is available
onMounted(() => {
    if (props.branches.length === 1) {
        form.branch_id = props.branches[0].id;
    }
});

function handleFileUpload(field, file) {
    form[field] = file;
}

function submitForm() {
    form.post(route('admin.customers.store'), {
        forceFormData: true,
        onSuccess: () => {
            // Reset form or redirect
        },
        onError: (err) => {
            errors.value = err;
        }
    });
}

function resetForm() {
    form.reset();
    errors.value = {};

    // Reapply auto-branch selection if only one branch exists
    if (props.branches.length === 1) {
        form.branch_id = props.branches[0].id;
    }
}
</script>

<style scoped>
/* Tailwind utility classes for form styling */
.form-input {
    @apply w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 dark:bg-gray-700 dark:text-white;
}

.btn-primary {
    @apply bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow hover:bg-blue-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed;
}

.btn-secondary {
    @apply bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded shadow hover:bg-gray-300 transition duration-200;
}
</style>
