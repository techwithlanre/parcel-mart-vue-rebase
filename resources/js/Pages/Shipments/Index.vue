<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";
import {Link, Head, useForm, usePage} from "@inertiajs/vue3"
import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import parcel from "../../../images/parcel.png";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {twMerge} from "tailwind-merge";

const activeKey = ref('1');
const tabs = [
    { name: "Parcels", icon: WalletIcon, route: "profile" },
    { name: "Documents", icon: DocumentIcon, route: "settlement-details" }
];

defineProps({
    log: Array,
    shipments: Array,
    shipmentsCount: String
})

const trackForm = useForm({
    number: ''
})

const filterForm = useForm({
    status: ''
})


const trackShipment = () => {
    trackForm.post(route('shipment.track'), {
        onFinish: () => trackForm.reset(),
    })
}

const shipmentStatusOptions = [
    {
        id: "all",
        name: "all"
    },
    {
        id: "pending",
        name: "pending"
    },
    {
        id: "processing",
        name: "processing"
    },
    {
        id: "delivered",
        name: "delivered"
    },
    {
        id: "cancelled",
        name: "cancelled"
    },
];

const dataSource = [
        {
            key: "1",
            name: 'Mike',
            age: 32,
            address: '10 Downing Street',
        },
        {
            key: "2",
            name: 'John',
            age: 42,
            address: '10 Downing Street',
        },
    ];

const columns = [
        {
            title: 'Name',
            dataIndex: 'name',
            key: 'name',
        },
        {
            title: 'Age',
            dataIndex: 'age',
            key: 'age',
        },
        {
            title: 'Address',
            dataIndex: 'address',
            key: 'address',
        },
    ];

const page = usePage();

const handleFilter = () => {
    axios.get(route('shipment.filter'), { params: { status: filterForm.status }}).then((response) => {
        page.props.log = response.data
    })
}
</script>

<template>
    <AuthenticatedLayout page-title="Shipments">
        <Head title="Start Shipment" />
        {{ ship}}
        <div class="flex flex-col w-full gap-5 mt-10">
            <div class="flex flex-row gap-5 justify-end items-end">
                <PrimaryButton data-modal-target="defaultModal" data-modal-toggle="defaultModal" :class="twMerge('w-max bg-white text-red-900 border-2 border-[#008083]')" style="color: #008083" type="button">
                    Track Shipment
                </PrimaryButton>
                <Link :href="route('shipment.start')">
                    <PrimaryButton class="w-max bg-primary text-white" :class="{ 'opacity-25': trackForm.processing }" :disabled="trackForm.processing">New Shipment</PrimaryButton>
                </Link>
            </div>
            <div id="defaultModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-2xl max-h-full duration-300">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Track Shipment
                                <p class="text-gray-500 font-normal text-xs">Use this form to track your shipment, enter your tracking number below</p>
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="defaultModal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <form @submit.prevent="trackShipment">
                            <div class="p-6 space-y-6">
                                    <TextInput v-model="trackForm.number" required type="text" class="mt-3" placeholder="Enter tracking number" />
                            </div>
                            <!-- Modal footer -->
                            <div class="flex justify-end items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                <PrimaryButton class="" :class="{ 'opacity-25': trackForm.processing }" :disabled="trackForm.processing">Track</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="p-10 w-full bg-white border rounded-2xl mt-10" v-if="log.length > 0 || parseInt(shipmentsCount) > 0" >
                <div class="flex lg:flex-row flex-col justify-between mb-10 items-start">
                    <h1 class="text-lg">Shipment History</h1>
                    <div>
                        <InputLabel value="Filter" class="mb-3" />
                        <a-select
                            :class="twMerge('w-[200px] rounded-lg')"
                            ref="select"
                            v-model:value="filterForm.status"
                            @focus="focus"
                            @change="handleFilter"
                        >
                            <a-select-option v-for="item in shipmentStatusOptions" :value="item.id">{{ item.name }}</a-select-option>
                        </a-select>

                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-xs table-pin-rows table-pin-cols">
                        <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3">Origin</th>
                            <th scope="col" class="px-6 py-3">Destination</th>
                            <th scope="col" class="px-6 py-3">Number</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="log.length > 0" v-for="item in log" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ item.origin['name']}}</div>
                                <span class="text-gray-600">{{ item.origin['phone'] }}</span>
                                <div>{{ item.origin['address_1']}}</div>
                                <div>{{item.origin['city']}}, {{ item.origin['country']}}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ item.destination['name']}}</div>
                                <span class="text-gray-600">{{ item.destination['phone'] }}</span>
                                <div>{{ item.destination['address_1']}}</div>
                                <div>{{item.destination['city']}}, {{ item.destination['country']}}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ item.number }}
                            </td>
                            <td class="px-6 py-4">
                              <span :class="{ 'bg-orange-400 px-3 py-1 rounded-full text-white font-medium': item.status ==='processing' }">{{ item.status}}</span>

                            </td>
                            <td class="px-6 py-4">
                                <Link :href="route('shipment.checkout', item.id)" v-if="item.status === 'pending'" class="text-primary font-medium hover:text-green-600">Checkout</Link>
                                <Link :href="route('shipment.details', item.id)" v-else class="text-primary font-medium hover:text-green-600"  >
                                  View
                                </Link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="shipments.links" />
            </div>
            <div v-else class="flex flex-row justify-center mt-20">
                <div class="flex flex-col items-center">
                    <img :src="parcel" alt="" class="h-52">
                    <h1 class="mt-5 text-center">You have not shipped any package with us! Click the button below to start your shipment</h1>
                    <Link :href="route('shipment.start')" class="mt-5"><PrimaryButton>Start Shipment</PrimaryButton></Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
