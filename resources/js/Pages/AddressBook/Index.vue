<script setup>
import {defineComponent} from 'vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import { DownOutlined, SmileOutlined } from '@ant-design/icons';
import { Dropdown, Space } from 'ant-design-vue';
import {twMerge} from "tailwind-merge";

defineProps({
    addresses: Object
});
</script>

<template>
    <Head title="Address Book" />
    <AuthenticatedLayout page-title="Address Book">
        <div>
            <div class="flex flex-col justify-end items-end">
                <Link :href="route('address-book.create')"
                      class="bg-primary px-3 py-2  text-sm text-white hover:text-gray-50 rounded-md focus:outline-none focus:ring-2 mb-10 focus:ring-offset-2 focus:ring-background">
                    Create Address
                </Link>
            </div>
            <div class="grid lg:grid-cols-3 gap-x-5 gap-y-10">
                <div class="card border bg-white p-5"  v-if="addresses.data.length > 0" v-for="item in addresses.data">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold">{{ item.address_contacts[0].contact_name }}</h3>
                        <div>
                            <button >
                                <a-dropdown>
                                    <a class="ant-dropdown-link" @click.prevent>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" id="option"><circle cx="12" cy="3" r="3"></circle><circle cx="12" cy="12" r="4"></circle><circle cx="12" cy="21" r="3"></circle></svg>
                                    </a>
                                    <template #overlay>
                                        <a-menu>
                                            <a-menu-item>
                                                <Link :href="route('address-book.edit', item.id)">Edit</Link>
                                            </a-menu-item>
                                            <a-menu-item>
                                                <a href="javascript:;">Set as default</a>
                                            </a-menu-item>
                                            <a-menu-item>
                                                <Link :href="route('address-book.destroy', item.id)" :class="twMerge('text-red-600 font-bold')">Delete</Link>
                                            </a-menu-item>
                                        </a-menu>
                                    </template>
                                </a-dropdown>
                            </button>
                        </div>
                    </div>
                    <p class="flex gap-x-10">
                        <span class="font-semibold text-primary">{{ item.address_contacts[0].contact_phone }}</span>
                        <span class="font-semibold">{{ item.address_contacts[0].contact_email }}</span>
                    </p>
                    <hr class="mt-2">
                    <p class="mt-5"> {{ item.address }}</p>
                    <p class=""> {{ item.landmark }}</p>
                    <p class="flex gap-x-10">
                        <div class="text-blue-950 font-bold">{{ item.country.name }}</div>
                        <div class="text-blue-950 font-bold">{{ item.city.name }}</div>
                    </p>
                </div>
            </div>
            <Pagination :links="addresses.links"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
