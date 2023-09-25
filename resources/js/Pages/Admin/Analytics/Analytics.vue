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
import { Line } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement,
    LineElement,)

export default {
  data() {
    return {
      salesChartData: {
        labels: this.monthlySalesLabels(),
        datasets: [ {
          label: 'Monthly Sales',
          backgroundColor: '#f87979',
          data: this.monthlySalesData()
        }]
      },
      shipmentChartOptions: {
        responsive: true
      },
      shipmentChartData: {
        labels: this.monthlyShipmentsLabels(),
        datasets: [ {
          label: 'Monthly Shipments',
          backgroundColor: '#797c47',
          data: this.monthlyShipmentsData()
        }]
      },
      salesChartOptions: {
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
    InputError, InputLabel, TextInput, Link, Head, Parcel, Bar, Line, PointElement, LineElement
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
    monthlySales: Array,
    monthlyShipments: Array,
  },
  methods:{
    monthlySalesData() {
      return this.monthlySales.map(item => {
        return item.total_sales;
      })
    },
    monthlySalesLabels() {
      return this.monthlySales.map(item => {
        return item.month;
      })
    },

    monthlyShipmentsData() {
      return this.monthlyShipments.map(item => {
        return item.shipment_count;
      })
    },
    monthlyShipmentsLabels() {
      return this.monthlyShipments.map(item => {
        return item.month;
      })
    },
  },
}
</script>

<script setup>

import {useForm} from "@inertiajs/vue3";
import {notification} from "ant-design-vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

</script>

<template>
  <DashboardLayout page-title="Analytics Dashboard">
    <div class="flex lg:flex-row flex-col gap-x-5 gap-y-10">
      <div class="w-full">
        <div class="grid grid-cols-2 gap-10">
          <div class="p-5 shadow shadow-background rounded-xl border ">
            <h3 class="font-bold mb-5">Monthly Sales Chart</h3>
            <Line
                id="sales-chart"
                :options="salesChartOptions"
                :data="salesChartData"
            />
          </div>
          <div class="p-5 shadow shadow-background rounded-xl border">
            <h3 class="font-bold mb-5">Monthly Shipments Chart</h3>
            <Line
                id="sales-chart"
                :options="shipmentChartOptions"
                :data="shipmentChartData"
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
