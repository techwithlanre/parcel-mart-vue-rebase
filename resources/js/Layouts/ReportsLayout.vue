<script setup>

import {DocumentIcon, WalletIcon, MapPinIcon, UsersIcon} from "@heroicons/vue/20/solid/index.js";
import TopNav from "@/Components/TopNav.vue";

const tabs = [
  { name: "Shipments", icon: WalletIcon, route: "/admin/dashboard/shipments", permissionKey: ['read-shipment-report']},
  { name: "Users", icon: UsersIcon, route: "/admin/dashboard/users", permissionKey: ['read-user-report']},
  { name: "Payments", icon: WalletIcon, route: "/admin/dashboard/payments", permissionKey: ['read-payment-report']},
];

</script>

<template>
  <TopNav :tabs="tabs" class="bg-white shadow shadow-background rounded-xl" />
  <slot />
</template>

<style scoped>

</style>