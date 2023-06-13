<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import NewShipmentModal from "@/Components/Modals/NewShipmentModal.vue";
import {ref} from "vue";
import { twMerge } from "tailwind-merge";

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex justify-end mb-10">
            <Link :href="route('shipments.index')" class="duration-300" :class="twMerge('text-white bg-primary px-6 py-2 rounded-lg hover:text-gray-200 hover:bg-primary-dark')">NEW SHIPMENT</Link>
        </div>
        <div class="flex flex-col gap-y-10 lg:flex-row gap-x-10">
            <div class="p-5 bg-white rounded-2xl w-full border">
                <div class="flex gap-5 items-center">
                    <div class="p-5 h-12 w-12 rounded-full border"></div>
                    <div class="flex flex-col">
                        <h1 class="font-medium text-gray-600">Today</h1>
                        <h3 class="text-2xl text-gray-400">24</h3>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white rounded-2xl w-full border">
                <div class="flex gap-5">
                    <div class="p-5 h-12 w-12 rounded-full border"></div>
                    <div class="flex flex-col">
                        <h1 class="font-medium">Collection</h1>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white rounded-2xl w-full border">
                <div class="flex gap-5">
                    <div class="p-5 h-12 w-12 rounded-full border"></div>
                    <div class="flex flex-col">
                        <h1 class="font-medium">In Transit</h1>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white rounded-2xl w-full border">
                <div class="flex gap-5">
                    <div class="p-5 h-12 w-12 rounded-full border"></div>
                    <div class="flex flex-col">
                        <h1 class="font-medium">Exceptions</h1>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white rounded-2xl w-full border">
                <div class="flex justify-between">
                    <div class="flex gap-5 items-start">
                        <div class="p-5 h-12 w-12 rounded-full border"></div>
                        <div class="flex flex-col">
                            <h1 class="font-medium">Wallet Balance</h1>
                            <h3 class="text-2xl font-bold">25,000</h3>
                        </div>
                    </div>
                    <div class="text-3xl">...</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
