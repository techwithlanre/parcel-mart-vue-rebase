<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";


defineProps({
    tracking_log: Array
})

</script>

<template>
    <Head title="Tracking Details" />
    <DashboardLayout page-title="Tracking Details">
        <div class="card bg-white p-10 border rounded-xl">
            <ol class="relative border-l border-gray-200 sdark:border-gray-700">
                <li class="mb-10 ml-4" v-for="item in tracking_log">
                    <div class="absolute w-3 h-3 bg-primary rounded-full mt-1.5 -left-1.5 border border-white sdark:border-gray-900 sdark:bg-gray-700"></div>
                    <time class="mb-1 text-sm font-normal leading-none text-primary sdark:text-gray-500">{{ item.update_datetime }} - <span class="font-semibold">{{ item.update_description }}</span></time>
                    <h3 class="text-lg font-semibold text-gray-900 sdark:text-white">{{ item.update_location }}</h3>
                    <p class="mb-4 text-base font-normal text-gray-500 sdark:text-gray-400"></p>
                    <p class="mb-4 text-base font-normal text-gray-500 sdark:text-gray-400">Gross Weight: {{ item.gross_weight }}kg</p>
                    <p class="mb-4 text-base font-normal text-gray-500 sdark:text-gray-400">Chargeable Weight: {{ item.gross_weight }}kg</p>

                </li>
            </ol>
        </div>
    </DashboardLayout>
</template>

<style scoped>

</style>
