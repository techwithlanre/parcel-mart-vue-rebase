<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ParcelForm from "@/Components/Front/ParcelForm/ParcelForm.vue";
import {twMerge} from "tailwind-merge";
import TopNav from "@/Components/TopNav.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import {DownOutlined} from "@ant-design/icons-vue";
import {ref} from "vue";
import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";

const activeKey = ref('1');
const tabs = [
    { name: "Parcels", icon: WalletIcon, route: "profile" },
    { name: "Documents", icon: DocumentIcon, route: "settlement-details" }
];

</script>

<template>
    <AuthenticatedLayout page-title="Shipments">
        <div class="flex flex-col lg:flex-row w-full gap-5">
            <div class="p-10 lg:w-1/3 bg-white border rounded-2xl">
                <div class="p-4 flex flex-col gap-x-4">
                    <div class="flex flex-col w-full">
                        <TopNav :tabs="tabs" />
                        <a-dropdown>
                            <a class="ant-dropdown-link mb-1.5 text-primary" @click.prevent>
                                Nigeria
                                <DownOutlined />
                            </a>
                            <template #overlay>
                                <a-menu>
                                    <a-menu-item>
                                        <a href="javascript:;">1st menu item</a>
                                    </a-menu-item>
                                    <a-menu-item>
                                        <a href="javascript:;">2nd menu item</a>
                                    </a-menu-item>
                                    <a-menu-item>
                                        <a href="javascript:;">3rd menu item</a>
                                    </a-menu-item>
                                </a-menu>
                            </template>
                        </a-dropdown>
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full mb-5"
                            placeholder="Enter address you're sending from"
                            required
                            autofocus
                            autocomplete="off"
                        />
                    </div>
                    <div class="flex flex-col">
                        <a-dropdown>
                            <a class="ant-dropdown-link mb-1.5 text-primary" @click.prevent>
                                United Kingdom
                                <DownOutlined />
                            </a>
                            <template #overlay>
                                <a-menu>
                                    <a-menu-item>
                                        <a href="javascript:;">1st menu item</a>
                                    </a-menu-item>
                                    <a-menu-item>
                                        <a href="javascript:;">2nd menu item</a>
                                    </a-menu-item>
                                    <a-menu-item>
                                        <a href="javascript:;">3rd menu item</a>
                                    </a-menu-item>
                                </a-menu>
                            </template>
                        </a-dropdown>
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            placeholder="Enter address you're sending to"
                            required
                            autofocus
                            autocomplete="off"
                        />
                    </div>
                    <div class="flex flex-col mt-7">
                        <SelectInput placeholder="Quantity" auto-size class="" place-holder="Quantity"  />
                    </div>
                    <div class="flex flex-col mt-7">
                        <TextInputSelectATopddon model-value="" />
                        <TextInput model-value="" />
                        <a-input v-model:value="userName" placeholder="Weight">
                            <template #suffix>
                                kg
                            </template>
                        </a-input>
                    </div>
                    <div class="flex flex-col mt-7">
                        <a-input v-model:value="userName" placeholder="Length">
                            <template #suffix>
                                cm
                            </template>
                        </a-input>
                    </div>
                    <div class="flex flex-col mt-7">
                        <a-input v-model:value="userName" placeholder="Width" aut-resize>
                            <template #suffix>
                                cm
                            </template>
                        </a-input>
                    </div>
                    <div class="flex flex-col mt-7">
                        <a-input v-model:value="userName" placeholder="Height" :class="twMerge()">
                            <template #suffix>
                                cm
                            </template>
                        </a-input>
                    </div>
                </div>
            </div>
            <div class="p-10 lg:w-2/3 bg-white border rounded-2xl">

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
