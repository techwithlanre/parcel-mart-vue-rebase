<script setup>
import OverviewIcon from "/resources/images/icons/overview.svg";
import ShoppingBagIcon from "/resources/images/icons/shopping-bag.svg";
import GraphIcon from "/resources/images/icons/graph.svg";
import CalendarIcon from "/resources/images/icons/calendar.svg";
import WalletIcon from "/resources/images/icons/wallet.svg";
import FileIcon from "/resources/images/icons/file.svg";
import GroupChatIcon from "/resources/images/icons/group-chat.svg";
import SettingsIcon from "/resources/images/icons/settings.svg";
import LogoutIcon from "/resources/images/icons/logout.svg";
import TopBar from "@/Components/TopBar.vue";
import {Link, usePage} from '@inertiajs/vue3';
import TestModeAlert from "@/Components/TestModeAlert.vue";
import {toast} from "vue3-toastify";

const page = usePage();
if (page.props.flash.message) {
    toast.success(page.props.flash.message);
    page.props.flash.message = "";
}

if (page.props.flash.error) {
    toast.error(page.props.flash.error);
    page.props.flash.error = "";
}

defineProps({
    pageTitle: String
})

const sidebar = [
    [
        { name: "Dashboard", icon: OverviewIcon, route: "/dashboard", admin: false },
        { name: "Shipments", icon: ShoppingBagIcon, route: "/shipments", admin: false },
        { name: "Wallet", icon: WalletIcon, route: "/wallet", admin: false },
        { name: "Address Book", icon: GroupChatIcon, route: '/address-book', admin: false  },
        { name: "Settings", icon: SettingsIcon, route: "/settings/shipping/description", admin: false },
        { name: "Logout", icon: LogoutIcon, route: route('logout'), admin: false },
    ],
];

const adminSidebar = [
    [
        { name: "Users", icon: "", route: "/admin/users", admin: true },
        { name: "Roles", icon: "", route: "/admin/roles", admin: true },
        { name: "Shipments", icon: "", route: "/admin/shipments" , admin: true },
        { name: "Provider Rates", icon: "", route: "/admin/settings/rate", admin: false },
    ]
];

</script>

<template>
    <div class="w-full min-h-screen font-sans text-gray-900 bg-gray-50 flex overflow-x-hidden">
        <aside class="hidden md:block py-6 px-5 w-64 bg-white ease-in-out duration-300 translate-x-0 border-r">
            <div class="w-[100px]">
                <img src="../../images/logo-dark.jpg" alt="parcel-mart-logo" class="w-16 h-16 rounded-full">
            </div>
            <ul v-for="group in sidebar" class="flex flex-col gap-y-6 pt-20">
                <li v-for="item in group" class="">
                    <Link :href="item.route" class="flex gap-x-4 items-center text-gray-500 hover:text-primary group px-5" :class="{'text-white bg-primary group font-bold rounded-md py-2 hover:text-white hover:bg-background ': $page.url.startsWith(item.route)}">
                        <span class="absolute w-1.5 h-8 bg-primary rounded-r-full left-0 scale-y-0 -translate-x-full group-hover:scale-y-100 group-hover:translate-x-0 transition-transform ease-in-out" :class="{'group-hover:scale-y-100 group-hover:translate-x-0': $page.url.startsWith(item.route)}"/>
                        <Component :is="item.icon" class="w-6 h-6 fill-current" />
                        <span>{{ item.name }}</span>
                    </Link>
                </li>
            </ul>


            <ul v-if="page.props.auth.user.is_admin" v-for="group in adminSidebar" class="flex flex-col gap-y-6 pt-20">
                <h1>Admin  Menu</h1>
                <li v-for="item in group" class="">
                    <Link :href="item.route" class="flex gap-x-4 items-center text-gray-500 hover:text-primary group px-5" :class="{'text-white bg-primary group font-bold rounded-md py-2 hover:text-white hover:bg-background ': $page.url.startsWith(item.route)}">
                        <span class="absolute w-1.5 h-8 bg-primary rounded-r-full left-0 scale-y-0 -translate-x-full group-hover:scale-y-100 group-hover:translate-x-0 transition-transform ease-in-out" :class="{'group-hover:scale-y-100 group-hover:translate-x-0': $page.url.startsWith(item.route)}"/>
                        <Component :is="item.icon" class="w-6 h-6 fill-current" />
                        <span>{{ item.name }}</span>
                    </Link>
                </li>
            </ul>
            <div class="bottom-1 absolute w-3/4">
                <div class="border p-5 rounded-2xl">

                </div>
            </div>
        </aside>

        <transition name="page" mode="out-in" appear>
            <main class="flex-1 pb-8">
                <TestModeAlert />
                <TopBar :title="pageTitle" />
<!--                <div>
                    <div v-if="$page.props.flash.message" class="alert">
                        {{ $page.props.flash.message }}
                    </div>
                </div>-->
                <transition name="page" mode="out-in" appear>
                    <main :key="$page.url" class="">
                        <div class="p-5 ">
                            <slot />
                        </div>
                    </main>
                </transition>
            </main>
        </transition>
    </div>
</template>
