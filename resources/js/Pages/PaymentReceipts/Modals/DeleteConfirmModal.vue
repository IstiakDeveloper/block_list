<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="sm">
        <div class="p-4 bg-white rounded-lg dark:bg-gray-800">
            <!-- Icon & Title -->
            <div class="flex items-start mb-3">
                <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 mr-3 bg-red-100 rounded-full dark:bg-red-900/30">
                    <AlertTriangle class="w-5 h-5 text-red-600 dark:text-red-400" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Confirm Delete Transaction
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        This action cannot be undone
                    </p>
                </div>
            </div>

            <!-- Warning Message -->
            <div class="p-4 mb-5 border border-yellow-200 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-700">
                <div class="flex items-start gap-2">
                    <AlertCircle class="w-5 h-5 mt-0.5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" />
                    <div class="text-sm text-yellow-800 dark:text-yellow-200">
                        <p class="font-medium mb-1">What will happen:</p>
                        <ul class="space-y-1 list-disc list-inside">
                            <li>The transaction will be permanently deleted</li>
                            <li>Books will be restored to their previous location</li>
                            <li>Inventory counts will be updated</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Confirmation Text -->
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-300">
                Are you sure you want to delete this transaction? The books associated with this transaction
                will be restored and made available again.
            </p>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <SecondaryButton @click="$emit('close')" :disabled="deleting"
                    class="dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                    Cancel
                </SecondaryButton>
                <button @click="$emit('confirm')" :disabled="deleting"
                    class="inline-flex items-center gap-2 px-4 py-2 text-white transition-all bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed dark:focus:ring-offset-gray-800">
                    <Loader2 v-if="deleting" class="w-4 h-4 animate-spin" />
                    <Trash2 v-else class="w-4 h-4" />
                    {{ deleting ? 'Deleting...' : 'Delete Transaction' }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { AlertTriangle, Loader2, Trash2, AlertCircle } from 'lucide-vue-next';

defineProps({
    show: Boolean,
    deleting: {
        type: Boolean,
        default: false
    }
});

defineEmits(['close', 'confirm']);
</script>
