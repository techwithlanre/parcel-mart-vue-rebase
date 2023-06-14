<script setup>

import {Head} from "@inertiajs/vue3";
import FrontNavigation from "@/Components/FrontNavigation/FrontNavigation.vue";
import Footer from "@/Pages/Home/Partials/Footer.vue";

defineProps({
    head: String
})

</script>

<template>
    <Head :title="head" />
    <div class="bg-white overflow-x-hidden">

        <div class="bg-white overflow-x-hidden">
            <div class="p-6 xl:px-[150px] lg:px-[100px]">
                <FrontNavigation />
            </div>
        </div>
        <slot />
        <Footer />
    </div>
</template>

<style scoped>

</style>
