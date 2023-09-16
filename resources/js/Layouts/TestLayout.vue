<script setup>
import {Link, usePage} from '@inertiajs/vue3'
import {notification} from "ant-design-vue";
import OverviewIcon from "../../images/icons/overview.svg";
import TruckIcon from "../../images/icons/_truck.svg";
import WalletIcon from "../../images/icons/_wallet.svg";
import LocationIcon from "../../images/icons/_location.svg";
import FAQ from "../../images/icons/_faq.svg";
import InviteIcon from "../../images/icons/_invite.svg";
import LogoutIcon from "../../images/icons/_logout.svg";
import ShoppingBagIcon from "../../images/icons/shopping-bag.svg";


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
    { name: "Invite & Earn", icon: InviteIcon, route: route('invite.index'), admin: false  },
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

const setup = () => {
  const getTheme = () => {
    if (window.localStorage.getItem('_dark')) {
      return JSON.parse(window.localStorage.getItem('_dark'))
    }
    return !!window.matchMedia && window.matchMedia('(prefers-color-scheme: _dark)').matches
  }

  const setTheme = (value) => {
    window.localStorage.setItem('_dark', value)
  }

  return {
    loading: true,
    is_dark: getTheme(),
  }
}
</script>


<template>
  <!-- component -->

  <div>
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-white">
      <!-- Header -->
      <div class="fixed w-full flex items-center justify-between h-14 text-white z-10 border-b">
        <div class="flex items-center justify-start pl-3 w-14 md:w-64 h-14">
          <img src="../../images/logo.png" alt="parcel-mart-logo" class="w-28 rounded-full">
        </div>
        <div class="flex justify-between items-center h-14 header-right">
          <ul class="flex items-center">

            <li>
              <div class="block w-px h-6 mx-3 bg-gray-400 _dark:bg-gray-700"></div>
            </li>
            <li>
              <Link href="#" class="flex items-center mr-4 hover:text-blue-100">
                <span class="inline-flex mr-1">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </span>
                Logout
              </Link>
            </li>
          </ul>
        </div>
      </div>
      <!-- ./Header -->

      <!-- Sidebar -->
      <div class="fixed flex flex-col top-14 left-0 w-14 hover:w-64 md:w-64 bg-primary h-full text-white transition-all duration-500 border-none z-10 sidebar">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow">
<!--          <ul class="flex flex-col py-4 space-y-1" v-for="group  in sidebar">
            <li v-if="page.props.auth.user.is_admin === 0" v-for="item in group" class="">
              <Link :href="item.route" class="" :class="{'text-white bg-primary group font-bold rounded-md py-2 hover:text-primary hover:bg-background': $page.url.startsWith(item.route)}">
                <Component :is="item.icon" class="w-6 h-6 text-gray-400" />
                <span class="ml-3">{{ item.name }}</span>
              </Link>
            </li>
            <li v-if="page.props.auth.user.is_admin === 1" v-for="item in adminMenu" class="">
              <Link :href="item.route" class="flex items-center p-2 hover:text-primary text-gray-900 rounded-lg s_dark:text-white hover:bg-gray-100 s_dark:hover:bg-gray-700 group">
                <Component :is="item.icon" class="w-6 h-6 fill-current" />
                <span class="flex-1 ml-3 whitespace-nowrap">{{ item.name }}</span>
              </Link>
            </li>
          </ul>-->
          <ul class="flex flex-col py-4 space-y-1" v-for="group  in sidebar">
            <li class="px-5 hidden md:block">
              <div class="flex flex-row items-center h-8">
                <div class="text-sm font-light tracking-wide text-gray-400 uppercase">Main</div>
              </div>
            </li>
            <li v-if="page.props.auth.user.is_admin === 0" v-for="item in group">
              <Link :href="item.route"
                    :class="{'text-primary bg-[#004e4f] border-l border-white outline-non group font-bold hover:text-primary hover:bg-background': $page.url.startsWith(item.route)}"
                    class="relative flex flex-row items-center h-11 focus:outline-none  text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6">
                <span class="inline-flex justify-center items-center sm:ml-4 ml-2.5">
                  <Component :is="item.icon" />
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">{{ item.name }}</span>
              </Link>
            </li>
            <li v-if="page.props.auth.user.is_admin === 1" v-for="item in adminMenu">
              <Link :href="item.route" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 _dark:hover:bg-gray-600 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 _dark:hover:border-gray-800 pr-6">
                <span class="inline-flex justify-center items-center sm:ml-4 ml-2.5">
                  <Component :is="item.icon" />
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">{{ item.name }}</span>
              </Link>
            </li>
<!--            <li>
              <Link href="wallet" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 _dark:hover:bg-gray-600 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 _dark:hover:border-gray-800 pr-6">
                <span class="inline-flex justify-center items-center ml-4">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">Board</span>
                <span class="hidden md:block px-2 py-0.5 ml-auto text-xs font-medium tracking-wide text-blue-500 bg-indigo-50 rounded-full">New</span>
              </Link>
            </li>-->
          </ul>
          <p class="mb-14 px-5 py-3 hidden md:block text-center text-xs">Copyright @2021</p>
        </div>
      </div>
      <!-- ./Sidebar -->

      <div class="h-full ml-14 mt-14 mb-10 md:ml-64 p-10">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>