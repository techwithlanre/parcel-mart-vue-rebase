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
    <input
        class="bg-transparent border-gray-300 rounded-md w-full placeholder-gray-500 outline-none focus:border-primary focus:ring-2 focus:ring-background transition ease-in-out duration-500"
        :value="modelValue"
        :placeholder="placeholder"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
