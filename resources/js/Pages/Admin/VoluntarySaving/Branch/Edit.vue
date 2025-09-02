<template>

    <Head title="Create Voluntary Saving Application" />

    <AdminLayout>
        <div class="container px-4 py-8 mx-auto">
            <div class="mb-6">
                <Link href="/branch/voluntary-savings"
                    class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Voluntary Savings
                </Link>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Create Voluntary Saving Application
                    </h1>
                </div>

                <form @submit.prevent="submitForm" class="p-6">
                    <div class="mb-8">
                        <h2 class="mb-4 text-lg font-medium text-gray-800 dark:text-gray-200">Application Information
                        </h2>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Application Date -->
                            <div>
                                <label for="application_date"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Application Date*
                                </label>
                                <input type="date" id="application_date" v-model="form.application_date"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                    required />
                                <div v-if="form.errors.application_date"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.application_date }}
                                </div>
                            </div>

                            <!-- Somiti Name -->
                            <div>
                                <label for="somiti_name"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Somiti Name*
                                </label>
                                <input type="text" id="somiti_name" v-model="form.somiti_name"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                    required />
                                <div v-if="form.errors.somiti_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.somiti_name }}
                                </div>
                            </div>

                            <!-- Somiti Code -->
                            <div>
                                <label for="somiti_code"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Somiti Code*
                                </label>
                                <input type="text" id="somiti_code" v-model="form.somiti_code"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                    required />
                                <div v-if="form.errors.somiti_code" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.somiti_code }}
                                </div>
                            </div>

                            <!-- Member Name -->
                            <div>
                                <label for="member_name"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Member Name*
                                </label>
                                <input type="text" id="member_name" v-model="form.member_name"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                    required />
                                <div v-if="form.errors.member_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.member_name }}
                                </div>
                            </div>

                            <!-- Member Code -->
                            <div>
                                <label for="member_code"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Member Code*
                                </label>
                                <input type="text" id="member_code" v-model="form.member_code"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                    required />
                                <div v-if="form.errors.member_code" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.member_code }}
                                </div>
                            </div>

                            <!-- Member Mobile -->
                            <div>
                                <label for="member_mobile"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Member Mobile
                                </label>
                                <input type="text" id="member_mobile" v-model="form.member_mobile"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600" />
                                <div v-if="form.errors.member_mobile"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.member_mobile }}
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="mb-4 text-lg font-medium text-gray-800 dark:text-gray-200">Applicant Information</h2>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Applicant Name -->
                            <div>
                                <label for="applicant_name"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Applicant Name*
                                </label>
                                <input type="text" id="applicant_name" v-model="form.applicant_name"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                    required />
                                <div v-if="form.errors.applicant_name"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.applicant_name }}
                                </div>
                            </div>

                            <!-- Designation -->
                            <div>
                                <label for="designation"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Designation
                                </label>
                                <input type="text" id="designation" v-model="form.designation"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600" />
                                <div v-if="form.errors.designation" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.designation }}
                                </div>
                            </div>

                            <!-- PIN -->
                            <div>
                                <label for="pin"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    PIN
                                </label>
                                <input type="text" id="pin" v-model="form.pin"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600" />
                                <div v-if="form.errors.pin" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.pin }}
                                </div>
                            </div>

                            <!-- Signature -->
                            <div>
                                <label for="signature"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Signature
                                </label>
                                <input type="file" id="signature" @change="handleSignatureUpload"
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600" />
                                <div v-if="form.errors.signature" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.signature }}
                                </div>
                                <div v-if="signaturePreview" class="mt-2">
                                    <img :src="signaturePreview" alt="Signature Preview"
                                        class="h-20 border border-gray-300 rounded dark:border-gray-700">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200">Deposits*</h2>
                            <button type="button" @click="addDeposit"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Add Deposit
                            </button>
                        </div>

                        <div v-if="form.errors['deposits']"
                            class="p-3 mb-4 text-red-600 rounded-md bg-red-50 dark:bg-red-900/50 dark:text-red-400">
                            {{ form.errors['deposits'] }}
                        </div>

                        <div v-for="(deposit, index) in form.deposits" :key="index"
                            class="p-4 mb-4 border border-gray-200 rounded-md bg-gray-50 dark:bg-gray-700/50 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200">Deposit #{{ index + 1
                                    }}</h3>
                                <button type="button" @click="removeDeposit(index)"
                                    class="inline-flex items-center p-1 text-sm text-red-500 bg-red-100 rounded-full hover:bg-red-200 dark:bg-red-900/50 dark:text-red-400 dark:hover:bg-red-800/50">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2">
                                <!-- Deposit Date -->
                                <div>
                                    <label :for="`deposit_date_${index}`"
                                        class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Deposit Date*
                                    </label>
                                    <input type="date" :id="`deposit_date_${index}`" v-model="deposit.deposit_date"
                                        class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                        required />
                                    <div v-if="form.errors[`deposits.${index}.deposit_date`]"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors[`deposits.${index}.deposit_date`] }}
                                    </div>
                                </div>

                                <!-- Deposit Amount -->
                                <div>
                                    <label :for="`deposit_amount_${index}`"
                                        class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Deposit Amount (BDT)*
                                    </label>
                                    <input type="number" :id="`deposit_amount_${index}`"
                                        v-model="deposit.deposit_amount" step="0.01"
                                        class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                        required />
                                    <div v-if="form.errors[`deposits.${index}.deposit_amount`]"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors[`deposits.${index}.deposit_amount`] }}
                                    </div>
                                </div>

                                <!-- Account Name -->
                                <div>
                                    <label :for="`account_name_${index}`"
                                        class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Account Name*
                                    </label>
                                    <input type="text" :id="`account_name_${index}`" v-model="deposit.account_name"
                                        class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                        required />
                                    <div v-if="form.errors[`deposits.${index}.account_name`]"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors[`deposits.${index}.account_name`] }}
                                    </div>
                                </div>

                                <!-- Profit -->
                                <div>
                                    <label :for="`profit_${index}`"
                                        class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Profit (BDT)
                                    </label>
                                    <input type="number" :id="`profit_${index}`" v-model="deposit.profit" step="0.01"
                                        class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600" />
                                    <div v-if="form.errors[`deposits.${index}.profit`]"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors[`deposits.${index}.profit`] }}
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div v-if="form.deposits.length === 0"
                            class="p-6 text-center border border-gray-200 border-dashed rounded-md bg-gray-50 dark:bg-gray-700/50 dark:border-gray-700">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                No deposits added yet. Click "Add Deposit" to add one.
                            </p>
                            <button type="button" @click="addDeposit"
                                class="inline-flex items-center px-3 py-2 mt-4 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Add Deposit
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <Link href="/branch/voluntary-savings"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        Cancel
                        </Link>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }" :disabled="form.processing">
                            <svg v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>{{ form.processing ? 'Saving...' : 'Save Application' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, reactive, onMounted } from 'vue';

// Set up form with initial values
const form = useForm({
    application_date: new Date().toISOString().split('T')[0], // Today's date
    somiti_name: '',
    somiti_code: '',
    member_name: '',
    member_code: '',
    member_mobile: '',
    applicant_name: '',
    designation: '',
    pin: '',
    signature: null,
    deposits: []
});

// Store signature preview URL
const signaturePreview = ref(null);

// Handle signature file upload
const handleSignatureUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    form.signature = file;

    // Create a preview
    const reader = new FileReader();
    reader.onload = (e) => {
        signaturePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

// Add a new deposit
const addDeposit = () => {
    form.deposits.push({
        deposit_date: new Date().toISOString().split('T')[0],
        account_name: '',
        deposit_amount: null,
        profit: null,
    });
};

// Remove a deposit
const removeDeposit = (index) => {
    form.deposits.splice(index, 1);
};

// Submit the form
const submitForm = () => {
    form.post('/branch/voluntary-savings', {
        onSuccess: () => {
            // Reset the form's state after successful submission
            form.reset();
            signaturePreview.value = null;
        }
    });
};

// Add an initial deposit on component mount
onMounted(() => {
    addDeposit();
});
</script>
