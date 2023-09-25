<script setup>

import {DocumentIcon, WalletIcon, MapPinIcon, UsersIcon} from "@heroicons/vue/20/solid/index.js";
import TopNav from "@/Components/TopNav.vue";

const tabs = [
  { name: "Shipments Report", icon: WalletIcon, route: "/admin/reports/shipments", permissionKey: ['read-shipment-report']},
  { name: "Users Report", icon: UsersIcon, route: "/admin/reports/users", permissionKey: ['read-user-report']},
  { name: "Payment Report", icon: WalletIcon, route: "/admin/reports/payments", permissionKey: ['read-payment-report']},
];

</script>

<template>
  <TopNav :tabs="tabs" class="bg-white shadow shadow-background rounded-xl" />
  <slot />
</template>

<style scoped>

</style>