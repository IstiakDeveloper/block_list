//resources/js/Pages/PaymentReceipts/Index.vue
<template>
    <AdminLayout title="Payment Receipts">
        <template #header>

        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-800">
                                Payment Receipts Management
                            </h2>
                            <button @click="openNewEntryModal"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Add New Entry
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-6 bg-white rounded-lg shadow p-4">
                    <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Date Range -->
                        <div>
                            <Label for="start_date" value="Start Date" />
                            <Input id="start_date" type="date" v-model="filters.start_date" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <Label for="end_date" value="End Date" />
                            <Input id="end_date" type="date" v-model="filters.end_date" class="mt-1 block w-full" />
                        </div>

                        <!-- Branch Selector (Only for Super Admin) -->
                        <div v-if="isSuperAdmin">
                            <Label for="branch_id" value="Branch" />
                            <select id="branch_id" v-model="filters.branch_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Branches</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                    {{ branch.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex items-end space-x-2">
                            <PrimaryButton type="submit" :disabled="processing">
                                Apply Filters
                            </PrimaryButton>
                            <SecondaryButton type="button" @click="resetFilters" :disabled="processing">
                                Reset
                            </SecondaryButton>
                            <SecondaryButton type="button" @click="exportData" :disabled="processing">
                                Export
                            </SecondaryButton>
                        </div>
                    </form>
                </div>


                <!-- Branch Summaries (For Super Admin) -->
                <div v-if="isSuperAdmin" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="summary in branchSummaries" :key="summary.branch_id"
                            class="bg-white rounded-lg shadow p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ summary.branch.name }}</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-600">Total Received</div>
                                    <div class="text-xl font-semibold">{{ summary.total_received }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600">Total Distributed</div>
                                    <div class="text-xl font-semibold">{{ summary.total_distributed }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600">Available</div>
                                    <div class="text-xl font-semibold">{{ summary.current_available }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600">Cumulative</div>
                                    <div class="text-xl font-semibold">{{ summary.cumulative_total }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branch Summary -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 p-4 rounded">
                                <div class="text-sm text-gray-600">Total Received</div>
                                <div class="text-2xl font-semibold">{{ branchSummary?.total_received || 0 }}</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded">
                                <div class="text-sm text-gray-600">Total Distributed</div>
                                <div class="text-2xl font-semibold">{{ branchSummary?.total_distributed || 0 }}</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded">
                                <div class="text-sm text-gray-600">Available Receipts</div>
                                <div class="text-2xl font-semibold">{{ branchSummary?.current_available || 0 }}</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded">
                                <div class="text-sm text-gray-600">Cumulative Total</div>
                                <div class="text-2xl font-semibold">{{ branchSummary?.cumulative_total || 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Records Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date
                                        </th>
                                        <!-- Receive Section -->
                                        <th colspan="5"
                                            class="px-4 py-3 text-center text-xs font-medium text-green-600 uppercase bg-green-50">
                                            Receive Section
                                        </th>
                                        <!-- Distribution Section -->
                                        <th colspan="6"
                                            class="px-4 py-3 text-center text-xs font-medium text-blue-600 uppercase bg-blue-50">
                                            Distribution Section
                                        </th>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                        <!-- Receive Headers -->
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Qty</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">From #</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">To #</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Received By
                                        </th>
                                        <!-- Distribution Headers -->
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Given To</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">PIN</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">From #</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">To #</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Book #</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Available</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="receipt in receipts.data" :key="receipt.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ formatDate(receipt.transaction_date) }}</td>
                                        <!-- Receive Data -->
                                        <td class="px-4 py-2" :class="{ 'text-gray-400': !receipt.receive_quantity }">
                                            {{ receipt.receive_quantity || '-' }}
                                        </td>
                                        <td class="px-4 py-2"
                                            :class="{ 'text-gray-400': !receipt.receipt_from_number }">
                                            {{ receipt.receipt_from_number || '-' }}
                                        </td>
                                        <td class="px-4 py-2" :class="{ 'text-gray-400': !receipt.receipt_to_number }">
                                            {{ receipt.receipt_to_number || '-' }}
                                        </td>
                                        <td class="px-4 py-2">{{ receipt.total_cumulative_quantity }}</td>
                                        <td class="px-4 py-2" :class="{ 'text-gray-400': !receipt.received_by }">
                                            {{ receipt.received_by || '-' }}
                                        </td>
                                        <!-- Distribution Data -->
                                        <td class="px-4 py-2" :class="{ 'text-gray-400': !receipt.given_to }">
                                            {{ receipt.given_to || '-' }}
                                        </td>
                                        <td class="px-4 py-2" :class="{ 'text-gray-400': !receipt.pin_number }">
                                            {{ receipt.pin_number || '-' }}
                                        </td>
                                        <td class="px-4 py-2" :class="{ 'text-gray-400': !receipt.given_from_number }">
                                            {{ receipt.given_from_number || '-' }}
                                        </td>
                                        <td class="px-4 py-2" :class="{ 'text-gray-400': !receipt.given_to_number }">
                                            {{ receipt.given_to_number || '-' }}
                                        </td>
                                        <td class="px-4 py-2"
                                            :class="{ 'text-gray-400': !receipt.receipt_book_number }">
                                            {{ receipt.receipt_book_number || '-' }}
                                        </td>
                                        <td class="px-4 py-2">{{ receipt.available_receipts }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4" v-if="receipts.links">
                            <Pagination :links="receipts.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Entry Modal -->
        <Modal :show="showNewEntryModal" @close="closeNewEntryModal" maxWidth="2xl">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    New Receipt Entry
                </h2>

                <!-- Error Message Display -->
                <div v-if="Object.keys(form.errors).length > 0"
                    class="mb-4 p-4 bg-red-50 border border-red-100 rounded">
                    <div v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600">
                        {{ error }}
                    </div>
                </div>

                <form @submit.prevent="submitForm">
                    <!-- Date -->
                    <div class="mb-4">
                        <Label for="transaction_date" value="Date" required />
                        <Input id="transaction_date" type="date" v-model="form.transaction_date"
                            :error="form.errors.transaction_date" class="mt-1 block w-full" required />
                    </div>

                    <!-- Receive Section -->
                    <div class="mb-6 border-t pt-4">
                        <h3 class="text-md font-medium text-green-600 mb-4">
                            Receive Section
                            <span class="text-sm text-gray-500 ml-2">(Optional if only distributing)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="receive_quantity" value="Quantity" />
                                <Input id="receive_quantity" type="number" v-model="form.receive_quantity"
                                    :error="form.errors.receive_quantity" class="mt-1 block w-full" min="0"
                                    @input="validateReceiveSection" />
                            </div>
                            <div>
                                <Label for="received_by" value="Received By" />
                                <Input id="received_by" type="text" v-model="form.received_by"
                                    :error="form.errors.received_by" class="mt-1 block w-full"
                                    :required="!!form.receive_quantity" />
                            </div>
                            <div>
                                <Label for="receipt_from_number" value="From Number" />
                                <Input id="receipt_from_number" type="number" v-model="form.receipt_from_number"
                                    :error="form.errors.receipt_from_number" class="mt-1 block w-full" min="1"
                                    :required="!!form.receive_quantity" />
                            </div>
                            <div>
                                <Label for="receipt_to_number" value="To Number" />
                                <Input id="receipt_to_number" type="number" v-model="form.receipt_to_number"
                                    :error="form.errors.receipt_to_number" class="mt-1 block w-full" min="1"
                                    :required="!!form.receive_quantity" />
                            </div>
                        </div>
                    </div>

                    <!-- Distribution Section -->
                    <div class="mb-6 border-t pt-4">
                        <h3 class="text-md font-medium text-blue-600 mb-4">
                            Distribution Section
                            <span class="text-sm text-gray-500 ml-2">(Optional if only receiving)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="given_quantity" value="Given Quantity" />
                                <Input id="given_quantity" type="number" v-model="form.given_quantity"
                                    :error="form.errors.given_quantity" class="mt-1 block w-full" min="0"
                                    @input="validateDistributeSection" />
                            </div>
                            <div>
                                <Label for="given_to" value="Given To" />
                                <Input id="given_to" type="text" v-model="form.given_to" :error="form.errors.given_to"
                                    class="mt-1 block w-full" :required="!!form.given_quantity" />
                            </div>
                            <div>
                                <Label for="pin_number" value="PIN Number" />
                                <Input id="pin_number" type="text" v-model="form.pin_number"
                                    :error="form.errors.pin_number" class="mt-1 block w-full"
                                    :required="!!form.given_quantity" />
                            </div>
                            <div>
                                <Label for="given_from_number" value="From Number" />
                                <Input id="given_from_number" type="number" v-model="form.given_from_number"
                                    :error="form.errors.given_from_number" class="mt-1 block w-full" min="1"
                                    :required="!!form.given_quantity" />
                            </div>
                            <div>
                                <Label for="given_to_number" value="To Number" />
                                <Input id="given_to_number" type="number" v-model="form.given_to_number"
                                    :error="form.errors.given_to_number" class="mt-1 block w-full" min="1"
                                    :required="!!form.given_quantity" />
                            </div>
                            <div>
                                <Label for="receipt_book_number" value="Book Number" />
                                <Input id="receipt_book_number" type="text" v-model="form.receipt_book_number"
                                    :error="form.errors.receipt_book_number" class="mt-1 block w-full"
                                    :required="!!form.given_quantity" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <SecondaryButton @click="closeNewEntryModal" type="button" class="mr-3">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="form.processing || !isFormValid">
                            {{ form.processing ? 'Saving...' : 'Save Entry' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    receipts: Object,
    branchSummaries: [Object, Array],
    branches: Array,
    filters: Object,
    isSuperAdmin: Boolean
});

const showNewEntryModal = ref(false);
const processing = ref(false);


const form = useForm({
    transaction_date: new Date().toISOString().split('T')[0],
    receive_quantity: 0,
    receipt_from_number: null,
    receipt_to_number: null,
    received_by: '',
    given_to: '',
    pin_number: '',
    given_from_number: null,
    given_to_number: null,
    receipt_book_number: '',
    given_quantity: 0
});

const filters = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
    branch_id: props.filters.branch_id || ''
});

const applyFilters = () => {
    processing.value = true;
    router.get(route('payment-receipts.index'), {
        start_date: filters.value.start_date,
        end_date: filters.value.end_date,
        branch_id: filters.value.branch_id
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            processing.value = false;
        }
    });
};
const resetFilters = () => {
    filters.value = {
        start_date: '',
        end_date: '',
        branch_id: ''
    };
    applyFilters();
};

const exportData = () => {
    processing.value = true;
    window.location.href = route('payment-receipts.export', {
        start_date: filters.value.start_date,
        end_date: filters.value.end_date,
        branch_id: filters.value.branch_id
    });
    processing.value = false;
};
// Form validation
const isFormValid = computed(() => {
    // Basic validation - at least one section must be filled
    if (form.receive_quantity === 0 && form.given_quantity === 0) {
        return false;
    }

    // Validate receive section if quantity is provided
    if (form.receive_quantity > 0) {
        if (!form.receipt_from_number ||
            !form.receipt_to_number ||
            !form.received_by ||
            form.receipt_to_number <= form.receipt_from_number) {
            return false;
        }
    }

    // Validate distribute section if quantity is provided
    if (form.given_quantity > 0) {
        if (!form.given_to ||
            !form.pin_number ||
            !form.given_from_number ||
            !form.given_to_number ||
            !form.receipt_book_number ||
            form.given_to_number <= form.given_from_number) {
            return false;
        }
    }

    return true;
});

// Format date for display
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Validate receive section when quantity changes
const validateReceiveSection = () => {
    form.clearErrors();

    if (form.receive_quantity > 0) {
        if (!form.receipt_from_number || !form.receipt_to_number) {
            return;
        }

        // Validate receipt number range
        if (form.receipt_to_number <= form.receipt_from_number) {
            form.setError('receipt_to_number', 'To number must be greater than From number');
        }

        // Validate quantity matches range
        const calculatedQuantity = form.receipt_to_number - form.receipt_from_number + 1;
        if (calculatedQuantity !== parseInt(form.receive_quantity)) {
            form.setError('receive_quantity', 'Quantity does not match the receipt number range');
        }
    }
};

// Validate distribute section when quantity changes
const validateDistributeSection = () => {
    form.clearErrors();

    if (form.given_quantity > 0) {
        if (!form.given_from_number || !form.given_to_number) {
            return;
        }

        // Validate receipt number range
        if (form.given_to_number <= form.given_from_number) {
            form.setError('given_to_number', 'To number must be greater than From number');
        }

        // Validate quantity matches range
        const calculatedQuantity = form.given_to_number - form.given_from_number + 1;
        if (calculatedQuantity !== parseInt(form.given_quantity)) {
            form.setError('given_quantity', 'Quantity does not match the receipt number range');
        }

        // Validate against available receipts
        const availableReceipts = props.branchSummary?.current_available || 0;
        if (form.given_quantity > (availableReceipts + form.receive_quantity)) {
            form.setError('given_quantity', 'Not enough receipts available for distribution');
        }
    }
};

const openNewEntryModal = () => {
    form.reset();
    form.transaction_date = new Date().toISOString().split('T')[0];
    form.clearErrors();
    showNewEntryModal.value = true;
};

const closeNewEntryModal = () => {
    form.reset();
    form.clearErrors();
    showNewEntryModal.value = false;
};

const submitForm = () => {
    if (!isFormValid.value) {
        return;
    }

    form.post(route('payment-receipts.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeNewEntryModal();
        },
        onError: () => {
            // Errors will be displayed automatically by the form
            console.log('Form submission failed:', form.errors);
        }
    });
};
</script>

<style scoped>
.table-header-group {
    background-color: #f9fafb;
}

.receipt-row:hover {
    background-color: #f3f4f6;
}

/* Additional styling for validation states */
.invalid-field {
    border-color: #ef4444;
}

.invalid-field:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 1px #ef4444;
}
</style>
