<script setup>
import {useForm, usePage, Link} from '@inertiajs/vue3';
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import {ref, watch} from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import EditOriginAddressModal from "@/Pages/Shipments/Partials/EditOriginAddressModal.vue";
import Modal from "@/Components/Modal.vue";
import EditDestinationAddressModal from "@/Pages/Shipments/Partials/EditDestinationAddressModal.vue";
import EditPackageDetailsModal from "@/Pages/Shipments/Partials/EditPackageDetailsModal.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {toast} from "vue3-toastify";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";


const activeKey = ref(['1']);
const isEditOriginAddressOpen  = ref(false);
const isEditDestinationAddressOpen  = ref(false);
const isEditPackageDetailsOpen  = ref(false);
const datePicker  = ref(null);
const confirm = ref(false);




watch(activeKey, val => {
  //console.log(val);
});

const page = usePage();

defineProps({
  shipment: Array,
  origin: Object,
  destination: Object,
  insurance_options: Array,
  shipping_rate_log: Array,
  origin_location: Array,
  destination_location: Array,
  item_category: Object,
  countries: Array,
  origin_states: Array,
  destination_states: Array,
  origin_cities: Array,
  destination_cities: Array,
  categories: Array,
  user: Object
});

const form  = useForm({
    insurance: 0,
    option_id: '',
    shipment_id: page.props.shipment.id,
    shipment_date: '',
    pickup_time: '',
    invoice_number: '',
    invoice_date: '',
});

const pay = () => {
    //do all validation here
  let hasError = false;
    if (form.option_id === '') {
      hasError = true;
      form.errors.option_id = 'Select a shipment option';
    }

    if (form.insurance === 0) {
      hasError = true;
      form.errors.insurance = 'Select a insurance option';
    }

    if (form.shipment_date.length === 0) {
      hasError = true;
      form.errors.shipment_date = 'Select a shipment date';
    }

    if (!hasError) {
      form.post(route('admin.shipment.book'), {
        onError: () => toast.error(form.errors.message)
      });
    }
}

let naira = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'NGN'
});

const setOption = () => {
    form.option_id = form.option.id;
    //console.log(form.option_id);
}

const formattedCurrency = () => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  });
}


const date = new Date();
const tomorrow = date.setDate(date.getDate() + 1);
const tenDays = date.setDate(date.getDate() + 10);


const showInvoiceForm = () => {
  let show = false;
  if(page.props.origin_location.country === 'Nigeria' && page.props.destination_location.country !== 'Nigeria') {
    show  = true;
  } else if(page.props.origin_location.country !== 'Nigeria' && page.props.destination_location.country !== 'Nigeria') {
    show = true;
  } else if(page.props.origin_location.country !== 'Nigeria' && page.props.destination_location.country === 'Nigeria') {
    show = true;
  } else if(page.props.origin_location.country === 'Nigeria' && page.props.destination_location.country === 'Nigeria') {
    show = false;
  }
  return show;
}

</script>

