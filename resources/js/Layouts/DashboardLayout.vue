<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3'
import {notification} from "ant-design-vue";
import OverviewIcon from "../../images/icons/overview.svg";
import TruckIcon from "../../images/icons/_truck.svg";
import WalletIcon from "../../images/icons/_wallet.svg";
import LocationIcon from "../../images/icons/_location.svg";
import FAQ from "../../images/icons/_faq.svg";
import InviteIcon from "../../images/icons/_invite.svg";
import LogoutIcon from "../../images/icons/_logout.svg";
import ShoppingBagIcon from "../../images/icons/shopping-bag.svg";
import {computed, ref} from "vue";


const page = usePage();

const openNotificationWithIcon = (type, message) => {
  notification[type]({
    message: type,
    description: message
  });
};

/*if (page.props.flash.message) {
  openNotificationWithIcon('success', page.props.flash.message);
  //toast.success(page.props.flash.message);
  page.props.flash.message = "";
}



if (page.props.flash.error) {
  openNotificationWithIcon('error', page.props.flash.error);
  //toast.error(page.props.flash.error);
  page.props.flash.error = "";
}*/

defineProps({
  pageTitle: String
})

const sidebar = [
  [
    { name: "Dashboard", icon: OverviewIcon, route: "/dashboard", admin: false },
    { name: "Shipments", icon: TruckIcon, route: "/shipments", admin: false },
    { name: "Wallet", icon: WalletIcon, route: "/wallet", admin: false },
    { name: "Address Book", icon: LocationIcon, route: '/address-book', admin: false  },
    { name: "FAQs", icon: FAQ, route: '/faq', admin: false  },
    { name: "Invite & Earn", icon: InviteIcon, route: '/invite', admin: false  },
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

const menuType = computed(() => {
  return page.props.auth.user.is_admin === 0 ? "User" : "Admin";
});

const mobileSidebarShow = ref(false)

const toggleSidebar = () => {
  mobileSidebarShow.value = !mobileSidebarShow.value;
  console.log(mobileSidebarShow.value)
}
</script>


<template>
  <!-- component -->
  <div>
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-white">
      <!-- Header -->
      <div class="fixed w-full flex justify-between bg-white border-b">
        <button class="ml-5 sm:hidden" @click="toggleSidebar">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.5 4H5.5C4.67157 4 4 4.67157 4 5.5V9.5C4 10.3284 4.67157 11 5.5 11H9.5C10.3284 11 11 10.3284 11 9.5V5.5C11 4.67157 10.3284 4 9.5 4Z" fill="currentColor"/>
            <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M5.5 13H9.5C10.3284 13 11 13.6716 11 14.5V18.5C11 19.3284 10.3284 20 9.5 20H5.5C4.67157 20 4 19.3284 4 18.5V14.5C4 13.6716 4.67157 13 5.5 13ZM14.5 4H18.5C19.3284 4 20 4.67157 20 5.5V9.5C20 10.3284 19.3284 11 18.5 11H14.5C13.6716 11 13 10.3284 13 9.5V5.5C13 4.67157 13.6716 4 14.5 4ZM14.5 13H18.5C19.3284 13 20 13.6716 20 14.5V18.5C20 19.3284 19.3284 20 18.5 20H14.5C13.6716 20 13 19.3284 13 18.5V14.5C13 13.6716 13.6716 13 14.5 13Z" fill="currentColor"/>
          </svg>
        </button>
        <div class="flex flex-row justify-between items-center">
          <div class="flex items-center justify-start pl-3 w-14 md:w-64 h-14">
            <img src="../../images/logo.png" alt="parcel-mart-logo" class="w-28 rounded-full">
          </div>
          <div class="flex flex-row justify-between items-center text-black">
            <div class="px-10 font-bold sm:text-xl text-sm hidden sm:block">{{ pageTitle }}</div>
            <div class="flex flex-row items-center">
              <Link :href="route('profile.edit')" class="flex justify-end items-center mr-4 hidden sm:block text-gray-700 hover:text-gray-300 sm:text-md text-sm">
                {{ page.props.auth.user.first_name + " " + page.props.auth.user.last_name }}
              </Link>
              <Link :href="route('profile.edit')" class="flex justify-end sm:hidden block items-center mr-4 text-gray-700 hover:text-gray-300 sm:text-md text-sm">
                {{ page.props.auth.user.first_name + " " + page.props.auth.user.last_name }}
              </Link>
            </div>
          </div>
        </div>
      </div>
      <!-- ./Header -->

      <!-- Sidebar -->
      <div :class="{'w-64': mobileSidebarShow === true}" class="fixed flex flex-col top-14 left-0 w-0 md:w-64 bg-primary h-full text-white transition-all duration-300 border-none z-10 sidebar">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow">
          <ul class="flex flex-col py-4 space-y-1" v-for="group  in sidebar">
            <li class="px-5 hidden md:block">
              <div class="flex flex-row items-center h-8">
                <div class="text-sm font-light tracking-wide text-white uppercase">{{ menuType }} Menu</div>
              </div>
            </li>
            <li v-if="page.props.auth.user.is_admin === 0" v-for="item in group">
              <Link :href="item.route"
                    :class="{'text-primary bg-[#004e4f] border-l border-white outline-non group font-bold hover:text-primary hover:bg-background': $page.url.startsWith(item.route)}"
                    class="relative flex flex-row items-center h-11 focus:outline-none  text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6 duration-300">
                <span class="inline-flex justify-center items-center sm:ml-4 ml-2.5">
                  <Component :is="item.icon" />
                </span>
                <span class="ml-2 text-sm tracking-wide truncate">{{ item.name }}</span>
              </Link>
            </li>
            <li v-if="page.props.auth.user.is_admin === 1" v-for="item in adminMenu">
              <Link :href="item.route"
                    :class="{'text-primary bg-[#004e4f] border-l border-white outline-non group font-bold hover:text-primary hover:bg-background': $page.url.startsWith(item.route)}"
                    class="relative flex flex-row items-center h-11 focus:outline-none  text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6 duration-300">
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
          <p class="mb-14 px-5 py-3 hidden md:block text-left text-xs">Copyright @ 2023</p>
        </div>
      </div>
      <!-- ./Sidebar -->

      <div class="h-full ml-0 mt-14 mb-10 md:ml-64 p-5 md:p-10">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>