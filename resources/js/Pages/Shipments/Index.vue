<script setup>

import {ref} from "vue";
import {Link, Head, useForm, usePage} from "@inertiajs/vue3"
import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import parcel from "../../../images/parcel.png";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {twMerge} from "tailwind-merge";
import Modal from "@/Components/Modal.vue";
import TrackShipmentModal from "@/Pages/Shipments/Partials/TrackShipmentModal.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const activeKey = ref('1');
const trackShipmentOpen = ref(false);
const tabs = [
    { name: "Parcels", icon: WalletIcon, route: "profile" },
    { name: "Documents", icon: DocumentIcon, route: "settlement-details" }
];

defineProps({
    log: Array,
    shipments: Array,
    shipmentsCount: String
})


const filterForm = useForm({
    status: ''
})


const shipmentStatusOptions = [
    {id: "all", name: "all"},
    {id: "pending", name: "pending"},
    {id: "processing", name: "processing"},
    {id: "delivered", name: "delivered"},
    {id: "cancelled",name: "cancelled"}
];

const page = usePage();

const handleFilter = () => {
    axios.get(route('shipment.filter'), { params: { status: filterForm.status }}).then((response) => {
        page.props.log = response.data
    })
}
</script>

<template>
    <DashboardLayout page-title="Shipments">
        <Head title="Start Shipment" />
        <div class="flex flex-col w-full gap-5 mt-10">
            <div v-if="log.length > 0 || parseInt(shipmentsCount) > 0" class="flex flex-row gap-5 justify-end items-end">
                <PrimaryButton @click="trackShipmentOpen = true" class="w-max text-xs bg-white text-red-900 border-2 border-background" style="color: #008083; border: 1px solid #d6e9ed" type="button">
                    Track Shipment
                </PrimaryButton>
                <Link :href="route('shipment.origin')">
                    <PrimaryButton class="w-max text-xs">New Shipment</PrimaryButton>
                </Link>
            </div>
            <Modal :show="trackShipmentOpen">
              <TrackShipmentModal @close-modal="(value) => trackShipmentOpen = value " />
            </Modal>
            <div class="p-10 w-full bg-white shadow rounded-2xl mt-10" v-if="log.length > 0 || parseInt(shipmentsCount) > 0" >
                <div class="flex lg:flex-row flex-col justify-between mb-10 items-start">
                    <h1 class="text-lg">Shipment History</h1>
                    <div>
                        <InputLabel value="Filter" class="mb-3" />
                        <SelectInput
                            :class="twMerge('w-[200px] rounded-lg')"
                            ref="select"
                            v-model="filterForm.status"
                            @focus="focus"
                            @change="handleFilter"
                            :options="shipmentStatusOptions">
                        </SelectInput>

                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
                      <thead class="text-xs text-gray-700 uppercase bg-gray-50 sdark:bg-gray-700 sdark:text-gray-400">
                        <tr>
                            <th class="text-left p-6 font-medium">Origin</th>
                            <th class="text-left p-6 font-medium">Destination</th>
                            <th class="text-left p-6 font-medium">Tracking Number</th>
                            <th class="text-left p-6 font-medium">Status</th>
                            <th class="text-left p-6 font-medium">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="log.length > 0" v-for="item in log" class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ item.origin['name']}}</div>
                                <span class="text-gray-600">{{ item.origin['phone'] }}</span>
                                <div>{{ item.origin['address_1']}}</div>
                                <div>{{item.origin['city']}}, {{ item.origin['country']}}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ item.destination['name']}}</div>
                                <span class="text-gray-600">{{ item.destination['phone'] }}</span>
                                  <div>{{ item.destination['address_1']}}</div>
                                <div>{{item.destination['city']}}, {{ item.destination['country']}}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ item.number }}
                            </td>
                            <td class="px-6 py-4">
                              <span
                                  :class="{'bg-blue-100 text-blue-800' : item.status ==='processing',
                                  'bg-orange-100 text-orange-800' : item.status ==='pending', 'bg-green-100 text-green-800' : item.status ==='delivered'}"
                                  class="px-3 rounded-xl py-1">{{ item.status}}</span>

                            </td>
                            <td class="px-6 py-4">
                                <Link :href="route('shipment.origin', item.id)" v-if="item.status === 'pending'" class="text-primary font-medium hover:text-green-600">Checkout</Link>
                                <Link :href="route('shipment.details', item.id)" v-else class="btn btn-sm rounded-xl bg-green-400 text-white px-5 py-1 text-sm font-medium hover:text-green-600">View</Link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="shipments.links" />
            </div>
            <div v-else class="flex flex-row justify-center mt-20">
                <div class="flex flex-col items-center">
                    <img :src="parcel" alt="" class="h-52">
                    <h1 class="mt-5 text-center">You have not shipped any package with us! Click the button below to start your shipment</h1>
                    <Link :href="route('shipment.origin')" class="mt-5"><PrimaryButton>Start Shipment</PrimaryButton></Link>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>

</style>
