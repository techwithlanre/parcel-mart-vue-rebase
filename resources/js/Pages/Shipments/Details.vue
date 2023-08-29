<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm, usePage} from '@inertiajs/vue3';
import {toast} from "vue3-toastify";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import {computed} from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const page = usePage()
defineProps({
    shipment: Array,
    origin: Object,
    destination: Object,
    insurance_options: Array,
    shipping_rate_log: Object,
    origin_location: Array,
    destination_location: Array,
    item_category: Object,
    tracking_log: Array
});

const form  = useForm({
    insurance: 0,
    option: '',
    shipment_id: page.props.shipment.id
});

const pay = () => {
    //do all validation here
    if (form.option === '') {
        return toast.error('Select a shipment option');
    }

    form.post(route('shipment.book'), {

    });
}

const image = computed(() => {
  if (page.props.shipment.provider === 'aramex') return "https://www.aramex.com/Sitefinity/WebsiteTemplates/aramex/App_Themes/aramex/Images/Aramex%20logo%20English.webp";
  if (page.props.shipment.provider === 'dhl') return "https://www.dhl.com/content/dam/dhl/global/core/images/logos/dhl-logo.svg";
})

let naira = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'NGN'
});

</script>

<template>
    <DashboardLayout class="capitalize" :page-title="'Shipment Details' + ' - ' + shipment.provider">
        <Head title="Shipment Checkout"/>
        <div class="flex lg:flex-row flex-col justify-between gap-10 mt-10">
            <div class="card rounded-xl shadow border border-gray-50 shadow-background/50 bg-white w-full">
                <h1 class="font-bold text-xl px-5 pt-3 pb-1">Shipment Status</h1>
                <hr>
                <div class="flex flex-col space-y-4 mt-5 px-5">
                  <div>Tracking Number: <span class="font-bold text-primary">{{ shipment.number }}</span></div>
                  <div class="flex items-center">
                    Carrier: &nbsp;
                    <div class="flex flex-row space-x-3 items-center">
                      <img :src="image" class="h-3" alt="">
                    </div>
                  </div>

                </div>
                <div class="px-5">
                    <h2 class="sr-only">Steps</h2>
                    <div class="mt-10">
                        <div class="overflow-hidden rounded-full bg-gray-200">
                            <div class="h-2 w-1/3 rounded-full bg-primary"></div>
                        </div>
                        <ol class="mt-4 grid grid-cols-3 text-sm font-medium text-gray-500">
                            <li class="flex items-center justify-start text-primary sm:gap-1.5">
                                <span class="sm:inline"> Processing </span>
                            </li>

                            <li class="flex items-center justify-center sm:gap-1.5">
                                <span class="sm:inline"> In Transit </span>
                            </li>

                            <li class="flex items-center justify-end sm:gap-1.5">
                                <span class="sm:inline"> Delivered </span>
                            </li>
                        </ol>
                    </div>
                </div>

            </div>
          <div class="flex lg:flex-row flex-col justify-between gap-10 w-full">
            <div class="shadow border border-gray-50 shadow-background/50 w-full bg-white rounded-xl">
              <div>
                <div class="card mt-5">
                  <h1 class="font-bold text-xl px-5">Payment Summary</h1>
                  <hr>
                  <div class="px-5 text-gray-600 text-sm mt-5">
                    <p>Amount: {{ naira.format(shipping_rate_log.amount_before_tax) }}</p>
                    <p>Tax: {{ naira.format(shipping_rate_log.tax) }}</p>
                    <p>Insurance: {{ naira.format(insurance_options[0].amount) }}</p>
                  </div>

                  <div class="p-4">
                    <div class="font-bold">Total Shipment Amount:  {{ shipping_rate_log.total_amount }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <div class="flex sm:flex-row flex-col gap-10 w-full mt-10">
        <div class="card shadow shadow-background/50 rounded-xl p-5 w-full ">
          <div>
            <div class="border-l border-dashed">
              <div>
                <div class="flex flex-row gap-x-3">
                  <div class="p-0.5 bg-red-500 h-5 w-5 -ml-2.5"></div>
                  <h3 class="text-sm">Pickup From</h3>
                </div>
                <div class="flex flex-row justify-between gap-x-10 rounded-xl ml-5 mt-5">
                  <div class="card bg-white duration-300 w-full">
                    <div class="flex justify-between items-center">
                      <h3 class="font-semibold text-sm">{{ origin.contact_name }}</h3>
                    </div>
                    <p class="flex gap-x-10">
                      <span class="text-sm text-primary">{{ origin.contact_phone }}</span>
                      <span class="text-sm">{{ origin.contact_email}}</span>
                    </p>
                    <p class="mt-3 text-sm"> {{ origin.address_1 }}</p>
                    <!--                        <p class="text-sm"> {{ origin.landmark }}</p>-->
                    <div class="flex gap-x-10">
                      <div class="text-primary font-bold text-sm">{{ origin_location.city }}, {{ origin_location.state }}, {{ origin_location.country }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-10">
                <div class="flex flex-row gap-x-3">
                  <div class="p-0.5 bg-green-400 h-5 w-5 -ml-2.5"></div>
                  <h3 class="text-sm">Deliver To</h3>
                </div>
                <div class="flex flex-row justify-between gap-x-10 rounded-xl ml-5 mt-5">
                  <div class="card bg-white duration-300 w-full">
                    <div class="flex justify-between items-center">
                      <h3 class="text-sm font-semibold">{{ destination.contact_name }}</h3>
                    </div>
                    <p class="flex gap-x-10">
                      <span class="text-sm text-primary">{{ destination.contact_phone }}</span>
                      <span class="text-sm">{{ destination.contact_email}}</span>
                    </p>
                    <p class="mt-3 text-sm"> {{ destination.address_1 }}</p>
                    <!--                        <p class="text-sm"> {{ destination.landmark }}</p>-->
                    <div class="flex gap-x-10">
                      <div class="text-primary text-sm font-bold">{{ destination_location.city }}, {{ destination_location.state }}, {{ destination_location.country }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card rounded-xl shadow shadow-background/50 w-full bg-white">
          <h1 class="font-bold text-xl p-5">Shipment Information</h1>
          <div class="relative overflow-x-auto rounded-2xl">
            <table class="w-full text-sm text-left text-gray-500">
              <tbody v-for="item in shipment.shipment_items" :key="item.id" >
              <tr class="bg-white">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                  Item Category
                </th>
                <td class="px-6 py-2">
                  {{ item_category.name }}
                </td>
              </tr>
              <tr class="bg-white">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                  Value
                </th>
                <td class="px-6 py-2">
                  {{ naira.format(item.value) }}
                </td>
              </tr>
              <tr class="bg-white">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                  Description
                </th>
                <td class="px-6 py-2">
                  {{ item.description }}
                </td>
              </tr>
              <tr class="bg-white">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                  Quantity
                </th>
                <td class="px-6 py-2">
                  {{ item.quantity }} Nos.
                </td>
              </tr>
              <tr class="">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                  Weight
                </th>
                <td class="px-6 py-2">
                  {{ item.weight }}KG
                </td>
              </tr>
              <tr class="">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                  Height
                </th>
                <td class="px-6 py-2">
                  {{ item.height }}CM
                </td>
              </tr>
              <tr class="">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap">
                  Length
                </th>
                <td class="px-6 py-2">
                  {{ item.length }}CM
                </td>
              </tr>
              <tr class="">
                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap">
                  Pickup Number
                </th>
                <td class="px-6 py-2">
                  {{ shipping_rate_log.pickup_number }}
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="card bg-white border border-gray-50 shadow shadow shadow-background/50 rounded-xl mt-10">
        <div>
          <h1 class="font-bold text-xl px-5 pt-3 pb-1">Shipment History</h1>
          <hr>
        </div>
        <div class="p-5">
          <ol v-if="tracking_log.length > 0" class="relative border-l border-gray-200">
            <li class="mb-10 ml-4" v-for="item in tracking_log">
              <div class="absolute w-3 h-3 bg-primary rounded-full mt-1.5 -left-1.5 border border-white sdark:border-gray-900 sdark:bg-gray-700"></div>
              <time class="mb-1 text-sm font-normal leading-none text-primary sdark:text-gray-500">{{ item.update_datetime }} - <span class="font-semibold">{{ item.update_description }}</span></time>
              <h3 class="text-lg font-semibold text-gray-900 sdark:text-white">{{ item.update_location }}</h3>
              <p class="mb-4 text-base font-normal text-gray-500 sdark:text-gray-400"></p>
              <p class="mb-4 text-base font-normal text-gray-500 sdark:text-gray-400">Gross Weight: {{ item.gross_weight }}kg</p>
              <p class="mb-4 text-base font-normal text-gray-500 sdark:text-gray-400">Chargeable Weight: {{ item.gross_weight }}kg</p>
            </li>
          </ol>
          <div v-else class="flex justify-center">No shipment history yet.</div>
        </div>
      </div>
    </DashboardLayout>
</template>

<style scoped>

</style>
