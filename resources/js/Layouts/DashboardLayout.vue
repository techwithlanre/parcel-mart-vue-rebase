<script setup>
import OverviewIcon from "/resources/images/icons/overview.svg";
import TruckIcon from "/resources/images/icons/_truck.svg";
import ShoppingBagIcon from "/resources/images/icons/shopping-bag.svg";
import WalletIcon from "/resources/images/icons/_wallet.svg";
import LocationIcon from "/resources/images/icons/_location.svg";
import FAQ from "/resources/images/icons/_faq.svg";
import InviteIcon from "/resources/images/icons/_invite.svg";
import LogoutIcon from "/resources/images/icons/_logout.svg";
import {Link, usePage} from '@inertiajs/vue3';
import {toast} from "vue3-toastify";
import 'flowbite';
import {onMounted} from "vue";
import { notification } from 'ant-design-vue';

onMounted(() => {
  initFlowbite();
});

const page = usePage();

const openNotificationWithIcon = (type, message) => {
  notification[type]({
    message: type,
    description: message
  });
};

if (page.props.flash.message) {
  openNotificationWithIcon('success', page.props.flash.message);
  //toast.success(page.props.flash.message);
  page.props.flash.message = "";
}



if (page.props.flash.error) {
  openNotificationWithIcon('error', page.props.flash.error);
  //toast.error(page.props.flash.error);
  page.props.flash.error = "";
}

defineProps({
  pageTitle: String
})

const sidebar = [
  [
    { name: "Dashboard", icon: OverviewIcon, route: "/dashboard", admin: false },
    { name: "Shipments", icon: TruckIcon, route: "/shipments", admin: false },
    { name: "Wallet", icon: WalletIcon, route: "/wallet", admin: false },
    { name: "Address Book", icon: LocationIcon, route: '/address-book', admin: false  },
    { name: "FAQs", icon: FAQ, route: route('faq.index'), admin: false  },
    { name: "Invite & Earn", icon: InviteIcon, route: '#', admin: false  },
    { name: "Logout", icon: LogoutIcon, route: route('logout'), admin: false },
  ],
];

const adminMenu = [
  { name: "Dashboard", icon: OverviewIcon, route: "/dashboard", admin: false },
  { name: "My Shipments", icon: ShoppingBagIcon, route: "/shipments", admin: false },
  { name: "My Wallet", icon: WalletIcon, route: "/wallet", admin: false },
  { name: "Address Book", icon: LocationIcon, route: '/address-book', admin: false  },
  { name: "Users List", icon: OverviewIcon, route: "/admin/users", admin: true },
  { name: "Roles", icon: ShoppingBagIcon, route: "/admin/roles", admin: true },
  { name: "All Shipments", icon: TruckIcon, route: "/admin/shipments" , admin: true },
  { name: "Provider Rates", icon: OverviewIcon, route: "/admin/settings/rate", admin: false },
  { name: "Logout", icon: LogoutIcon, route: route('logout'), admin: false },
];
</script>

<template>

  <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 sdark:bg-gray-800 sdark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-start">
          <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
          </button>
          <div class="flex ml-2 md:mr-24">
            <img src="../../images/logo.png" alt="parcel-mart-logo" class="w-28 rounded-full">
          </div>
        </div>
        <div class="flex items-center">
          <div class="flex items-center ml-3">
            <div>
              <button type="button" class="flex text-sm items-center gap-x-3" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                <div class="p-1 bg-gray-300 rounded-full">
                  <svg width="10" height="10" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <g fill="#54595d">
                      <path d="M 10 11 C 4.08 11 2 14 2 16 L 2 19 L 18 19 L 18 16 C 18 14 15.92 11 10 11 Z"/>
                      <circle cx="10" cy="5.5" r="4.5"/>
                    </g>
                  </svg>
                </div>
                <div>{{ page.props.auth.user.first_name + " " + page.props.auth.user.last_name }}</div>
              </button>
            </div>
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow-md sdark:bg-gray-700 sdark:divide-gray-600" id="dropdown-user">
              <div class="px-4 py-3" role="none">
                <p class="text-sm text-gray-900 sdark:text-white" role="none">
                  {{ page.props.auth.user.first_name + " " + page.props.auth.user.last_name }}
                </p>
              </div>
              <ul class="py-1" role="none">
                <li>
                  <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 sdark:text-gray-300 sdark:hover:bg-gray-600 sdark:hover:text-white" role="menuitem">Profile</Link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <aside id="logo-sidebar" class="fixed  left-0 z-40 w-64 mt-10 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 sdark:bg-gray-800 sdark:border-gray-700" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white sdark:bg-gray-800">
      <ul class="space-y-2 font-medium" v-for="group  in sidebar">
        <li v-if="page.props.auth.user.is_admin === 0" v-for="item in group" class="">
          <Link :href="item.route" class="flex items-center hover:text-primary p-2 text-gray-900 rounded-lg sdark:text-white hover:bg-gray-100 sdark:hover:bg-gray-700 group" :class="{'text-white bg-primary group font-bold rounded-md py-2 hover:text-primary hover:bg-background': $page.url.startsWith(item.route)}">
            <Component :is="item.icon" class="w-6 h-6 fill-current" />
            <span class="ml-3">{{ item.name }}</span>
          </Link>
        </li>
        <li v-if="page.props.auth.user.is_admin === 1" v-for="item in adminMenu" class="">
          <Link :href="item.route" class="flex items-center p-2 hover:text-primary text-gray-900 rounded-lg sdark:text-white hover:bg-gray-100 sdark:hover:bg-gray-700 group">
            <Component :is="item.icon" class="w-6 h-6 fill-current" />
            <span class="flex-1 ml-3 whitespace-nowrap">{{ item.name }}</span>
          </Link>
        </li>
      </ul>
    </div>
  </aside>

  <div class="sm:ml-64">
    <div class="mt-14">
      <main :key="$page.url">
        <div class="p-5">
          <div v-if="$page.props.flash.message" class="alert">
            {{ $page.props.flash.message }}
          </div>
          <div v-if="$page.props.flash.error" class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 sdark:bg-gray-800 sdark:text-red-400" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div>
              {{ $page.props.flash.error }}
            </div>
          </div>
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>