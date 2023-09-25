<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Helper from "@/Helpers/Helper.js";
import ReportsLayout from "@/Layouts/ReportsLayout.vue";
import {onMounted, ref, watch} from "vue";
import {Link, router, useForm} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import {WalletIcon} from "@heroicons/vue/20/solid/index.js";
import Loading from "@/Components/Loading.vue";
import Pagination from "@/Components/Pagination.vue";
import {Inertia} from "@inertiajs/inertia";
import VueDatePicker from "@vuepic/vue-datepicker";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
  totalShipmentCount: Number,
  processingShipmentsCount: Number,
  pendingShipmentsCount: Number,
  deliveredShipmentsCount: Number,
  shipmentCost: Number,
  shipmentCharge: Number,
  insuranceAmount: Number,
  log: Array,
  shipments: Array,
  dateRangeUrl: Array
});

let trackingNumber = ref('');
let status = ref('0');

let dateRange = ref('');

watch(dateRange, (value) => {
  Inertia.get(route('reports.shipment'), { from: value[0], to: value[1] }, {
    preserveState: true
  });
});

watch(trackingNumber, (value) => {
  router.get(route('reports.shipment'), {
    number: value
  }, {
    preserveState: true
  })
});


watch(status, (value) => {
  router.get(route('reports.shipment'), {
    status: value
  }, {
    preserveState: true
  })
});

const shipmentStatusOptions = [
  {id: "all", name: "all"},
  {id: "pending", name: "pending"},
  {id: "processing", name: "processing"},
  {id: "delivered", name: "delivered"},
  {id: "cancelled",name: "cancelled"}
];

const exportCsv = () => {
  axios.get(route('admin.shipment.filter')).then((response) => {
    let newData = [];
    response.data.map((data) => {
      newData = [
        {
          'Tracking Number': data.number,
          'origin': "Name: " + data.origin['name'] + " \n Address: " + data.origin['address_1'],
        }
      ];
      console.log(newData);
    });
    Helper.exportDataFromJSON(newData, 'Shipment Report', 'xls')
  });
}

