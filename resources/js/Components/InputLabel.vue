<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block font-normal text-sm text-text-color">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
