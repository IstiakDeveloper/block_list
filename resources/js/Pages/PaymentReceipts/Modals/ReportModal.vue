<template>
    <Modal :show="show" @close="handleClose" maxWidth="md">
        <div class="p-4 bg-white rounded-lg dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Generate PDF Report
                </h2>
                <button @click="handleClose"
                    class="p-2 text-gray-400 transition-colors rounded-lg hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <X class="w-5 h-5" />
                </button>
            </div>

            <form @submit.prevent="generateReport">
                <div class="space-y-5">
                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label value="Start Date" class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="reportForm.start_date" placeholder="dd/mm/yyyy"
                                class="block w-full mt-1" required />
                        </div>
                        <div>
                            <Label value="End Date" class="text-gray-700 dark:text-gray-300" />
                            <CustomDateInput v-model="reportForm.end_date" placeholder="dd/mm/yyyy"
                                class="block w-full mt-1" required />
                        </div>
                    </div>

                    <!-- Branch Selection -->
                    <div>
                        <Label value="Branch (Optional)" class="text-gray-700 dark:text-gray-300" />
                        <select v-model="reportForm.branch_id"
                            class="block w-full mt-1 border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Branches</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                {{ branch.branch_code }} - {{ branch.branch_name }}
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Leave empty to generate report for all branches
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <input
                            id="report-include-transactions"
                            v-model="reportForm.include_transactions"
                            type="checkbox"
                            class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                        />
                        <label for="report-include-transactions" class="text-sm text-left text-gray-700 dark:text-gray-300">
                            <span class="font-medium">Include transaction list</span>
                            <span class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                Adds a detailed transactions table to the PDF. Leave off for branch summary only (smaller, faster).
                            </span>
                        </label>
                    </div>

                    <!-- Error Display -->
                    <div v-if="reportForm.error"
                        class="flex items-start gap-2 p-3 border border-red-200 rounded-lg bg-red-50 dark:bg-red-900/50 dark:border-red-800">
                        <AlertCircle class="w-5 h-5 mt-0.5 text-red-500 flex-shrink-0" />
                        <div>
                            <p class="text-sm font-medium text-red-800 dark:text-red-300">Error</p>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ reportForm.error }}</p>
                        </div>
                    </div>

                    <!-- Processing Indicator -->
                    <div v-if="reportForm.processing"
                        class="flex items-center gap-2 p-3 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/50 dark:border-blue-800">
                        <Loader2 class="w-5 h-5 text-blue-500 animate-spin" />
                        <span class="text-sm text-blue-600 dark:text-blue-400">
                            Generating report, please wait...
                        </span>
                    </div>

                    <!-- Info Message -->
                    <div class="flex items-start gap-2 p-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                        <Info class="w-5 h-5 mt-0.5 text-gray-500 dark:text-gray-400 flex-shrink-0" />
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            PDF includes branch-wise totals for the selected dates. Turn on “Include transaction list” if you also need each movement line-by-line.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <SecondaryButton @click="handleClose" type="button" :disabled="reportForm.processing">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton :disabled="!isFormValid || reportForm.processing">
                        <Loader2 v-if="reportForm.processing" class="w-4 h-4 mr-2 animate-spin" />
                        <FileText v-else class="w-4 h-4 mr-2" />
                        {{ reportForm.processing ? 'Generating...' : 'Generate PDF' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { reactive, computed, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import Label from '@/Components/Label.vue';
import CustomDateInput from '@/Components/CustomDateInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Loader2, AlertCircle, FileText, Info, X } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    branches: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close']);

const reportForm = reactive({
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || '',
    branch_id: props.filters?.branch_id || '',
    include_transactions: false,
    processing: false,
    error: null
});

// Watch show prop to reset form when modal opens
watch(() => props.show, (newVal) => {
    if (newVal) {
        reportForm.start_date = props.filters?.start_date || '';
        reportForm.end_date = props.filters?.end_date || '';
        reportForm.branch_id = props.filters?.branch_id || '';
        reportForm.include_transactions = false;
        reportForm.error = null;
    }
});

const isFormValid = computed(() => {
    if (!reportForm.start_date || !reportForm.end_date) return false;

    const startDate = new Date(reportForm.start_date);
    const endDate = new Date(reportForm.end_date);

    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) return false;

    return endDate >= startDate;
});

const generateReport = async () => {
    if (!isFormValid.value) return;

    reportForm.processing = true;
    reportForm.error = null;

    try {
        const params = new URLSearchParams({
            start_date: reportForm.start_date,
            end_date: reportForm.end_date,
            ...(reportForm.branch_id && { branch_id: reportForm.branch_id }),
            ...(reportForm.include_transactions && { include_transactions: '1' }),
        });

        // Check if report can be generated
        const response = await fetch(route('payment-receipts.report') + '?' + params.toString(), {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.message || data.error || 'Failed to generate report');
        }

        // Trigger download
        window.location.href = route('payment-receipts.report') + '?' + params.toString();

        // Close modal after short delay
        setTimeout(() => {
            reportForm.processing = false;
            emit('close');
        }, 1000);

    } catch (error) {
        reportForm.error = error.message;
        reportForm.processing = false;
    }
};

const handleClose = () => {
    if (!reportForm.processing) {
        reportForm.error = null;
        emit('close');
    }
};
</script>
