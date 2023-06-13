<script setup>

import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronUpIcon } from '@heroicons/vue/20/solid'

import Quote from "../../../../images/icons/quote.svg";
import Accordion from "@/Components/Accordion.vue";

const faqs = [
    {
        title: "How can I track my parcel using the web and mobile application?",
        body: "To track your parcel, simply open the web or mobile application and enter the unique tracking number provided to you during the shipment process. The application will display the real-time status and location of your parcel",
    },
    {
        title: "What are the available payment options for parcel delivery?",
        body: "Our web and mobile application support multiple payment options, including credit/debit cards, online banking, and digital wallets such as PayPal and Apple Pay. You can choose the most convenient payment method during the checkout process.",
    },
    {
        title: "Can I schedule a specific delivery time for my parcel?",
        body: "Yes, our application allows you to schedule a preferred delivery time for your parcel. During the checkout process, you will be provided with available time slots based on the courier's schedule. Simply select the desired time slot, and our system will ensure that your parcel is delivered accordingly.",
    },
    {
        title: "What should I do if my parcel is damaged or lost during transit?",
        body: "In the unfortunate event of parcel damage or loss, please contact our customer support immediately through the web or mobile application. Our dedicated team will assist you in filing a claim and initiating an investigation with the courier. We will work to resolve the issue and provide you with a suitable solution.",
    },
    {
        title: "Are there any restrictions on the size and weight of parcels that can be sent through the application?",
        body: "Our application supports a wide range of parcel sizes and weights, but there are certain restrictions imposed by the courier. During the booking process, the application will notify you if your parcel exceeds the maximum size or weight allowed. In such cases, we recommend contacting our customer support for alternative options or arranging a specialized shipment if required.",
    },
]
</script>

<template>
    <div class="flex flex-col p-6 lg:px-[250px] ">
        <h1 class="text-3xl lg:text-5xl font-bold text-center">Frequently <br>Asked Questions</h1>
        <p class="text-center text-sm mb-10">Don't just take our words, hear from our customers</p>
        <div class="flex gap-x-20">
            <div class="flex flex-col bg-white w-full">
                <div class="w-full">
                    <div class="mx-auto w-max-md w-full rounded-2xl bg-white p-2 flex flex-col space-y-6">
                        <Accordion v-for="item in faqs" :title="item.title" :body="item.body" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
