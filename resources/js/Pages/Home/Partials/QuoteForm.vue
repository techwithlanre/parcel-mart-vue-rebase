<script setup>
import { ref } from 'vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import {Link} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import QuoteTabs from "@/Pages/Home/Partials/QuoteTabs.vue";
import greenTruck from "/resources/images/green-truck.jpg";

defineProps({
    countries: Array
})

</script>

<template>
    <div class="flex flex-col lg:flex-row gap-x-20 p-6 lg:px-[250px] py-20">
        <div class="lg:w-1/2 w-full">
            <h1 class="text-left lg:text-5xl text-3xl font-bold ">Find out the price right now</h1>
            <p class="text-left lg:text-2xl text-xl mb-10">Use our quote form to calculate cost of shipping</p>
            <div class="mt-5 h-52 hidden lg:block rounded-xl bg-center bg-cover bg-[url('/resources/images/green-truck.jpg')]">

            </div>
        </div>
        <div class="lg:w-1/2 w-full flex gap-x-20">
            <div class="w-full px-2 sm:px-0">
                <QuoteTabs :countries="countries" />
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
