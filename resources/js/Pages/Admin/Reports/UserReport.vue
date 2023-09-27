<script setup>

import ReportsLayout from "@/Layouts/ReportsLayout.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Helper from "@/Helpers/Helper.js";

const props = defineProps({
  totalUsersCount: Number,
  businessUsersCount: Number,
  individualUsersCount: Number
});

const cardsData = [
  {name: "Total Users", data: props.totalUsersCount},
  {name: "Individual Users", data: props.individualUsersCount},
  {name: "Business Users", data: props.businessUsersCount},
]
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
    </ReportsLayout>
  </DashboardLayout>
</template>

<style scoped>

</style>