const cardsData = [
  {name: "Shipments", data: props.totalShipmentCount},
  {name: "Processing Shipments", data: props.processingShipmentsCount},
  {name: "Pending Shipments", data: props.pendingShipmentsCount},
  {name: "Delivered Shipments", data: props.deliveredShipmentsCount},
  {name: "Total Shipment Charge", data: Helper.nairaFormat(props.shipmentCharge)},
  {name: "Total Shipment Cost", data: Helper.nairaFormat(props.shipmentCost)},
  {name: "Profit On Shipments", data: Helper.nairaFormat(props.shipmentCharge - props.shipmentCost)},
  {name: "Total Insurance", data: Helper.nairaFormat(props.insuranceAmount)}
]
</script>
<template>
  <DashboardLayout page-title="Shipments Report">
    <ReportsLayout>
      <div class="flex flex-col justify-end mb-20 w-max">
        <InputLabel value="Filter Data" />
        <VueDatePicker
            v-model="dateRange"
            :enable-time-picker="false"
            :range="true"
            class="mt-3" />
      </div>
      <div class="flex flex-col w-full gap-5">
        <div class="grid sm:grid-cols-4 grid-cols-1 justify-between gap-5 w-full">
          <div v-for="item in cardsData" :key="item.name" class=" flex flex-row justify-between p-5 shadow shadow-background rounded-xl w-full">
            <div>
              <h3 class="text-sm">{{ item.name }}</h3>
              <div class="mt-5 text-xl">{{ item.data }}</div>
            </div>
            <div class="p-2 bg-primary/10 h-max rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
              </svg>
            </div>
          </div>
        </div>
      </div>
      <div class="w-full bg-white rounded-2xl mt-20 shadow">
        <div class="flex sm:flex-row flex-col-reverse gap-y-10 justify-between items-center p-10 ">
          <div class="flex-1">
            <div class="font-semibold ">
              Filter Shipments
            </div>
            <div class="grid sm:grid-cols-4 grid-col-1 items-center gap-x-5 mt-3">
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
          </div>
          <div>
            <button @click="exportCsv()" id="hs-as-table-table-export-dropdown" type="button" class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border font-semibold bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-slate-900 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800">
              <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
              </svg>
              Export
            </button>
          </div>
        </div>
        <div>
          <div class="bg-white">
            <div class="overflow-x-auto rounded-b-xl">
              <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                  <tr class="bg-gray-100">
                    <th class="text-left p-4 font-bold">User</th>
                    <th class="text-left p-4 font-bold">Origin</th>
                    <th class="text-left p-4 font-bold">Destination</th>
                    <th class="text-left p-4 font-bold">Package</th>
                    <th class="text-left p-4 font-bold">Tracking Number</th>
                    <th class="text-left p-4 font-bold">Provider Cost</th>
                    <th class="text-left p-4 font-bold">Amount Paid</th>
                    <th class="text-left p-4 font-bold">Profit</th>
                    <th class="text-left p-4 font-bold">Status</th>
                    <th class="text-left p-4 font-bold">Action</th>
                  </tr>
                </thead>

                <tbody v-if="fetchingShipments">
                  <tr>
                    <td colspan="8" class="text-center p-5">
                      <Loading />
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                <tr v-if="log.length > 0"  class="bg-white border-t hover:bg-gray-50" v-for="item in log">
                  <td class="p-4 font-semibold">
                    <div class="">{{ item.shipment.user.first_name }} {{ item.shipment.user.last_name }} </div>
                    <div class="">{{ item.shipment.user.email}}</div>
                    <div class="">{{ item.shipment.user.phone }}</div>
                  </td>
                  <td class="p-4">
                    <div>{{ item.origin?.name}}</div>
                    <div class="text-red-700">{{ item.origin?.phone}}</div>
                    <div>{{ item.origin?.country}}</div>
                    <div>{{ item.origin?.city }} {{ item.origin?.state}}</div>
                  </td>
                  <td class="p-4">
                    <div>{{ item.destination?.name}}</div>
                    <div>{{ item.destination?.phone}}</div>
                    <div>{{ item.destination?.country}}</div>
                    <div>{{item.destination?.city}} {{ item.destination?.state }}</div>
                  </td>
                  <td class="p-4">{{ item.shipment?.shipping_rate_log?.product_name}}</td>
                  <td class="p-4">
                    <h1 class="text-md text-primary">{{ item.shipment?.number }}</h1>
                  </td>
                  <td class="p-4">{{ Helper.nairaFormat(item.shipment?.shipping_rate_log?.provider_total_amount ?? 0) }}</td>
                  <td class="p-4">{{ Helper.nairaFormat(item.shipment?.shipping_rate_log?.total_charge ?? 0) ?? '' }}</td>
                  <td class="p-4">{{ Helper.nairaFormat(item.shipment?.shipping_rate_log?.total_charge - item.shipment?.shipping_rate_log?.provider_total_amount) }}</td>
                  <td class="p-4">
                    <span
                      :class="{'bg-blue-100 text-blue-800' : item.shipment.status ==='processing',
                                  'bg-orange-100 text-orange-800' : item.shipment.status ==='pending', 'bg-green-100 text-green-800' : item.shipment.status ==='delivered'}"
                      class="px-3 rounded-xl py-1">{{ item.shipment.status}}</span></td>
                  <td class="p-4">
                    <Link :href="route('shipment.details', item.shipment.id)" v-if="item.shipment.status !== 'pending'" class="btn btn-sm rounded-xl bg-green-400 text-white px-5 py-1 text-sm font-medium hover:text-green-600">View</Link>
                  </td>
                </tr>
                <tr v-else>
                  <td colspan="8" class="text-center p-5">
                    No results found
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <Pagination :links="shipments.links" />
    </ReportsLayout>
  </DashboardLayout>
</template>

<style scoped>
.text-2xl {
  @apply font-semibold text-gray-500
}
</style>