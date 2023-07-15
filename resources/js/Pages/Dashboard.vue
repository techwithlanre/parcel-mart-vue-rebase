<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import {Tab, TabGroup, TabList, TabPanel, TabPanels, TransitionRoot} from "@headlessui/vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Parcel from "../../images/parcel.png";
import Pagination from "@/Components/Pagination.vue";


export default {
    data() {
        return {
            isOpen: false,
            isQuoteOpen: false,
            selectedOriginAddress: 0,
            selectedDestinationAddress: 0,
            selectedTab: 0,
            originCities: [],
            destinationCities: [],
            state: 0,
            originStates: [],
            destinationStates: [],
            originDisabled: false,
            page: usePage(),
            form: useForm({
                country_from: "",
                state_from: "",
                city_from: "",
                country_to: "",
                state_to: "",
                city_to: "",
                quantity: "",
                weight: "",
                width: "",
                height: "",
                length: ""
            })
        }
    },
    components: {
        Pagination,
        TransitionRoot, TextAreaInput, PrimaryButton, SelectInput, Tab,
        TabPanel, TabPanels, TabList, TabGroup, AuthenticatedLayout,
        InputError, InputLabel, TextInput, Link, Head, Parcel
    },

    props: {
        log: Array,
        balance: String,
        shipmentCount: String,
        countries: Array,
        totalWalletBalance: String,
        totalUsersCount: String,
        businessUsersCount: String,
        individualUsersCount: String
    },
    methods:{
        toggleQuote(value) {
            this.isQuoteOpen = value
        },

        closeQuote() {
            this.isQuoteOpen = false
        },

        closeModal() {
            this.isOpen = false
        },

        openModal() {
            this.isOpen = true
        },

        getOriginStates: function() {
            axios.get('/api/states/' + this.form.country_from).then(function(response){
                this.originStates = response.data.states;
            }.bind(this));
        },

        getOriginCities: function() {
            axios.get('/api/cities/' + this.form.state_from).then(function(response){
                this.originCities = response.data.cities;
            }.bind(this));
        },

        getDestinationStates: function() {
            axios.get('/api/states/' + this.form.country_to).then(function(response){
                this.destinationStates = response.data.states;
            }.bind(this));
        },

        getDestinationCities: function() {
            axios.get('/api/cities/' + this.form.state_to).then(function(response){
                this.destinationCities = response.data.cities;
            }.bind(this));
        },

        changeTab(index) {
            this.selectedTab = index
        },

        validateForm(index) {
            this.changeTab(index)
        },

        initialize: function () {
            this.form.post(route('shipment.initialize'), {

            })
        },
    },
}
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout page-title="Dashboard">
        <div class="flex lg:flex-row flex-col gap-x-5 gap-y-10 mt-10">
            <div class="w-full">
                <div class="flex flex-col gap-y-10 lg:flex-row gap-x-10">
                    <div class="p-5 bg-white rounded-2xl w-full border shadow-sm hover:shadow-lg duration-300">
                        <div class="flex gap-5 items-center">
                            <div class="h-12 w-12">
                                <img src="../../images/shipment.png" alt="">
                            </div>
                            <div class="flex flex-col">
                                <h1 class="font-bold">Shipments</h1>
                                <h3 class="text-xl font-bold">{{ shipmentCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div v-if="page.props.auth.role !== 'admin'" class="p-5 bg-white rounded-2xl w-full border shadow-sm hover:shadow-lg duration-300">
                        <div class="flex justify-between">
                            <div class="flex gap-5 items-start">
                                <div class="h-12 w-12">
                                    <img src="../../images/wallet.png" alt="">
                                </div>
                                <div class="flex flex-col">
                                    <h1 class="font-bold">Wallet Balance</h1>
                                    <h3 class="text-xl font-bold">{{ balance }}</h3>
                                </div>
                            </div>
                            <div class="text-3xl"></div>
                        </div>
                    </div>
                    <div v-if="page.props.auth.role === 'admin'" class="p-5 bg-white rounded-2xl w-full border shadow-sm hover:shadow-lg duration-300">
                        <div class="flex justify-between">
                            <div class="flex gap-5 items-start">
                                <div class="h-12 w-12">
                                    <img src="../../images/wallet.png" alt="">
                                </div>
                                <div class="flex flex-col">
                                    <h1 class="font-bold">Total Users</h1>
                                    <h3 class="text-xl font-bold">{{ totalUsersCount }}</h3>
                                </div>
                            </div>
                            <div class="text-3xl"></div>
                        </div>
                    </div>
                    <div v-if="page.props.auth.role === 'admin'" class="p-5 bg-white rounded-2xl w-full border shadow-sm hover:shadow-lg duration-300">
                        <div class="flex justify-between">
                            <div class="flex gap-5 items-start">
                                <div class="h-12 w-12">
                                    <img src="../../images/wallet.png" alt="">
                                </div>
                                <div class="flex flex-col">
                                    <h1 class="font-bold">Individual Users</h1>
                                    <h3 class="text-xl font-bold">{{ individualUsersCount }}</h3>
                                </div>
                            </div>
                            <div class="text-3xl"></div>
                        </div>
                    </div>
                    <div v-if="page.props.auth.user.is_admin" class="p-5 bg-white rounded-2xl w-full border shadow-sm hover:shadow-lg duration-300">
                        <div class="flex justify-between">
                            <div class="flex gap-5 items-start">
                                <div class="h-12 w-12">
                                    <img src="../../images/wallet.png" alt="">
                                </div>
                                <div class="flex flex-col">
                                    <h1 class="font-bold">Business Users</h1>
                                    <h3 class="text-xl font-bold">{{ businessUsersCount }}</h3>
                                </div>
                            </div>
                            <div class="text-3xl"></div>
                        </div>
                    </div>
                    <div v-if="page.props.auth.role === 'admin'" class="p-5 bg-white rounded-2xl w-full border shadow-sm hover:shadow-lg duration-300">
                        <div class="flex justify-between">
                            <div class="flex gap-5 items-start">
                                <div class="h-12 w-12">
                                    <img src="../../images/wallet.png" alt="">
                                </div>
                                <div class="flex flex-col">
                                    <h1 class="font-bold">Total Wallet Balance</h1>
                                    <h3 class="text-xl font-bold">{{ totalWalletBalance }}</h3>
                                </div>
                            </div>
                            <div class="text-3xl"></div>
                        </div>
                    </div>
                </div>

                <div v-if="page.props.auth.role !== 'admin'" class="flex flex-col gap-y-10 lg:flex-row gap-x-10 mt-10">
                    <div class="p-5 bg-white rounded-2xl w-full border shadow-sm hover:shadow-lg duration-300">
                        <Link :href="route('shipment.start')" class="flex flex-col">
                            <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                <img src="../../images/parcel.png" alt="" class="h-10 w-10">
                            </div>

                            <div class="flex flex-row justify-between items-center">
                                <div class="flex flex-col">
                                    <h1 class="font-medium text-md text-gray-600 mt-5">Book Shipments</h1>
                                    <h3 class="text-xs text-gray-400">Send and receive item(s)</h3>
                                </div>
                                <div class="text-primary"></div>
                            </div>
                        </link>
                    </div>
                    <div class="p-5 bg-white rounded-2xl w-full border shadow-md hover:shadow-lg duration-300">
                        <a href="javascript:void(0)" class="flex flex-col" @click="toggleQuote(true)">
                            <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                <img src="../../images/parcel.png" alt="" class="h-10 w-10">
                            </div>

                            <div class="flex flex-row justify-between items-center">
                                <div class="flex flex-col">
                                    <h1 class="font-medium text-md text-gray-600 mt-5">Get Pricing</h1>
                                    <h3 class="text-xs text-gray-400">Request a quote</h3>
                                </div>
                                <div class="text-primary"></div>
                            </div>
                        </a>
                    </div>

                    <Link :href="route('shipment.index')" class="p-5 bg-white rounded-2xl w-full border shadow-sm hover:shadow-lg duration-300">
                        <div class="flex gap-5 items-center">
                            <div class="flex flex-col">
                                <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                    <img src="../../images/parcel.png" alt="" class="h-10 w-10">
                                </div>

                                <h1 class="font-medium text-md text-gray-600 mt-5">Tracking</h1>
                                <h3 class="text-xs text-gray-400">Track your shipments</h3>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
        <div>
            <div class="mt-20" v-if="page.props.auth.role === 'admin'">
                <h1 class="text-lg">Recent Shipments</h1>
                <div class="relative overflow-x-auto shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Origin</th>
                            <th scope="col" class="px-6 py-3">Destination</th>
                            <th scope="col" class="px-6 py-3">Number</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in log" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
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
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ item.number }}
                            </td>
                            <td class="px-6 py-4">
                                {{ item.status}}
                            </td>
                            <td class="px-6 py-4">
                                <Link :href="route('shipment.checkout', item.id)" v-if="item.status === 'pending'" class="text-primary font-medium hover:text-green-600">Checkout</Link>
                                <Link :href="route('shipment.details', item.id)" v-else class="text-primary font-medium hover:text-green-600">View</Link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <a-modal v-model:visible="isQuoteOpen" title="Get Quote" @ok="" width="50%" wrap-class-name="full-modal" body-style="@apply ">
                <div>
                    <form>
                        <div class="mt-4">
                            <InputLabel value="From" />
                            <div class="grid lg:grid-cols-3 gap-x-5 w-full">
                                <SelectInput
                                    place-holder="Select Country"
                                    class="block w-full"
                                    v-model="form.country_from"
                                    required
                                    :options="countries"
                                    v-on:change="getOriginStates"
                                />

                                <SelectInput
                                    place-holder="Select State"
                                    class="block w-full"
                                    required
                                    v-model="form.state_from"
                                    :options="originStates"
                                    v-on:change="getOriginCities"
                                />

                                <SelectInput
                                    place-holder="Select State"
                                    class="block w-full"
                                    required
                                    v-model="form.city_from"
                                    :options="originCities"
                                />
                            </div>
                        </div>
                        <div class="mt-4">
                            <InputLabel value="To" />
                            <div class="grid lg:grid-cols-3 gap-x-5 w-full">
                                <SelectInput
                                    place-holder="Select Country"
                                    class="block w-full"
                                    v-model="form.country_to"
                                    required
                                    :options="countries"
                                    v-on:change="getDestinationStates"
                                />

                                <SelectInput
                                    place-holder="Select State"
                                    class="block w-full"
                                    required
                                    v-model="form.state_to"
                                    :options="destinationStates"
                                    v-on:change="getDestinationCities"
                                />

                                <SelectInput
                                    place-holder="Select State"
                                    class="block w-full"
                                    required
                                    v-model="form.city_to"
                                    :options="destinationCities"
                                />
                            </div>
                        </div>

                        <div class="grid lg:grid-cols-3 gap-x-5 mt-5">
                            <TextInput
                                id="quantity"
                                type="number"
                                class="mt-1 w-full"
                                required
                                autocomplete="off"
                                placeholder="Quantity"
                                v-model="form.quantity"/>

                            <TextInput
                                id="email"
                                type="number"
                                class="mt-1 w-full"
                                required
                                autocomplete="off"
                                placeholder="Weight"
                                v-model="form.weight"/>
                            <TextInput
                                id=""
                                type="number"
                                class="mt-1 w-full block"
                                required
                                autocomplete="off"
                                placeholder="Length"
                                v-model="form.length"/>
                        </div>
                        <div class="grid lg:grid-cols-3 gap-x-5 mt-5">
                            <TextInput
                                id=""
                                type="number"
                                class="mt-1 w-full"
                                required
                                autocomplete="off"
                                placeholder="Width"
                                v-model="form.width"/>

                            <TextInput
                                id=""
                                type="number"
                                class="mt-1 w-full"
                                required
                                autocomplete="off"
                                placeholder="Height"
                                v-model="form.height"/>
                        </div>
                    </form>
                </div>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>

<style>

</style>
