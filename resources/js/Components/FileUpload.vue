<template>
    <div class="space-y-1">
        <label
            v-if="label"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>
        <input
            type="file"
            ref="fileInput"
            @change="handleFileChange"
            :class="[
                'block w-full text-sm border rounded-md py-2 px-3',
                error || validationError
                    ? 'border-red-500 focus:ring-red-500'
                    : 'border-gray-300 dark:border-gray-600'
            ]"
            accept="image/*"
        />
        <div v-if="previewUrl" class="mt-2">
            <img
                :src="previewUrl"
                alt="File Preview"
                class="h-24 w-24 object-cover rounded-md"
            />
        </div>
        <p v-if="validationError" class="text-sm text-red-600 mt-1">
            {{ validationError }}
        </p>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB

const props = defineProps({
    label: {
        type: String,
        default: ''
    },
    file: {
        type: [File, null],
        default: null
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:file']);
const previewUrl = ref(null);
const validationError = ref('');
const fileInput = ref(null);

function handleFileChange(event) {
    const file = event.target.files[0];
    validationError.value = '';
    previewUrl.value = null;

    if (file) {
        // Validate file size
        if (file.size > MAX_FILE_SIZE) {
            validationError.value = 'ফাইলের আকার ২ মেগাবাইটের বেশি হতে পারবে না।';
            fileInput.value.value = ''; // Clear the file input
            emit('update:file', null);
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            validationError.value = 'শুধুমাত্র JPEG, PNG, বা GIF ফাইল আপলোড করতে পারবেন।';
            fileInput.value.value = ''; // Clear the file input
            emit('update:file', null);
            return;
        }

        // Create preview and emit file
        const reader = new FileReader();
        reader.onload = (e) => {
            previewUrl.value = e.target.result;
        };
        reader.readAsDataURL(file);

        emit('update:file', file);
    }
}

// Reset when external changes occur
watch(() => props.file, (newFile) => {
    if (!newFile) {
        previewUrl.value = null;
        validationError.value = '';
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
});
</script>