<template>
    <DashboardLayout page-title="Shipment Checkout">
        <h1 class="mt-5 font-bold text-xl">Complete Shipment</h1>
        <div class="p-5 shadow rounded-xl border mt-10">
          <div class=""><span class="font-bold">User: </span>{{ user.first_name + " " + user.last_name}}</div>
        </div>
        <div class="flex sm:flex-row-reverse flex-col-reverse justify-between gap-10 mt-10">
          <form @submit.prevent="pay" class="card sm:p-10 p-5 bg-white rounded-xl shadow hover:shadow-lg duration-500 sm:w-1/2 w-full">
            <div >
              <h1 class="font-bold text-md">Shipment Options</h1>
              <div class="grid sm:grid-cols-1 grid-col-1 gap-5 mb-10 mt-10">
                <div v-for="item in shipping_rate_log" class="card shadow-sm border border-gray-100 rounded-xl">
                  <label class="cursor-pointer">
                    <div class="p-4 font-bold flex flex-row justify-between bg-gray-50 rounded-t-xl">
                      <div class="font-medium text-primary text-sm">{{ item.product_name }}</div>
                      <input type="radio" class="text-primary border-primary focus:ring-opacity-30 focus:ring-primary" :value="item" v-model="form.option" v-on:change="setOption"/>
                    </div>
                    <hr class="border-gray-100">
                    <div class="p-4">
                      <p class="text-sm">Amount: {{ naira.format(parseFloat(item.charge_before_tax)) }}</p>
                      <p class="text-sm">Tax: {{ naira.format(parseFloat(item.charge_tax)) }}</p>
                    </div>
                    <hr class="border-gray-100">
                    <div class="p-4">
                      <div class="font-bold text-sm">{{ naira.format(item.total_charge) }}</div>
                    </div>
                  </label>
                </div>
              </div>

              <InputError class="mt-3" :message="form.errors.option_id" />

              <div v-if="form.option_id > 0" class="transition-all duration-500">
                <div class="mt-5 flex sm:flex-row flex-col justify-between gap-x-10">
                  <div class="w-full">
                    <InputLabel value="Planned Shipment Date and Time" />
                    <VueDatePicker
                        v-model="form.shipment_date"
                        :disabled-week-days="[6, 0]"
                        :min-time="{ hours: 9, minutes: 0 }"
                        :max-time="{ hours: 15, minutes: 0 }"
                        class="mt-3"></VueDatePicker>
                    <InputError :message="form.errors.shipment_date" />
                  </div>
                </div>

                <div class="mt-5">
                  <InputLabel value="Choose an Insurance Option" />
                  <SelectInput v-model="form.insurance" :options="insurance_options" class="mt-3" />
                  <InputError :message="form.errors.insurance" />
                </div>

                <div class="mt-10 shadow-md rounded-xl" v-show="showInvoiceForm()">
                  <h3 class="mt-5 px-5 py-3">Import/Export Details</h3>
                  <hr>
                  <div class="mb-3 mt-3 px-5">
                    <InputLabel value="Invoice Number" />
                    <TextInput type="text" v-model="form.invoice_number" class="mt-3" />
                    <InputError :message="form.errors.invoice_number" />
                  </div>

                  <div class="mb-3 px-5 pb-5">
                    <InputLabel value="Invoice Date" />
                    <VueDatePicker
                        v-model="form.invoice_date"
                        :disabled-week-days="[6, 0]"
                        :enable-time-picker="false"
                        :min-time="{ hours: 9, minutes: 0 }"
                        :max-time="{ hours: 16, minutes: 0 }"
                        class="mt-3"></VueDatePicker>
                    <InputError :message="form.errors.invoice_date" />
                  </div>
                </div>

              </div>

              <div class="mt-10 p-5 shadow-sm border border-gray-100 rounded-xl duration-500 transition-all flex flex-row justify-between items-center" v-if="form.insurance.length > 0">
                <div>
                  <div class="text-lg font-bold">{{ insurance_options[form.insurance - 1].name }}</div>
                  <div>Covers damages upto {{ naira.format(insurance_options[form.insurance - 1].cover) }}</div>
                </div>
                <div class="text-green-600 bg-background px-4 py-1 rounded-full">{{ naira.format(insurance_options[form.insurance - 1].amount) }}</div>
              </div>

              <div class="mt-10">
                <h3 class=" text-lg">Payment breakdown</h3>
                <p class="text-sm">Shipping Amount:
                  <span  v-if="form.option">{{ naira.format(form.option.total_charge.replace(',','')) }}</span>
                  <span  v-else>0.00</span>
                </p>
                <p class="text-sm">Insurance: <span v-if="form.insurance.length > 0">{{ naira.format(insurance_options[form.insurance - 1].amount) }}</span><span v-else>0.00</span> </p>
              </div>
              <div class="mt-10 flex  items-center gap-x-4 p-5 bg-orange-50 text--orange-500 rounded-xl">
                <Checkbox
                    id="postcode"
                    v-model="confirm"
                    placeholder=""
                    autocomplete="postcode"/>
                <InputLabel value="Please confirm that every detail is correct" class="text-orange-500"/>
              </div>
              <input type="hidden" v-model="form.option_id">
              <div class="mt-10">
                <PrimaryButton type="submit" class="duration-500" :class="{ 'opacity-25': form.processing || !confirm }" :disabled="!confirm || form.processing">
                  Book & Pay
                  <span v-if="form.option">{{ naira.format(parseFloat(form.option?.total_charge) + parseFloat(insurance_options[form.insurance - 1]?.amount)) }}</span>
                  <span v-else>0.00</span>
                </PrimaryButton>

              </div>
            </div>
          </form>
          <div class="sm:w-1/2 w-full">
            <div class="card shadow rounded-xl p-5 mb-10">
              <div>
                <div class="border-l border-dashed">
                  <div>
                    <div class="flex flex-row gap-x-3">
                      <div class="p-0.5 bg-red-500 h-5 w-5 -ml-2.5"></div>
                      <h3 class="text-sm">Pickup From</h3>
                    </div>
                    <div class="flex flex-row justify-between gap-x-10 rounded-xl ml-5 mt-5">
                      <div class="card bg-white duration-500 w-full">
                        <div class="flex justify-between items-center">
                          <h3 class="font-semibold text-sm">{{ origin.contact_name }}</h3>
                        </div>
                        <p class="flex flex-col">
                          <span class="text-sm text-primary">{{ origin.contact_phone }}</span>
                          <span class="text-sm">{{ origin.contact_email}}</span>
                        </p>
                        <p class="mt-3 text-sm"> {{ origin.address_1 }}</p>
                        <p class="text-sm"> {{ origin.landmark }}</p>
                        <div class="flex gap-x-10">
                          <div class="text-primary font-bold text-sm">{{ origin_location.city }}, {{ origin_location.state }}, {{ origin_location.country }}</div>
                        </div>
                      </div>
                      <div class="mb-3 mt-3 flex flex-row justify-end h-max">
                        <Link :href="route('admin.shipment.origin', shipment.id)">
                          <button class="shadow p-2 rounded-full bg-background/30 flex gap-x-3 flex-row justify-between items-center">
                            <span class="text-primary text-xs">Change</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                          </button>
                        </Link>
                      </div>
                    </div>
                  </div>
                  <div class="mt-10">
                    <div class="flex flex-row gap-x-3">
                      <div class="p-0.5 bg-green-400 h-5 w-5 -ml-2.5"></div>
                      <h3 class="text-sm">Deliver To</h3>
                    </div>
                    <div class="flex flex-row justify-between gap-x-10 rounded-xl ml-5 mt-5">
                      <div class="card bg-white duration-500 w-full">
                        <div class="flex justify-between items-center">
                          <h3 class="text-sm font-semibold">{{ destination.contact_name }}</h3>
                        </div>
                        <p class="flex flex-col">
                          <span class="text-sm text-primary">{{ destination.contact_phone }}</span>
                          <span class="text-sm">{{ destination.contact_email}}</span>
                        </p>
                        <p class="mt-3 text-sm"> {{ destination.address_1 }}</p>
