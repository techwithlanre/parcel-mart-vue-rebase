<script setup>
import {computed, ref} from 'vue';
import { usePage, Link } from '@inertiajs/vue3'

const props = defineProps({
    title: String
});

let show = ref(false);
const isOpen = () => (show.value = !show.value);
const page = usePage();
const fullName = computed(() => {
    return page.props.auth.user.first_name + ' ' + page.props.auth.user.last_name;
});


const adminMenu = [
    { name: "Users", icon: "", route: "/admin/users", admin: true },
    { name: "Roles", icon: "", route: "/admin/roles", admin: true },
    { name: "Shipments", icon: "", route: "/admin/shipments" , admin: true },
    { name: "Provider Rates", icon: "", route: "/admin/settings/rate", admin: false },
];

</script>

<template>
    <div class="p-5">
        <div class="flex items-center justify-between bg-white py-3 px-10 rounded-2xl border">
            <div class="flex mt-2">
                <h1 class="text-lg font-semibold leading-relaxed text-gray-800">{{ title }}</h1>
            </div>
            <div class="flex items-center gap-2 z-50" >
                <div class="flex flex-col">
                    <div v-show="show" class="absolute py-2 mt-10 bg-white flex flex-col gap-2 text-gray-600 rounded-md shadow-xl duration-500 ease-in-out transition-all w-44" >
                        <Link :href="route('profile.edit')" class="px-5 py-1 text-primary hover:text-primary-dark">Profile</Link>
                    </div>
                </div>
                <button @click="show = !show">
                    {{ fullName }}
                </button>
            </div>
        </div>
    </div>
</template>
