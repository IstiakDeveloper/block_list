<script setup>
import { onMounted, ref } from 'vue';

const input = ref(null);

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  min: {
    type: [String, Number],
    default: null
  },
  max: {
    type: [String, Number],
    default: null
  },
  required: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: [String, Boolean],
    default: false
  }
});

const emit = defineEmits(['update:modelValue']);

onMounted(() => {
  if (input.value?.hasAttribute('autofocus')) {
    input.value?.focus();
  }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
  <input
    :id="$attrs.id"
    ref="input"
    :value="modelValue"
    :type="type"
    :min="min"
    :max="max"
    :required="required"
    :placeholder="placeholder"
    :disabled="disabled"
    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
    :class="{
      'border-red-500 focus:border-red-500 focus:ring-red-500': error,
      'bg-gray-100': disabled
    }"
    @input="$emit('update:modelValue', $event.target.value)"
    v-bind="$attrs"
  >
  <div v-if="error && typeof error === 'string'" class="text-red-500 text-sm mt-1">
    {{ error }}
  </div>
</template>
