<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";
import {Link, Head, useForm} from "@inertiajs/vue3"
import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import parcel from "../../../../images/parcel.png";

const activeKey = ref('1');
const tabs = [
    { name: "Parcels", icon: WalletIcon, route: "profile" },
    { name: "Documents", icon: DocumentIcon, route: "settlement-details" }
];

defineProps({
    log: Array,
    shipments: Array
})

const trackForm = useForm({
    number: ''
})


const trackShipment = () => {
    trackForm.post(route('shipment.track'), {
        onFinish: () => trackForm.reset(),
    })
}

</script>

<template>
    <AuthenticatedLayout page-title="Shipments">
        <Head title="Start Shipment" />
        <div class="flex flex-col w-full gap-5">
            <div class="p-10 w-full bg-white border rounded-2xl" v-if="log.length > 0" >
                <h1 class="text-lg">Shipment History</h1>
                <div>
                    <!-- component -->
                    <div class="bg-white">
                        <div class="overflow-x-auto border-x border-t rounded-xl">
                            <table class="table-auto w-full ">
                                <thead class="border-b">
                                <tr class="bg-gray-100">
                                    <th class="text-left p-4 font-medium">Number</th>
                                    <th class="text-left p-4 font-medium">Origin</th>
                                    <th class="text-left p-4 font-medium">Destination</th>
                                    <th class="text-left p-4 font-medium">Status</th>
                                    <th class="text-left p-4 font-medium">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="border-b hover:bg-gray-50" v-for="item in log">
                                    <td class="p-4">
                                        <h1 class="text-md font-bold">{{ item.number }}</h1>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold">{{ item.origin['name']}}</div>
                                        <span class="text-gray-600">{{ item.origin['phone'] }}</span>
                                        <div>{{ item.origin['address_1']}}</div>
                                        <div>{{item.origin['city']}}, {{ item.origin['country']}}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold">{{ item.destination['name']}}</div>
                                        <span class="text-gray-600">{{ item.destination['phone'] }}</span>
                                        <div>{{ item.destination['address_1']}}</div>
                                        <div>{{item.destination['city']}}, {{ item.destination['country']}}</div>
                                    </td>
                                    <td class="p-4">{{ item.status}}</td>
                                    <td class="p-4">
                                        <Link :href="route('shipment.details', item.id)" v-if="item.status !== 'pending'" class="text-primary font-medium hover:text-green-600">View</Link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="shipments.links" />
                    </div>
                </div>
            </div>
            <div v-else class="flex flex-row justify-center mt-20">
                <div class="flex flex-col items-center">
                    <img :src="parcel" alt="" class="h-52">
                    <h1 class="mt-5 text-center">You have not shipped any package with us! Click the button below to start your shipment</h1>
                    <Link :href="route('shipment.start')" class="mt-5"><PrimaryButton>Start Shipment</PrimaryButton></Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
