<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="text-orange-400 text-sm mt-1.5 transition delay-150 duration-500 ease-in-out">
            {{ message }}
        </p>
    </div>
</template>
