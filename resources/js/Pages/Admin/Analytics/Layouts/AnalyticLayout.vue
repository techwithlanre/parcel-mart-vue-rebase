<script setup>

import TopNav from "@/Components/TopNav.vue";
import {WalletIcon} from "@heroicons/vue/20/solid/index.js";

const tabs = [
  { name: "Users Analytics", icon: '', route: "/admin/analytics/users", permissionKey: ['read-shipment-report']},
  { name: "Shipments Analytics", icon: WalletIcon, route: "/admin/analytics/shipments", permissionKey: ['read-shipment-report']},
  { name: "Payments Analytics", icon: WalletIcon, route: "/admin/analytics/payments", permissionKey: ['read-payment-report']},
  { name: "Geographic Analytics", icon: WalletIcon, route: "/admin/reports/payments", permissionKey: ['read-payment-report']},
];
</script>

<template>
  <TopNav :tabs="tabs" class="bg-white shadow shadow-background rounded-xl" />
  <slot />
</template>

<style scoped>

</style>