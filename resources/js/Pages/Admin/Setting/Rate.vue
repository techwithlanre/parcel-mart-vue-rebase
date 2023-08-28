<script setup>
import {Head, Link, useForm} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {TrashIcon, StarIcon} from "@heroicons/vue/24/solid/index.js";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {ref} from "vue";
import {twMerge} from "tailwind-merge";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import {toast} from "vue3-toastify";
import Pagination from "@/Components/Pagination.vue";
import TopNav from "@/Components/TopNav.vue";

import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const tabs = [
    { name: "Description", icon: WalletIcon, route: "description" },
    { name: "Measurement", icon: DocumentIcon, route: "measurement" }
];

const count = ref(5);
const visible = ref(false);
const confirmLoading = ref(false);

const showModal = () => {
    visible.value = true;
};

const handleOk = () => {
    confirmLoading.value = true;
    setTimeout(() => {
        submit();
        visible.value = false;
        confirmLoading.value = false;

    }, 2000);
};

const form = useForm({
    description: ''
});

defineProps({
    providers: Array
});

const submit = () => {
    form.post(route('shipping.setting.description.post'), {
        onFinish: () => toast.success('Description Added'),
    })
}

</script>

<template>
    <Head title="Shipping Setting::Description" />
    <DashboardLayout page-title="Margin Rate Setting">
<!--        <TopNav :tabs="tabs"/>-->
        <div>
            <div class="mt-10 border rounded-3xl bg-white">
                <table class="w-full">
                    <thead class="rounded-t-3xl">
                    <tr class="text-sm font-medium text-gray-700 border-b border-gray-200 px-5">
                        <td class="font-bold p-7">Name</td>
                        <td class="font-bold p-7">Status</td>
                        <td class="font-bold p-7">Profit Margin</td>
                        <td class="font-bold p-7">Action</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="providers.length > 0" v-for="item in providers" class="hover:bg-gray-100 transition-colors group px-5">
                        <td class="text-sm p-7">{{ item.name }}</td>
                        <td class="text-sm p-7">{{ item.status }}</td>
                        <td class="text-sm p-7">{{ item.profit_margin }}%</td>
                        <td class="text-sm p-7"><Link :href="route('setting.rate.edit', item.id)" class="text-primary">Edit</Link></td>
                    </tr>
                    <tr v-else class="hover:bg-gray-100 transition-colors group border-b px-5">
                        <td class="text-sm p-7 text-center text-gray-400" colspan="5">No data in table</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </DashboardLayout>

</template>

<style scoped>

</style>
