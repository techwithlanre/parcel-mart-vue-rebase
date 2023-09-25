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
import Parcel from "../../../../images/parcel.png";
import Pagination from "@/Components/Pagination.vue";
import { notification } from 'ant-design-vue';
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  data() {
    return {
      chartData: {
        labels: this.userRetentionLabels(),
        datasets: [ {
          label: 'User Retention Percentage',
          backgroundColor: '#008083',
          data: this.userRetentionPercentages()
        }]
      },
      chartOptions: {
        responsive: true
      },
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
    InputError, InputLabel, TextInput, Link, Head, Parcel, Bar
  },

  computed: {
    barStyles () {
      return {
        color: 'orange',
        position: 'relative'
      }
    },
  },

  props: {
    log: Array,
    balance: String,
    shipmentCount: String,
    countries: Array,
    totalWalletBalance: String,
    totalUsersCount: String,
    businessUsersCount: String,
    individualUsersCount: String,
    userRetention: Array
  },
  methods:{
    userRetentionPercentages() {
      return this.userRetention.map(item => {
        return item.percentage;
      })
    },
    userRetentionLabels() {
      return this.userRetention.map(item => {
        return item.day;
      })
    },
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
      this.form.post(route('send.quote.form'), {
        onFinish: () => {
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

import {useForm} from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import {notification} from "ant-design-vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import TopNav from "@/Components/TopNav.vue";
import {UsersIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";

const isTrackingOpen = ref(false);

const trackForm = useForm({
  number: ''
})


const trackShipment = () => {
  trackForm.post(route('shipment.track'), {
    onFinish: ()  => {
      trackForm.reset();
      isTrackingOpen.value = false;
    },
  })
}

const tabs = [
  { name: "Users Analytics", icon: WalletIcon, route: "/admin/analytics/users", permissionKey: ['read-shipment-report']},
  { name: "Shipments Analytics", icon: WalletIcon, route: "/admin/analytics/shipments", permissionKey: ['read-shipment-report']},
  { name: "Payments Analytics", icon: WalletIcon, route: "/admin/analytics/payments", permissionKey: ['read-payment-report']},
  { name: "Geographic Analytics", icon: WalletIcon, route: "/admin/reports/payments", permissionKey: ['read-payment-report']},
];
</script>

<template>
  <DashboardLayout page-title="Analytics Dashboard">
    <TopNav :tabs="tabs" class="bg-white shadow shadow-background rounded-xl" />
    <div class="flex lg:flex-row flex-col gap-x-5 gap-y-10 mt-10">
      <div class="w-full">
        <div class="flex flex-col gap-y-10 lg:flex-row gap-x-10">
          <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
            <div class="flex gap-5 items-center">
              <div class="h-12 w-12">
                <img src="../../../../images/shipment.png" alt="">
              </div>
              <div class="flex flex-col">
                <h1 class="font-bold">Shipments</h1>
                <h3 class="text-xl font-bold">{{ shipmentCount }}</h3>
              </div>
            </div>
          </div>
          <div v-if="!page.props.auth.user.is_admin" class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
            <div class="flex justify-between">
              <div class="flex gap-5 items-start">
                <div class="h-12 w-12">
                  <img src="../../../../images/wallet.png" alt="">
                </div>
                <div class="flex flex-col">
                  <h1 class="font-bold">Wallet Balance</h1>
                  <h3 class="text-xl font-bold">{{ balance }}</h3>
                </div>
              </div>
              <div class="text-3xl"></div>
            </div>
          </div>
          <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
            <div class="flex justify-between">
              <div class="flex gap-5 items-start">
                <div class="h-12 w-12">
                  <img src="../../../../images/wallet.png" alt="">
                </div>
                <div class="flex flex-col">
                  <h1 class="font-bold">Total Users</h1>
                  <h3 class="text-xl font-bold">{{ totalUsersCount }}</h3>
                </div>
              </div>
              <div class="text-3xl"></div>
            </div>
          </div>
          <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
            <div class="flex justify-between">
              <div class="flex gap-5 items-start">
                <div class="h-12 w-12">
                  <img src="../../../../images/wallet.png" alt="">
                </div>
                <div class="flex flex-col">
                  <h1 class="font-bold">Individual Users</h1>
                  <h3 class="text-xl font-bold">{{ individualUsersCount }}</h3>
                </div>
              </div>
              <div class="text-3xl"></div>
            </div>
          </div>
          <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
            <div class="flex justify-between">
              <div class="flex gap-5 items-start">
                <div class="h-12 w-12">
                  <img src="../../../../images/wallet.png" alt="">
                </div>
                <div class="flex flex-col">
                  <h1 class="font-bold">Business Users</h1>
                  <h3 class="text-xl font-bold">{{ businessUsersCount }}</h3>
                </div>
              </div>
              <div class="text-3xl"></div>
            </div>
          </div>
          <div class="p-5 bg-white rounded-2xl w-full shadow hover:shadow-lg duration-500">
            <div class="flex justify-between">
              <div class="flex gap-5 items-start">
                <div class="h-12 w-12">
                  <img src="../../../../images/wallet.png" alt="">
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

        <div class="grid grid-cols-2 my-10 gap-10">
          <div class="p-5 shadow shadow-background rounded-xl ">
            <h3 class="font-bold mb-5">User Retention Chart</h3>
            <Bar
                id="my-chart-id"
                :options="chartOptions"
                :data="chartData"
                :style="barStyles"
            />
          </div>
        </div>
      </div>
    </div>
    <!-- Table-->
  </DashboardLayout>
</template>

<style>

</style>
