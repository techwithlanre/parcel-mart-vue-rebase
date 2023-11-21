<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="w-full items-center px-4 py-1.5 bg-white border border-primary rounded-md font-semibold text-sm text-primary uppercase tracking-widest hover:bg-primary-accent focus:bg-primary-accent active:bg-primary-accent focus:outline-none transition ease-in-out duration-30"
    >
        <slot/>
    </button>
</template>
