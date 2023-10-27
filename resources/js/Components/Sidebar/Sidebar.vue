<script setup>
import LogoutIcon from "../../../images/icons/_logout.svg";
import {Link} from "@inertiajs/vue3";
import {ref} from "vue";
import OverviewIcon from "../../../images/icons/overview.svg";
import TruckIcon from "../../../images/icons/_truck.svg";
import WalletIcon from "../../../images/icons/_wallet.svg";
import LocationIcon from "../../../images/icons/_location.svg";
import FAQ from "../../../images/icons/_faq.svg";
import InviteIcon from "../../../images/icons/_invite.svg";
import ChartIcon from "../../../images/icons/graph.svg";
import {CursorArrowRippleIcon, DocumentTextIcon, UsersIcon} from "@heroicons/vue/20/solid/index.js";
import PencilIcon from "../../../images/icons/pencil.svg";
import OptionsIcon from "../../../images/icons/options.svg";


const props = defineProps({
  mobileSidebarShow: Boolean
})


const mobileSidebarShow = ref(false)

const menu = [
  { name: "Dashboard", icon: OverviewIcon, route: "/dashboard", admin: false },
  { name: "Shipments", icon: TruckIcon, route: "/shipments", admin: false },
  { name: "Wallet", icon: WalletIcon, route: "/wallet", admin: false },
  { name: "Address Book", icon: LocationIcon, route: '/address-book', admin: false},
  { name: "FAQs", icon: FAQ, route: '/faq', admin: false  },
  { name: "Invite & Earn", icon: InviteIcon, route: '/invite', admin: false  },
  { name: "Feedback", icon: FAQ, route: '/feedback/tickets/user', admin: false  },
  { name: "Analytics", icon: ChartIcon, route: "/admin/analytics", admin: true, permissionKey: ['read-dashboard'] },
  { name: "Reports", icon: DocumentTextIcon, route: "/admin/reports/shipments" , admin: true, permissionKey: ['read-shipment-report'] },
  { name: "Users", icon: UsersIcon, route: "/admin/users", admin: true, permissionKey: ["read-user", "create-user"] },
  { name: "Quotes", icon: PencilIcon, route: "/admin/quotes", admin: true, permissionKey: ["read-user", "create-user"] },
  { name: "Roles", icon: CursorArrowRippleIcon, route: "/admin/roles", admin: true, permissionKey: ["read-role", "create-role", 'edit-role', 'delete-role'] },
  { name: "Shipment Locations", icon: LocationIcon, route: "/admin/shipment-locations", admin: true, permissionKey: ["read-role", "create-role", 'edit-role', 'delete-role'] },
  { name: "Providers", icon: OptionsIcon, route: "/admin/settings/rate", admin: true, permissionKey: ["read-provider"] },
  { name: "All Tickets", icon: FAQ, route: '/admin/feedback/tickets', admin: true,   permissionKey: ['read-ticket']},
];

const toggleSidebar = () => {
  mobileSidebarShow.value = !mobileSidebarShow.value;
}

const checkPermission = (featurePermissions) => {
  return page.props.auth.permissions.some( permission => featurePermissions.includes(permission));
}
</script>

<template>
  <div :class="{'w-64': mobileSidebarShow === true}" style="z-index: 100" class="fixed flex flex-col top-14 left-0 w-0 md:w-64 bg-primary h-full text-white transition-all duration-500 border-none sidebar">
    <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow">
      <ul class="">
        <li v-for="item in menu" v-if="$page.props.auth.user.is_admin === 0" class="pt-1">
          <Link v-if="!item.admin" :href="item.route" :class="{'text-primary bg-[#004e4f] border-l border-r border-r-red-500 border-white outline-non group font-bold hover:text-primary hover:bg-background/5': $page.url === item.route}"
                class="relative flex flex-row items-center h-11 focus:outline-none text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6 duration-500">
                <span class="inline-flex justify-center items-center sm:ml-4 ml-2.5">
                  <Component :is="item.icon" class="w-5 h-5 fill-current" />
                </span>
            <span class="ml-2 text-sm tracking-wide truncate">{{ item.name }}</span>
          </Link>
        </li>
        <li v-for="item in menu" v-if="$page.props.auth.user.is_admin === 1" class="">
          <Link v-if="item.admin" :href="item.route" :class="{'text-primary bg-[#004e4f] border-l border-white outline-non group font-bold hover:text-primary hover:bg-background': $page.url === item.route}"
                class="relative flex flex-row items-center h-11 focus:outline-none  text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6 duration-500">
              <span class="inline-flex justify-center items-center sm:ml-4 ml-2.5">
                <Component :is="item.icon" class="w-5 h-5 fill-current" />
              </span>
            <span class="ml-2 text-sm tracking-wide truncate">{{ item.name }}</span>
          </Link>
        </li>
        <li class="">
          <Link :href="route('logout')"
                class="relative flex flex-row items-center h-11 focus:outline-none  text-white hover:text-white border-l-4 border-transparent hover:bg-[#008083]/50 hover:border-background pr-6 duration-500">
                <span class="inline-flex justify-center items-center sm:ml-4 ml-2.5">
                  <Component :is="LogoutIcon" />
                </span>
            <span class="ml-2 text-sm tracking-wide truncate">Logout</span>
          </Link>
        </li>
      </ul>
      <p class="mb-14 px-5 py-3 hidden md:block text-left text-xs">Copyright @ 2023</p>
    </div>
  </div>
</template>

<style scoped>

</style>