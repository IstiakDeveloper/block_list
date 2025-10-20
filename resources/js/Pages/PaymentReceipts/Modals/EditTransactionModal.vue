<template>
    <Modal :show="show" @close="handleClose" maxWidth="2xl">
        <div class="p-6 bg-white rounded-lg dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Edit Transaction
                </h2>
                <button @click="handleClose" type="button"
                    class="p-2 text-gray-400 transition-colors rounded-lg hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Error Display -->
            <div v-if="form.errors && Object.keys(form.errors).length > 0"
                class="flex items-start gap-2 p-4 mb-4 border border-red-400 rounded-lg bg-red-50 dark:bg-red-900/30 dark:border-red-500">
                <AlertCircle class="w-5 h-5 mt-0.5 text-red-600 dark:text-red-400 flex-shrink-0" />
                <div class="space-y-1">
                    <div v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600 dark:text-red-400">
                        {{ error }}
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitForm" v-if="transaction">
                <!-- Transaction Info (Read-only) -->
                <div class="p-4 mb-6 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Transaction Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">#{{ transaction.id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Date:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ transaction.transaction_date }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Type:</span>
                            <span class="ml-2 font-medium">
                                <span :class="getTypeClass(transaction.transaction_type)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ getTypeLabel(transaction.transaction_type) }}
                                </span>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Books:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">
                                {{ transaction.book_from }}-{{ transaction.book_to }} ({{ transaction.total_books }})
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Edit Fields based on transaction type -->
                <div v-if="transaction.transaction_type === 'distribute_to_person'" class="space-y-4">
                    <div class="p-4 border border-purple-200 rounded-lg bg-purple-50 dark:bg-purple-900/20 dark:border-purple-700">
                        <h3 class="flex items-center gap-2 mb-4 text-sm font-semibold text-purple-900 dark:text-purple-100">
                            <User class="w-4 h-4" />
                            Person Information (Editable)
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <Label value="Given To" required class="text-gray-700 dark:text-gray-300" />
                                <Input v-model="form.given_to" type="text" required
                                    class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                            </div>
                            <div>
                                <Label value="PIN Number" class="text-gray-700 dark:text-gray-300" />
                                <Input v-model="form.pin_number" type="text"
                                    class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="transaction.transaction_type === 'distribute_to_branch'" class="space-y-4">
                    <div class="p-4 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700">
                        <h3 class="flex items-center gap-2 mb-4 text-sm font-semibold text-blue-900 dark:text-blue-100">
                            <Building class="w-4 h-4" />
                            Branch Information (Editable)
                        </h3>
                        <div>
                            <Label value="Received By" class="text-gray-700 dark:text-gray-300" />
                            <Input v-model="form.received_by" type="text"
                                class="block w-full mt-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Person who received the books at the branch
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Remarks (Optional) -->
                <div class="mt-4">
                    <Label value="Remarks (Optional)" class="text-gray-700 dark:text-gray-300" />
                    <textarea v-model="form.remarks" rows="3"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Add any notes or remarks..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <SecondaryButton @click="handleClose" type="button" :disabled="form.processing">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                        <Save v-else class="w-4 h-4 mr-2" />
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Loader2, X, AlertCircle, User, Building, Save } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    transaction: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'success']);

const form = useForm({
    given_to: '',
    pin_number: '',
    received_by: '',
    remarks: ''
});

// Watch for transaction changes to populate form
watch(() => props.transaction, (newTransaction) => {
    if (newTransaction) {
        form.given_to = newTransaction.given_to || '';
        form.pin_number = newTransaction.pin_number || '';
        form.received_by = newTransaction.received_by || '';
        form.remarks = newTransaction.remarks || '';
    }
}, { immediate: true });

const getTypeLabel = (type) => {
    const labels = {
        'stock_in': 'Stock In',
        'distribute_to_branch': 'To Branch',
        'distribute_to_person': 'To Person'
    };
    return labels[type] || type;
};

const getTypeClass = (type) => {
    const classes = {
        'stock_in': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
        'distribute_to_branch': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        'distribute_to_person': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

const submitForm = () => {
    if (!props.transaction) return;

    form.put(route('payment-receipts.update', { receipt: props.transaction.id }), {
        preserveScroll: true,
        onSuccess: () => {
            emit('success');
            emit('close');
        },
        onError: (errors) => {
            console.error('Update failed:', errors);
        }
    });
};

const handleClose = () => {
    if (!form.processing) {
        form.reset();
        form.clearErrors();
        emit('close');
    }
};
</script>
