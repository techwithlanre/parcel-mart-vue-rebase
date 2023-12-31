<script setup>
import {Head, Link} from '@inertiajs/vue3'
import OverviewIcon from "../../images/icons/overview.svg";
import TruckIcon from "../../images/icons/_truck.svg";
import WalletIcon from "../../images/icons/_wallet.svg";
import LocationIcon from "../../images/icons/_location.svg";
import LogoutIcon from "../../images/icons/_logout.svg";
import ShoppingBagIcon from "../../images/icons/shopping-bag.svg";
import {ref} from "vue";
import FAQ from "../../images/icons/_faq.svg";
import InviteIcon from "../../images/icons/_invite.svg";


defineProps({
  pageTitle: String
})

const adminMenu = [
  { name: "Dashboard", icon: OverviewIcon, route: "/admin/dashboard", admin: false },
  { name: "Shipments", icon: TruckIcon, route: "/shipments", admin: false },
  { name: "Wallet", icon: WalletIcon, route: "/wallet", admin: false },
  { name: "Address Book", icon: LocationIcon, route: '/address-book', admin: false  },
  { name: "FAQs", icon: FAQ, route: '/faq', admin: false  },
  { name: "Invite & Earn", icon: InviteIcon, route: '/invite', admin: false},
];


const menu =  [
  { name: "Users", icon: OverviewIcon, route: "/admin/users", admin: true, permissionKey: "read-user" },
  { name: "Roles", icon: ShoppingBagIcon, route: "/admin/roles", admin: true, permissionKey: "read-role" },
  //{ name: "Shipments ", icon: TruckIcon, route: "/admin/shipments" , admin: true, permissionKey: "read-shipment" },
  { name: "Reports", icon: WalletIcon, route: "/admin/reports/shipments" , admin: true, permissionKey: "read-shipment" },
  { name: "Providers", icon: OverviewIcon, route: "/admin/settings/rate", admin: false, permissionKey: "read-provider" },
  { name: "Logout", icon: LogoutIcon, route: route('logout'), admin: false },
];

const setup = () => {
  return {
    loading: true,
  }
}

const mobileSidebarShow = ref(false)

const toggleSidebar = () => {
  mobileSidebarShow.value = !mobileSidebarShow.value;
}
</script>


<template>
  <!-- component -->
  <Head :title="pageTitle"></Head>

  <div class="min-h-screen flex flex-col bg-white">
    <div class="fixed w-full flex items-center text-gray-700 border-b bg-white">
      <div class="flex items-center justify-between pl-3 w-36 md:w-64 h-14 border-r">
        <Link :href="route('dashboard')">
          <img src="../../images/logo.png" alt="parcel-mart-logo" class="sm:w-28 w-16 rounded-full">
        </Link>
        <button class="ml-5 sm:hidden" @click="toggleSidebar">
          <svg class="w-7 h-7 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.5 4H5.5C4.67157 4 4 4.67157 4 5.5V9.5C4 10.3284 4.67157 11 5.5 11H9.5C10.3284 11 11 10.3284 11 9.5V5.5C11 4.67157 10.3284 4 9.5 4Z" fill="currentColor"/>
            <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M5.5 13H9.5C10.3284 13 11 13.6716 11 14.5V18.5C11 19.3284 10.3284 20 9.5 20H5.5C4.67157 20 4 19.3284 4 18.5V14.5C4 13.6716 4.67157 13 5.5 13ZM14.5 4H18.5C19.3284 4 20 4.67157 20 5.5V9.5C20 10.3284 19.3284 11 18.5 11H14.5C13.6716 11 13 10.3284 13 9.5V5.5C13 4.67157 13.6716 4 14.5 4ZM14.5 13H18.5C19.3284 13 20 13.6716 20 14.5V18.5C20 19.3284 19.3284 20 18.5 20H14.5C13.6716 20 13 19.3284 13 18.5V14.5C13 13.6716 13.6716 13 14.5 13Z" fill="currentColor"/>
          </svg>
        </button>
      </div>

      <div class="flex flex-1 flex-row justify-between items-center header-right px-10">
        <div class="font-bold text-lg">
          {{ pageTitle }}
        </div>
        <div class="">
          <div>
            <Link :href="route('profile.edit')" class="sm:block hidden">
              {{ $page.props.auth.user.first_name + " " + $page.props.auth.user.last_name }}
            </Link>
            <Link :href="route('profile.edit')" class="sm:hidden block text-sm font-bold">
              Profile
            </Link>
          </div>
        </div>
      </div>
    </div>
    <!-- ./Header -->

    <!-- Sidebar -->
    <div :class="{'w-64': mobileSidebarShow === true}" class="fixed flex flex-col top-14 left-0 w-0 md:w-64 bg-primary h-full text-white transition-all duration-500 border-none z-10 sidebar">
      <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow">
        <ul class="flex flex-col py-4 space-y-1">
          <li v-for="item in adminMenu">
            <Link :href="item.route"
                  :class="{'text-primary bg-[#004e4f] border-l border-white outline-non group font-bold hover:text-primary hover:bg-background': $page.url.endsWith(item.route)}"
                  class="relative flex flex-row items-center h-11 focus:outline-none  text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6 duration-500">
                <span class="inline-flex justify-center items-center sm:ml-4 ml-2.5">
                  <Component :is="item.icon" />
                </span>
              <span class="ml-2 text-sm tracking-wide truncate">{{ item.name }}</span>
            </Link>
          </li>
          <li class="py-5 px-5 pt-10 relative text-sm flex flex-col items-start h-11 font-bold focus:outline-none  text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6 duration-500">
            ADMIN MENU
          </li>
          <li v-for="item in menu" :key="item" class="mt-10">
            <Link :href="item.route"
                  :class="{'text-primary bg-[#004e4f] border-l border-white outline-non group font-bold hover:text-primary hover:bg-background': $page.url.includes(item.route)}"
                  class="relative flex flex-row items-center h-11 focus:outline-none  text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6 duration-500">
                <span class="inline-flex justify-center items-center sm:ml-4 ml-2.5">
                  <Component :is="item.icon" />
                </span>
              <span class="ml-2 text-sm tracking-wide truncate">{{ item.name }}</span>
            </Link>
          </li>

        </ul>
        <p class="mb-14 px-5 py-3 hidden md:block text-left text-xs">Copyright @ 2023</p>
      </div>
    </div>
    <!-- ./Sidebar -->
    <div class="h-full ml-0 mt-14 mb-10 md:ml-64 p-5 md:p-10">
      <slot />
    </div>
  </div>
</template>

<style scoped>

</style>