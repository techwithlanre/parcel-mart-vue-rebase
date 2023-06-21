<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { twMerge } from "tailwind-merge";
import parcel from '/resources/images/parcel.png'
import shipment from '/resources/images/shipment.png'
import wallet from '/resources/images/wallet.png'
import { CaretUpFilled, CaretDownOutlined } from '@ant-design/icons-vue';
import {Dialog, DialogDescription, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from "@headlessui/vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {ref} from "vue";
import DashboardQuoteModal from "@/Components/Modals/DashboardQuoteModal.vue";

defineProps({
    balance: String
})
const isOpen = ref(false)
const isQuoteOpen = ref(false)

function toggleQuote(value) {
    isQuoteOpen.value = value
}

function closeQuote() {
    isQuoteOpen.value = false
}

function closeModal() {
    isOpen.value = false
}
function openModal() {
    isOpen.value = true
}



</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout page-title="Dashboard">
        <div class="flex lg:flex-row flex-col gap-x-5 gap-y-10">
            <div class="lg:w-3/4 w-full">
                <div class="flex flex-col gap-y-10 lg:flex-row gap-x-10">
                    <div class="p-5 bg-white rounded-2xl w-full border">
                        <div class="flex gap-5 items-center">
                            <div class="h-12 w-12">
                                <img :src="shipment" alt="">
                            </div>
                            <div class="flex flex-col">
                                <h1 class="font-bold">Shipments</h1>
                                <h3 class="text-2xl font-bold">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 bg-white rounded-2xl w-full border">
                        <div class="flex justify-between">
                            <div class="flex gap-5 items-start">
                                <div class="h-12 w-12">
                                    <img :src="wallet" alt="">
                                </div>
                                <div class="flex flex-col">
                                    <h1 class="font-bold">Wallet Balance</h1>
                                    <h3 class="text-2xl font-bold">{{ balance }}</h3>
                                </div>
                            </div>
                            <div class="text-3xl"></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-y-10 lg:flex-row gap-x-10 mt-10">
                    <div class="p-5 bg-white rounded-2xl w-full border">
                        <a href="javascript:void(0)" class="flex flex-col" @click="openModal">
                            <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                <img :src="parcel" alt="" class="h-10 w-10">
                            </div>

                            <div class="flex flex-row justify-between items-center">
                                <div class="flex flex-col">
                                    <h1 class="font-medium text-md text-gray-600 mt-5">Book Shipments</h1>
                                    <h3 class="text-xs text-gray-400">Send and receive item(s)</h3>
                                </div>
                                <div class="text-primary">icon here</div>
                            </div>
                        </a>
                    </div>
                    <div class="p-5 bg-white rounded-2xl w-full border">
                        <a href="javascript:void(0)" class="flex flex-col" @click="toggleQuote(true)">
                            <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                <img :src="parcel" alt="" class="h-10 w-10">
                            </div>

                            <div class="flex flex-row justify-between items-center">
                                <div class="flex flex-col">
                                    <h1 class="font-medium text-md text-gray-600 mt-5">Get Pricing</h1>
                                    <h3 class="text-xs text-gray-400">Request a quote</h3>
                                </div>
                                <div class="text-primary">icon here</div>
                            </div>
                        </a>
                    </div>

                    <Link class="p-5 bg-white rounded-2xl w-full border">
                        <div class="flex gap-5 items-center">
                            <div class="flex flex-col">
                                <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                    <img :src="parcel" alt="" class="h-10 w-10">
                                </div>

                                <h1 class="font-medium text-md text-gray-600 mt-5">Tracking</h1>
                                <h3 class="text-xs text-gray-400">Track your shipments</h3>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
            <div class="lg:w-1/4 w-full">
                <div class="bg-white border rounded-xl w-full">
                    <div class="p-5 font-bold">Latest Shipments (5)</div>
                    <hr>
                    <div class="p-5 w-full">
                        <div v-for="i in 5" class="flex items-center gap-x-3 w-full mb-10">
                            <div class="w-8 h-8 flex justify-center items-center rounded-full bg-green-600 text-white font-bold">
                                <caret-down-outlined/>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex justify-between items-center gap-x-30 w-full">
                                    <h3 class="font-bold">128173928293{{ i }}</h3>
                                    <div class="bg-yellow-500 text-yellow-900 px-4 py-1 text-xs rounded-full">in transit</div>
                                </div>
                                <div class="text-sm">Your shipment will arrive in 7 - 9 days</div>
                            </div>
                        </div>
                        <p>
                            <Link class="text-primary font-semibold">View all</Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <TransitionRoot appear :show="isOpen" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-10">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black bg-opacity-25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white pt-6 text-left align-middle shadow-xl transition-all"
                            >
                                <form @submit.prevent="submit">
<!--                                    <DialogTitle as="h3" class="text-lg font-medium leading-6 px-6 text-gray-900">
                                        <div class="p-3 bg-yellow-100 text-yellow-500 text-sm">
                                            xddd
                                        </div>
                                    </DialogTitle>-->

                                    <div class="px-6 mb-5 flex flex-col gap-y-3">
                                        <Link class="border p-3 rounded-lg">
                                            <h3 class="font-semibold">Book a delivery</h3>
                                            <div class="text-primary">Send out a parcel, locally or internationally</div>
                                        </Link>
                                        <Link class="border p-3 rounded-lg">
                                            <h3 class="font-semibold">Book an import</h3>
                                            <div class="text-primary">Receive a package from anywhere in the world</div>
                                        </Link>
                                    </div>
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
        <DashboardQuoteModal :is-open="isQuoteOpen" @closeQuoteModal="(value) => toggleQuote(value)" />
    </AuthenticatedLayout>
</template>