<!--                        <p class="text-sm"> {{ destination.landmark }}</p>-->
                        <div class="flex gap-x-10">
                          <div class="text-primary text-sm font-bold">{{ destination_location.city }}, {{ destination_location.state }}, {{ destination_location.country }}</div>
                        </div>
                      </div>
                      <div class="mb-3 mt-3 flex flex-row justify-end h-max">
                        <Link :href="route('admin.shipment.destination', shipment.id)">
                          <button class="shadow p-2 rounded-full bg-background/30 flex gap-x-3 flex-row justify-between items-center">
                            <span class="text-primary text-xs">Change</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                          </button>
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <div class="relative flex flex-row overflow-x-auto duration-500 shadow p-5 rounded-xl hover:shadow-xl">
              <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
                <tbody v-for="item in shipment.shipment_items" :key="item.id" >
                <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                  <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                    Item Category
                  </th>
                  <td class="px-6 py-2">
                    {{ item_category.name }}
                  </td>
                </tr>
                <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                  <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                    Value
                  </th>
                  <td class="px-6 py-2">
                    {{ naira.format(item.value) }}
                  </td>
                </tr>
                <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                  <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                    Description
                  </th>
                  <td class="px-6 py-2">{{ item.description }}</td>
                </tr>
                <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                  <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                    Quantity
                  </th>
                  <td class="px-6 py-2 uppercase">
                    {{ item.quantity }} Nos.
                  </td>
                </tr>
                <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                  <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                    Weight
                  </th>
                  <td class="px-6 py-2 uppercase">
                    {{ item.weight }}kg
                  </td>
                </tr>
                <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                  <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                    Height
                  </th>
                  <td class="px-6 py-2 uppercase">
                    {{ item.height }}cm
                  </td>
                </tr>
                <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                  <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                    Length
                  </th>
                  <td class="px-6 py-2 uppercase">
                    {{ item.length }}cm
                  </td>
                </tr>
                <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                  <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                    Width
                  </th>
                  <td class="px-6 py-2 uppercase">
                    {{ item.width }}cm
                  </td>
                </tr>
                </tbody>
              </table>
              <div class="mb-3 mt-3 flex flex-row justify-end h-max">
                <Link :href="route('shipment.package-information', shipment.id)">
                  <button class="shadow p-2 rounded-full bg-background/30 flex gap-x-3 flex-row justify-between items-center">
                    <span class="text-primary text-xs">Change</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                    </svg>
                  </button>
                </Link>
              </div>
            </div>
          </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>

</style>
