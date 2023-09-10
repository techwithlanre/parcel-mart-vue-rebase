<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: true,
    },

    options: {
        type: Object
    },

    placeHolder: {
        type: String,
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);
</script>

<template>
    <select
        class="bg-transparent border-gray-300 rounded-md w-full outline-none focus:border-primary focus:ring-2 focus:ring-background"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        ref="select">
        <option value="0" disabled selected>{{ placeHolder ?? 'Select'}}</option>
        <option :value="item.id" v-for="item in options">{{ item.name }}</option>
    </select>
</template>
