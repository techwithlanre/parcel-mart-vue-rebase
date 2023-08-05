<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm, usePage} from '@inertiajs/vue3';
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import {onMounted, ref, watch} from 'vue';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import EditOriginAddressModal from "@/Pages/Shipments/Partials/EditOriginAddressModal.vue";
import Modal from "@/Components/Modal.vue";
import EditDestinationAddressModal from "@/Pages/Shipments/Partials/EditDestinationAddressModal.vue";
import EditPackageDetailsModal from "@/Pages/Shipments/Partials/EditPackageDetailsModal.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'


const activeKey = ref(['1']);
const isEditOriginAddressOpen  = ref(false);
const isEditDestinationAddressOpen  = ref(false);
const isEditPackageDetailsOpen  = ref(false);
const datePicker  = ref(null);




watch(activeKey, val => {
  console.log(val);
});

const page = usePage()
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
});

const form  = useForm({
    insurance: 0,
    option: '',
    option_id: '',
    shipment_id: page.props.shipment.id,
    shipment_date: '',
    pickup_time: '',
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
      form.post(route('shipment.book'), {

      });
    }
}

let naira = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'NGN'
});

const setOption = () => {
    form.option_id = form.option.id;
    console.log(form.option_id);
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

</script>

<template>
    <AuthenticatedLayout page-title="Shipment Checkout">
        <Head title="Shipment Checkout"/>
        <h1 class="mt-5 font-bold text-xl">Complete Shipment</h1>
        <div class="flex sm:flex-row-reverse flex-col-reverse justify-between gap-10 mt-10">
          <form @submit.prevent="pay" class="card sm:p-10 p-5 bg-white rounded-xl shadow hover:shadow-lg duration-300 sm:w-1/2 w-full">
            <div >
              <h1 class="font-bold text-md">Shipment Options</h1>
              <div class="grid sm:grid-cols-1 grid-col-1 gap-5 mb-10 mt-10">
                <div v-for="item in shipping_rate_log" class="card shadow-sm border border-gray-100 rounded-xl">
<!--                  {{ item.id }}-->
                  <label class="cursor-pointer">
                    <div class="p-4 font-bold flex flex-row justify-between bg-gray-50 rounded-t-xl">
                      <div class="font-medium text-primary text-sm">{{ item.product_name }}</div>
                      <input type="radio" class="text-primary border-primary focus:ring-opacity-30 focus:ring-primary" :value="item" v-model="form.option" v-on:change="setOption"/>
                    </div>
                    <hr class="border-gray-100">
                    <div class="p-4">
                      <p class="text-sm">Amount: {{ naira.format(parseFloat(item.amount_before_tax.replace(',', ''))) }}</p>
                      <p class="text-sm">Tax: {{ naira.format(parseFloat(item.tax.replace(',', ''))) }}</p>
                    </div>
                    <hr class="border-gray-100">
                    <div class="p-4">
                      <div class="font-bold text-sm">{{ naira.format(item.total_amount.replace(',', '')) }}</div>
                    </div>
                  </label>
                </div>
              </div>

              <InputError class="mt-3" :message="form.errors.option_id" />

              <div v-if="form.option_id > 0" class="transition-all duration-300">
                <div class="mt-5 flex sm:flex-row flex-col justify-between gap-x-10">
                  <div class="w-full">
                    <InputLabel value="Planned Shipment Date and Time" />
                    <VueDatePicker
                        v-model="form.shipment_date"
                        :disabled-week-days="[6, 0]"
                        :min-time="{ hours: 9, minutes: 0 }"
                        :max-time="{ hours: 16, minutes: 0 }"
                        class="mt-3"></VueDatePicker>
                    <InputError :message="form.errors.shipment_date" />
                  </div>
                </div>

                <div class="mt-5">
                  <InputLabel value="Choose an Insurance Option" />
                  <SelectInput v-model="form.insurance" :options="insurance_options" class="mt-3" />
                  <InputError :message="form.errors.insurance" />
                </div>
              </div>

              <div class="mt-10 p-5 shadow-sm border border-gray-100 rounded-xl duration-300 transition-all flex flex-row justify-between items-center" v-if="form.insurance.length > 0">
                <div>
                  <div class="text-lg font-bold">{{ insurance_options[form.insurance - 1].name }}</div>
                  <div>Covers damages upto {{ naira.format(insurance_options[form.insurance - 1].cover) }}</div>
                </div>
                <div class="text-green-600 bg-background px-4 py-1 rounded-full">{{ naira.format(insurance_options[form.insurance - 1].amount) }}</div>
              </div>

              <div class="mt-10">
                <h3 class=" text-lg">Payment breakdown</h3>
                <p>Shipping Amount:
                  <span  v-if="form.option">{{ naira.format(form.option.total_amount.replace(',','')) }}</span>
                  <span  v-else>0.00</span>
                </p>
                <p>Insurance: <span v-if="form.insurance.length > 0">{{ naira.format(insurance_options[form.insurance - 1].amount) }}</span><span v-else>0.00</span> </p>
              </div>
              <input type="hidden" v-model="form.option_id">
              <div class="mt-10">
                <PrimaryButton  type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Pay
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
                      <div class="mb-3 mt-3 flex flex-row justify-end h-max">
                        <button @click="isEditOriginAddressOpen = true" class="shadow p-2 rounded-full bg-background/30 flex gap-x-3 flex-row justify-between items-center">
                          <span class="text-primary text-xs">Change</span>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                          </svg>
                        </button>
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
                      <div class="mb-3 mt-3 flex flex-row justify-end h-max">
                        <button @click="isEditDestinationAddressOpen = true" class="shadow p-2 rounded-full bg-background/30 flex gap-x-3 flex-row justify-between items-center">
                          <span class="text-primary text-xs">Change</span>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <a-collapse v-model:activeKey="activeKey" class="border-0 shadow-md">
              <a-collapse-panel key="1" header="Package Information">
                <div class="relative flex flex-row overflow-x-auto duration-300">
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
                      <td class="px-6 py-2">
                        {{ item.quantity }} Nos.
                      </td>
                    </tr>
                    <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                        Weight
                      </th>
                      <td class="px-6 py-2">
                        {{ item.weight }}kg
                      </td>
                    </tr>
                    <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                        Height
                      </th>
                      <td class="px-6 py-2">
                        {{ item.height }}cm
                      </td>
                    </tr>
                    <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                        Length
                      </th>
                      <td class="px-6 py-2">
                        {{ item.length }}cm
                      </td>
                    </tr>
                    </tbody>
                  </table>
                  <div class="mb-3 mt-3 flex flex-row justify-end h-max">
                    <button @click="isEditPackageDetailsOpen = true" class="shadow p-2 rounded-full bg-background/30 flex gap-x-3 flex-row justify-between items-center">
                      <span class="text-primary text-xs">Change</span>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                      </svg>
                    </button>
                  </div>
                </div>
              </a-collapse-panel>
            </a-collapse>
          </div>
        </div>
        <Modal :show="isEditOriginAddressOpen">
          <EditOriginAddressModal @is-modal-open="(value) => isEditOriginAddressOpen = value"  :shipment-id="page.props.shipment.id"  :origin-address="origin" :destination-address="destination" :countries="countries" :states="origin_states" :cities="origin_cities" :shipment="shipment" />
        </Modal>
        <Modal :show="isEditDestinationAddressOpen">
          <EditDestinationAddressModal @is-modal-open="(value) => isEditDestinationAddressOpen = value" :address="destination" :shipment-id="page.props.shipment.id" :countries="countries" :origin-address="origin" :destination-address="destination" :states="destination_states" :cities="destination_cities" :shipment="shipment" />
        </Modal>
        <Modal :show="isEditPackageDetailsOpen">
          <EditPackageDetailsModal @is-modal-open="(value) => isEditPackageDetailsOpen = value" :categories="categories" :address="destination" :shipment-id="page.props.shipment.id" :countries="countries" :origin-address="origin" :destination-address="destination" :states="destination_states" :cities="destination_cities" :shipment="shipment" />
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
