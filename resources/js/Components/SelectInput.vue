<script setup>
import { ref } from 'vue';

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
        class="bg-transparent border-gray-300 rounded-md w-full h-10 placeholder-gray-500 outline-none focus:border-primary focus:ring-2 focus:ring-background transition ease-in-out duration-500"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        ref="select">
        <option value="0" disabled selected>{{ placeHolder ?? 'Select'}}</option>
        <option :value="item.id" v-for="item in options">{{ item.name }}</option>
    </select>
</template>
