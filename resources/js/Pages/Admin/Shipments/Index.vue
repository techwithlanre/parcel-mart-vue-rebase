<script setup>

import TextInput from "@/Components/TextInput.vue";
import {onMounted, ref, watch} from "vue";
import {Link} from "@inertiajs/vue3"
import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import parcel from "../../../../images/parcel.png";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Helper from "../../../Helpers/Helper.js";
import SelectInput from "@/Components/SelectInput.vue";

const activeKey = ref('1');
const tabs = [
    { name: "Parcels", icon: WalletIcon, route: "profile" },
    { name: "Documents", icon: DocumentIcon, route: "settlement-details" }
];

const shipmentStatusOptions = [
  {id: "all", name: "all"},
  {id: "pending", name: "pending"},
  {id: "processing", name: "processing"},
  {id: "delivered", name: "delivered"},
  {id: "cancelled",name: "cancelled"}
];

let props = defineProps({
    log: Array,
    shipments: Array
})

let shipmentLog = ref([]);
let trackingNumber = ref('');
let status = ref('0')

onMounted(() => {
  shipmentLog.value = props.log;
});

watch(trackingNumber, (value) => {
  axios.get(route('admin.shipment.filter'), { params: { number: value } }).then((response) => {
    shipmentLog.value = response.data
  })
});


watch(status, (value) => {
  axios.get(route('admin.shipment.filter'), { params: { status: value } }).then((response) => {
    shipmentLog.value = response.data
  })
});

</script>

<template>
    <DashboardLayout page-title="Shipments">
        <div class="flex flex-col w-full gap-5">
            <div class="font-semibold ">
              Filter Shipments
            </div>
            <div class="grid sm:grid-cols-4 grid-col-1 items-center gap-x-5">
              <div class="mb-2">
                <TextInput
                    type="text"
                    v-model="trackingNumber"
                    placeholder="Enter tracking number"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg"
                />
              </div>
              <div class="mb-2">
                <SelectInput
                    v-model="status"
                    :options="shipmentStatusOptions"
                    place-holder="Select status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg"
                />
              </div>
            </div>
            <div class="p-10 w-full bg-white border rounded-2xl" v-if="log.length > 0" >
                <h1 class="text-lg">Shipment History</h1>
                <div>
                    <!-- component -->
                    <div class="bg-white">
                        <div class="overflow-x-auto border-x border-t rounded-xl">
                          <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 sdark:bg-gray-700 sdark:text-gray-400">
                                <tr class="bg-gray-100">
                                    <th class="text-left p-4 font-medium">Origin</th>
                                    <th class="text-left p-4 font-medium">Destination</th>
                                    <th class="text-left p-4 font-medium">Provider</th>
                                    <th class="text-left p-4 font-medium">Tracking Number</th>
                                    <th class="text-left p-4 font-medium">Provider Cost</th>
                                    <th class="text-left p-4 font-medium">Amount Paid</th>
                                    <th class="text-left p-4 font-medium">Status</th>
                                    <th class="text-left p-4 font-medium">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700 hover:bg-gray-50 sdark:hover:bg-gray-600" v-for="item in shipmentLog">
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
                                  <td class="p-4">{{ item.rate?.provider_code}}</td>
                                  <td class="p-4">
                                    <h1 class="text-md font-bold">{{ item.number }}</h1>
                                  </td>
                                  <td class="p-4">{{ Helper.nairaFormat(item.rate?.provider_total_amount ?? 0) }}</td>
                                  <td class="p-4">{{ Helper.nairaFormat(item.rate?.total_charge ?? 0) }}</td>
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
    </DashboardLayout>
</template>

<style scoped>

</style>
