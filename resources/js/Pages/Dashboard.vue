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
import { notification } from 'ant-design-vue';

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
                name: this.$page.props.auth.user.first_name + " " + this.$page.props.auth.user.last_name,
                email: this.$page.props.auth.user.email,
                phone: this.$page.props.auth.user.phone,
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
                length: "",
                commercial_invoice: "",
                parking_list: "",
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

      submitQuote: function () {
        this.form.post(route('request-quote'), {
          onSuccess: () => {
            this.form.reset()
            this.toggleQuote(false);
            notification['success']({
              message: 'success',
              description: 'Request has been submitted, You will be contacted via email'
            });
          },
        });
      }
    },
}
</script>

<script setup>

import {Link, useForm} from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import {notification} from "ant-design-vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {toast} from "vue3-toastify";

const isTrackingOpen = ref(false);

const trackForm = useForm({
  number: ''
})


const trackShipment = () => {
  trackForm.post(route('shipment.track'), {
    onFinish: ()  => {
      trackForm.reset();
    },
  })
}
</script>

<template>
    <DashboardLayout page-title="Dashboard">
        <div class="flex lg:flex-row flex-col gap-x-5 gap-y-10 mt-10">
            <div class="w-full">
                <div class="flex flex-col gap-y-10 lg:flex-row gap-x-10">
                    <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
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
                    <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
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
                            <div class="text-3xl">
                              <a :href="route('wallet.index')">
                                <PrimaryButton>Fund Wallet</PrimaryButton>
                              </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-y-10 sm:flex-row gap-x-10 mt-10">
                    <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
                        <Link :href="route('shipment.origin')" class="flex flex-col">
                            <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                <img src="../../images/parcel.png" alt="" class="h-10 w-10">
                            </div>

                            <div class="flex flex-row justify-between items-center">
                                <div class="flex flex-col">
                                    <h1 class="font-medium text-md text-gray-600 mt-5">Book Shipments</h1>
                                    <h3 class="text-sm text-gray-400">Send and receive item(s)</h3>
                                </div>
                                <div class="text-primary"></div>
                            </div>
                        </link>
                    </div>
                    <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
                        <a href="javascript:void(0)" id="" class="flex flex-col" @click="toggleQuote(true)">
                            <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                <img src="../../images/price-tag.png" alt="" class="h-10 w-10">
                            </div>

                            <div class="flex flex-row justify-between items-center">
                                <div class="flex flex-col">
                                    <h1 class="font-medium text-md text-gray-600 mt-5">Get Pricing</h1>
                                    <h3 class="text-sm text-gray-400">Request a quote</h3>
                                </div>
                                <div class="text-primary"></div>
                            </div>
                        </a>
                    </div>
                    <a href="javascript:void(0)" @click="isTrackingOpen = true" class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
                        <div class="flex gap-5 items-center">
                            <div class="flex flex-col">
                                <div class="border bg-background/50 rounded-full h-16 w-16 flex justify-center items-center">
                                    <img src="../../images/location.png" alt="" class="h-10 w-10">
                                </div>

                                <h1 class="font-medium text-md text-gray-600 mt-5">Tracking</h1>
                                <h3 class="text-sm text-gray-400">Track your shipments</h3>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <!-- Table-->
        <div>
            <div class="mt-20">
                <h1 class="text-lg">Recent Shipments</h1>
                <div class="relative overflow-x-auto shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Origin</th>
                            <th scope="col" class="px-6 py-3">Destination</th>
                            <th scope="col" class="px-6 py-3">Number</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="log.length > 0" v-for="item in log" class="bg-white border-b hover:bg-gray-50">
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
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ item.number }}
                            </td>
                            <td class="px-6 py-4">
                               <span
                                   :class="{'bg-blue-100 text-blue-800' : item.status ==='processing',
                                  'bg-orange-100 text-orange-800' : item.status ==='pending', 'bg-green-100 text-green-800' : item.status ==='delivered'}"
                                   class="px-3 rounded-xl py-1">{{ item.status}}</span>
                            </td>
                          <td class="px-6 py-4">
                            <Link :href="route('shipment.origin', item.id)" v-if="item.status === 'pending'" class="text-primary font-medium hover:text-green-600">Continue</Link>
                            <Link :href="route('shipment.details', item.id)" v-else class="btn btn-sm rounded-xl bg-green-400 text-white px-5 py-1 text-sm font-medium hover:text-green-600">View</Link>
                          </td>
                        </tr>
                        <tr v-else>
                          <td colspan="5" class="px-6 py-4 text-center sm:text-lg text-sm">
                            You have not shipped any package with us! Click the <Link class="font-semibold text-primary" :href="route('shipment.origin')">here</Link> to start your shipment
                          </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <Modal :show="isQuoteOpen">
          <div class="">
            <div class="relative w-full max-w-2xl max-h-full duration-500">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t">
                  <div class="text-xl font-semibold text-gray-900">
                    Get Pricing
                    <p class="text-gray-500 font-normal text-sm mt-2">Use this form to get request for a quote</p>
                  </div>
                  <button type="button" class="text-gray-400 bg-transparent duration-500 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center sdark:hover:bg-gray-600 sdark:hover:text-white" data-modal-hide="getQuoteModal">
                    <svg @click="isQuoteOpen = false" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </button>
                </div>
                <!-- Modal body -->
                <div class="">
                  <form @submit.prevent="submitQuote">
                    <div class="px-6 mb-5">
                      <div class="gap-y-3 mt-3 gap-x-5 w-full">
                        <div>
                          <TextInput
                              id="quantity"
                              class="mt-1 w-full"
                              hidden=""
                              required
                              autocomplete="off"
                              readonly
                              v-model="form.name"/>
                          <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <div class="">
                          <TextInput
                              id="quantity"
                              class="mt-1 w-full"
                              hidden=""
                              required
                              autocomplete="off"
                              readonly
                              v-model="form.phone"/>
                          <InputError class="mt-2" :message="form.errors.phone" />
                        </div>
                        <div class="">
                          <TextInput
                              id="quantity"
                              hidden=""
                              class="mt-1 w-full"
                              autocomplete="off"
                              readonly
                              v-model="form.email"/>
                          <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                      </div>
                      <div class="mt-4">
                        <div class="grid lg:grid-cols-1 gap-y-3 mt-3 gap-x-5 w-full">
                          <div>
                            <InputLabel value="Origin Country" class="mb-2" />
                            <SelectInput
                                place-holder="Select Country"
                                class="block w-full"
                                v-model="form.country_from"
                                required
                                :options="countries"
                                v-on:change="getOriginStates" />
                            <InputError class="mt-2" :message="form.errors.country_from" />
                          </div>
                          <div>
                            <InputLabel value="Origin State" class="mb-2" />
                            <SelectInput
                                place-holder="Select State"
                                class="block w-full"
                                required
                                v-model="form.state_from"
                                :options="originStates"
                                v-on:change="getOriginCities" />
                            <InputError class="mt-2" :message="form.errors.state_from" />
                          </div>
                          <div>
                            <InputLabel value="Origin City" class="mb-2" />
                            <SelectInput
                                place-holder="Select City"
                                class="block w-full"
                                required
                                v-model="form.city_from"
                                :options="originCities" />
                            <InputError class="mt-2" :message="form.errors.city_from" />
                          </div>
                        </div>
                      </div>
                      <div class="mt-4">
                        <div class="grid lg:grid-cols-1 gap-y-3 gap-x-5 w-full">
                          <div>
                            <InputLabel value="Destination Country" class="mb-2" />
                            <SelectInput
                                place-holder="Select Country"
                                class="block w-full"
                                v-model="form.country_to"
                                required
                                :options="countries"
                                v-on:change="getDestinationStates"
                            />
                            <InputError class="mt-2" :message="form.errors.country_to" />
                          </div>

                          <div>
                            <InputLabel value="Destination State" class="mb-2" />
                            <SelectInput
                                place-holder="Select State"
                                class="block w-full"
                                required
                                v-model="form.state_to"
                                :options="destinationStates"
                                v-on:change="getDestinationCities" />
                            <InputError class="mt-2" :message="form.errors.state_to" />
                          </div>

                          <div>
                            <InputLabel value="Destination City" class="mb-2" />
                            <SelectInput
                                place-holder="Select City"
                                class="block w-full"
                                required
                                v-model="form.city_to"
                                :options="destinationCities"
                            />
                            <InputError class="mt-2" :message="form.errors.city_to" />
                          </div>
                        </div>
                      </div>

                      <div class="grid lg:grid-cols-3 gap-y-3 gap-x-5 mt-4">
                        <div>
                          <InputLabel value="Quantity" class="mb-2" />
                          <TextInput
                              id="quantity"
                              type="number"
                              class=" w-full"
                              required
                              autocomplete="off"
                              placeholder="Quantity"
                              v-model="form.quantity"/>
                          <InputError class="mt-2" :message="form.errors.quantity" />
                        </div>

                        <div>
                          <InputLabel value="Weight" class="mb-2" />
                          <TextInput
                              id="email"
                              type="number"
                              class="w-full"
                              required
                              autocomplete="off"
                              placeholder="Weight"
                              v-model="form.weight"/>
                          <InputError class="mt-2" :message="form.errors.weight" />
                        </div>

                        <div>
                          <InputLabel value="Length" class="mb-2" />
                          <TextInput
                              id=""
                              type="number"
                              class="w-full"
                              required
                              autocomplete="off"
                              placeholder="Length"
                              v-model="form.length"/>
                          <InputError class="mt-2" :message="form.errors.length" />
                        </div>
                      </div>
                      <div class="grid lg:grid-cols-2 gap-y-3 mt-4 gap-x-5">
                        <div>
                          <InputLabel value="Width" class="mb-2" />
                          <TextInput
                              id=""
                              type="number"
                              class="w-full"
                              required
                              autocomplete="off"
                              placeholder="Width"
                              v-model="form.width"/>
                          <InputError class="mt-2" :message="form.errors.width" />
                        </div>
                        <div>
                          <InputLabel value="Height" class="mb-2" />
                          <TextInput
                              id=""
                              type="number"
                              class="w-full"
                              required
                              autocomplete="off"
                              placeholder="Height"
                              v-model="form.height"/>
                          <InputError class="mt-2" :message="form.errors.height" />
                        </div>
                      </div>
                      <div class="grid lg:grid-cols-2 gap-y-3 mt-4 gap-x-5">
                        <div>
                          <InputLabel value="Commercial Invoice" class="mb-2" />
                          <TextInput
                              id=""
                              type="file"
                              class="mt-1 w-full"
                              @input="form.commercial_invoice = $event.target.files[0]"
                              />
                          <InputError class="mt-2" :message="form.errors.commercial_invoice" />
                        </div>
                        <div>
                          <InputLabel value="Parking List" class="mb-2" />
                          <TextInput
                              id=""
                              type="file"
                              class="mt-1 w-full"
                              @input="form.parking_list = $event.target.files[0]"
                              />
                          <InputError class="mt-2" :message="form.errors.parking_list" />
                        </div>
                      </div>
                    </div>
                    <div class="flex justify-end items-center p-6 space-x-2 border-t border-gray-200 rounded-b sdark:border-gray-600">
                      <button type="submit"
                          class="inline-flex justify-center rounded-md border border-transparent bg-background px-4 py-2 text-sm font-medium text-primary hover:text-white
                                         hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-all duration-500"
                          :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Get Pricing
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </Modal>
         <Modal :show="isTrackingOpen">
          <div class="relative w-full max-w-2xl max-h-full duration-500">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow">
              <!-- Modal header -->
              <div class="flex items-start justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900 sdark:text-white">
                  Track Shipment
                  <p class="text-gray-500 font-normal text-sm">Use this form to track your shipment, enter your tracking number below</p>
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center sdark:hover:bg-gray-600 sdark:hover:text-white" data-modal-hide="trackingModal">
                  <svg @click="isTrackingOpen = false" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
                       stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </button>
              </div>
              <!-- Modal body -->
              <form @submit.prevent="trackShipment">
                <div class="p-6">
                  <InputLabel value="Tracking Number" />
                  <TextInput v-model="trackForm.number" required type="text" class="mt-2" placeholder="Enter tracking number" />
                  <InputError class="mt-2" :message="trackForm.errors.number" />
                </div>
                <!-- Modal footer -->
                <div class="flex justify-end items-center p-6 space-x-2 border-t border-gray-200 rounded-b sdark:border-gray-600">
                  <button
                      type="submit"
                      class="inline-flex justify-center rounded-md border border-transparent bg-background px-4 py-2 text-sm font-medium text-primary hover:text-white
                                         hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-all duration-500"
                      :class="{ 'opacity-25': trackForm.processing }" :disabled="trackForm.processing">
                    Continue
                  </button>
                </div>
              </form>
            </div>
          </div>
        </Modal>
    </DashboardLayout>
</template>

<style>

</style>
