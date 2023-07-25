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
import {onMounted, watch} from "vue";
import { notification } from 'ant-design-vue';

const page = usePage();

const openNotificationWithIcon = (type, message) => {
  notification[type]({
    message: type,
    description: message
  });
};

if (page.props.flash.message) {
  openNotificationWithIcon('success', page.props.flash.message);
}



if (page.props.flash.error) {
  openNotificationWithIcon('error', page.props.flash.error);
}

defineProps({
  pageTitle: String
})

const sidebar = [
  { name: "Dashboard", icon: OverviewIcon, route: "/dashboard", admin: false },
  { name: "Shipments", icon: TruckIcon, route: "/shipments", admin: false },
  { name: "Wallet", icon: WalletIcon, route: "/wallet", admin: false },
  { name: "Address Book", icon: LocationIcon, route: '/address-book', admin: false  },
  { name: "FAQs", icon: FAQ, route: '/faq', admin: false  },
  { name: "Invite & Earn", icon: InviteIcon, route: '/invite', admin: false  },
  { name: "Logout", icon: LogoutIcon, route: route('logout'), admin: false },
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
  <header class="flex flex-wrap sm:justify-start sm:flex-nowrap z-50 w-full bg-white border-b border-gray-200 text-sm py-3 sm:py-0">
    <nav class="relative w-full mx-auto px-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8" aria-label="Global">
      <div class="flex items-center justify-between">
        <a class="flex-none text-xl font-semibold " href="#" aria-label="Brand"><img src="../../images/logo.png" alt="parcel-mart-logo" class="w-28 rounded-full"></a>
        <div class="sm:hidden">
          <button type="button" class="hs-collapse-toggle p-2 inline-flex justify-center items-center gap-2 rounded-md border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-primary transition-all text-sm sdark:bg-slate-900 sdark:hover:bg-slate-800 sdark:border-gray-700 sdark:text-gray-400 sdark:hover:text-white sdark:focus:ring-offset-gray-800" data-hs-collapse="#navbar-collapse-with-animation" aria-controls="navbar-collapse-with-animation" aria-label="Toggle navigation">
            <svg class="hs-collapse-open:hidden w-4 h-4" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
            </svg>
            <svg class="hs-collapse-open:block hidden w-4 h-4" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </button>
        </div>
      </div>
      <div id="navbar-collapse-with-animation" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow sm:block">
        <div class="flex flex-col gap-y-4 gap-x-0 mt-5 sm:flex-row sm:items-center sm:justify-end sm:gap-y-0 sm:gap-x-7 sm:mt-0 sm:pl-7">
          <Link
              v-if="!page.props.auth.user.is_admin"
              v-for="item in sidebar"
              class="font-medium text-gray-500 sm:py-6 hover:text-primary"
              :href="item.route" aria-current="page"
              :class="{'text-primary group font-bold py-1 hover:text-primary border-b-2 border-primary transition-all duration-300': $page.url.startsWith(item.route)}">{{ item.name }}</Link>

          <Link
              v-else
              v-for="item in adminMenu"
              class="font-medium text-gray-500 sm:py-6 hover:text-primary"
              :href="item.route" aria-current="page"
              :class="{'text-primary group font-bold py-1 hover:text-primary border-b-2 border-primary transition-all duration-300': $page.url.startsWith(item.route)}">{{ item.name }}</Link>

          <Link :href="route('profile.edit')" class="flex items-center gap-x-2 font-medium text-gray-500 hover:text-primary sm:border-l sm:border-gray-300 sm:my-6 sm:pl-6 sdark:border-gray-700 sdark:text-gray-400 sdark:hover:text-primary" href="#">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
            </svg>
            Profile </Link>
        </div>
      </div>
    </nav>
  </header>
  <div>
    <div class="">
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