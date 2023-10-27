<script setup>

import ReportsLayout from "@/Layouts/ReportsLayout.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Helper from "@/Helpers/Helper.js";
import {Link, usePage} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {onMounted, ref} from "vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
  totalUsersCount: Number,
  businessUsersCount: Number,
  individualUsersCount: Number,
  users: Array
});

const page = usePage();

const userPermissions = ref([]);

const cardsData = [
  {name: "Total Users", data: props.totalUsersCount},
  {name: "Individual Users", data: props.individualUsersCount},
  {name: "Business Users", data: props.businessUsersCount},
];

onMounted(() => {
  userPermissions.value = page.props.auth.permissions;
})

const checkPermission = (permission) => {
  return userPermissions.value.includes(permission);
}
</script>

<template>
  <DashboardLayout page-title="Shipments Report">
    <ReportsLayout>
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
      <div class="card bg-white">
        <div class="overflow-x-auto shadow-background shadow mt-10 rounded-xl">
          <!-- Header -->
          <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200">
            <div>
              <h2 class="text-xl font-semibold text-gray-800">
                Users
              </h2>
            </div>
          </div>
          <!-- End Header -->
          <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr class="bg-gray-100">
              <th class="text-left p-4 font-medium">Name</th>
              <th class="text-left p-4 font-medium">Email</th>
              <th class="text-left p-4 font-medium">Phone</th>
              <th class="text-left p-4 font-medium">User Type</th>
              <th class="text-left p-4 font-medium">Role</th>
              <th class="text-left p-4 font-medium">Credit Limit</th>
              <th class="text-left p-4 font-medium">Wallet</th>
              <th class="text-left p-4 font-medium">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-b hover:bg-gray-50" v-for="item in users.data">
              <td class="p-4">
                <h1 class="text-md font-bold">{{ item.first_name }} {{ item.last_name }}</h1>
              </td>
              <td class="p-4">{{ item.email }}</td>
              <td class="p-4">{{ item.phone }}</td>
              <td class="p-4 uppercase">
                <div>{{ item.user_type }}<span v-if="item.user_type === 'business'" class="font-bold text-primary">: {{ item.business_name }}</span></div>
              </td>
              <td class="p-4">{{ item.roles[0]?.name }}</td>
              <td class="p-4">{{ Helper.nairaFormat(item.credit_limit) }}</td>
              <td class="p-4 text-primary font-semibold">{{ Helper.nairaFormat(item.wallet.balance) }}</td>
              <td class="p-4">
                <div class="hs-dropdown relative inline-flex">
                  <button id="hs-dropdown-custom-icon-trigger" type="button" class="hs-dropdown-toggle p-3 inline-flex justify-center items-center gap-2 rounded-md border border-background font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-background transition-all text-sm sdark:bg-slate-900 sdark:hover:bg-slate-800 sdark:border-gray-700 sdark:text-gray-400 sdark:hover:text-white sdark:focus:ring-offset-gray-800">
                    <svg class="w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    </svg>
                  </button>

                  <div class="hs-dropdown-menu z-40 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2" aria-labelledby="hs-dropdown-custom-icon-trigger">
                    <Link v-show="checkPermission('read-shipment')" :href="route('reports.users.shipments', item.id)" class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:hover:text-gray-300">
                      Shipments
                    </Link>
                  </div>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <Pagination :links="users.links"/>
    </ReportsLayout>
  </DashboardLayout>
</template>

<style scoped>

</style>