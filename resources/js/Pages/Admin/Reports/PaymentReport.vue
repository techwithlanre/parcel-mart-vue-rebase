<script setup>

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import ReportsLayout from "@/Layouts/ReportsLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Helper from "@/Helpers/Helper.js";
import Pagination from "@/Components/Pagination.vue";
import {twMerge} from "tailwind-merge";
import {router, useForm} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";
import VueDatePicker from "@vuepic/vue-datepicker";

const props = defineProps({
  walletBalance: String,
  totalDeposits: String,
  transactions: Array,
  overdraft_wallet_balance: String,
  shipmentCost: Number,
  shipmentRevenue: Number,
  insuranceAmount: Number,
  shipmentProfit: Number,
  depositsCount: Number,
  paymentsCount: Number,
  dateRangeUrl: Array
});

const filterForm = useForm({
  type: ''
})


const shipmentStatusOptions = [
  {
    id: "all",
    name: "all"
  },
  {
    id: "deposit",
    name: "deposit"
  },
  {
    id: "withdraw",
    name: "withdraw"
  }
];

const cardsData = [
  {
    name: "No. of Deposits",
    data: props.depositsCount
  },
  {
    name: "No. of Payments (Shipments)",
    data: props.paymentsCount
  },
  {
    name: "Total Deposits",
    data: Helper.nairaFormat(props.totalDeposits)
  },
  {
    name: "Wallet Balance (Current)",
    data: Helper.nairaFormat(props.walletBalance)
  },
  {
    name: "Revenue From Shipments",
    data: Helper.nairaFormat(props.shipmentRevenue)
  },
  {
    name: "Cost on Shipments",
    data: Helper.nairaFormat(props.shipmentCost)
  },
  {
    name: "Profit On Shipments",
    data: Helper.nairaFormat(props.shipmentProfit)
  },
  {
    name: "Insurance",
    data: Helper.nairaFormat(props.insuranceAmount)
  },
];

let dateRange = ref('');

/*onMounted(() => {
  const startDate = props.dateRangeUrl.from ?? new Date('January 01, 2023');
  const endDate = new Date(new Date().setDate(startDate.getDate()));
  dateRange.value = [startDate, endDate];
})*/

watch(dateRange, (value) => {
  Inertia.get(route('reports.payments'), { from: value[0], to: value[1] }, {
    preserveState: true
  });
});

</script>

<template>
  <DashboardLayout page-title="Payments Dashboard">
    <ReportsLayout>
      <div class="flex flex-col justify-end mb-20 w-max">
        <InputLabel value="Filter Data" />
        <VueDatePicker
            v-model="dateRange"
            :enable-time-picker="false"
            :range="true"
            class="mt-3" />
      </div>

      <div class="flex flex-col w-full gap-5 mt-10">
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

      <div class="rounded-xl shadow bg-white mt-10">
        <div class="flex sm:flex-row flex-col p-5 justify-between items-center">
          <h1 class="text-lg">Payment History</h1>
          <div>
            <InputLabel value="Filter" class="mb-3" />
            <a-select
                :class="twMerge('w-[200px] rounded-lg')"
                ref="select"
                v-model:value="filterForm.type"
                @focus="focus"
                @change="handleFilter">
              <a-select-option v-for="item in shipmentStatusOptions" :value="item.id">{{ item.name }}</a-select-option>
            </a-select>

          </div>
        </div>
        <div class="overflow-x-auto border-x rounded-t-0 rounded-b-xl">
          <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 sdark:bg-gray-700 sdark:text-gray-400">
            <tr class="bg-gray-100">
              <th class="text-left p-4 font-medium">User</th>
              <th class="text-left p-4 font-medium">Amount</th>
              <th class="text-left p-4 font-medium">Wallet Before</th>
              <th class="text-left p-4 font-medium">Wallet After</th>
              <th class="text-left p-4 font-medium">Type</th>
              <th class="text-left p-4 font-medium">Comment</th>
              <th class="text-left p-4 font-medium">Channel</th>
              <th class="text-left p-4 font-medium">Status</th>
              <th class="text-left p-4 font-medium">Time Initiated</th>
              <th class="text-left p-4 font-medium">Time Completed</th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-b hover:bg-gray-50" v-for="item in transactions.data">
              <td class="p-4">
                <div class="font-semibold">{{ item?.user.first_name }} {{ item?.user.last_name }} </div>
                <div class="text-sm">{{ item?.user.email}}</div>
                <div class="text-sm">{{ item?.user.phone }}</div>
              </td>
              <td class="p-4">
                <h1 class="">{{ Helper.nairaFormat(Math.abs(item.amount)) }}</h1>
              </td>
              <td class="p-4">{{ Helper.nairaFormat(item?.before) }}</td>
              <td class="p-4">{{ Helper.nairaFormat(item?.after) }}</td>
              <td class="p-4">{{ item.description }}</td>
              <td class="p-4">{{ item.comment }}</td>
              <td class="p-4 uppercase">{{ item.channel }}</td>
              <td class="p-4 lowercase">
                <span :class="{'bg-blue-100 text-blue-800' : item.status ==='processing',
                                  'bg-orange-100 text-orange-800' : item.status ==='pending', 'bg-green-100 text-green-800' : item.status ==='success'}"
                    class="px-3 rounded-xl py-1">
                  {{ item.status }}
                </span>
              </td>
              <td class="p-4"><date-format short-month :has-time="true" :date="item.time_initiated" /></td>
              <td class="p-4"><date-format short-month :has-time="true" :date="item.time_completed" /></td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <Pagination :links="transactions.links" />
    </ReportsLayout>
  </DashboardLayout>
</template>

<style scoped>

</style>