<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block font-bold text-sm text-text-color">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
