<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: true,
    },

    placeholder: {
        type: String,
        required: false
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <textarea
        class="bg-transparent border-gray-200 rounded-md w-full placeholder-gray-300 outline-none focus:border-dashed focus:border-primary focus:ring-1 focus:ring-primary focus:ring-offset-2"
        :value="modelValue"
        :placeholder="placeholder"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input" />
</template>
