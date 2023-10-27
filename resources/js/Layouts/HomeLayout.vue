<script setup>

import {Head} from "@inertiajs/vue3";
import FrontNavigation from "@/Components/FrontNavigation/FrontNavigation.vue";
import Footer from "@/Pages/Home/Partials/Footer.vue";

defineProps({
    head: String,
    showFooter: Boolean
})

</script>

<template>
    <Head :title="head" />
    <div class="bg-white overflow-x-hidden">

        <div class="bg-white overflow-x-hidden">
            <div class="p-6 w-full">
<!--                <FrontNavigation />-->
            </div>
        </div>
        <slot />
        <Footer :show="showFooter" />
    </div>
</template>

<style scoped>

</style>
