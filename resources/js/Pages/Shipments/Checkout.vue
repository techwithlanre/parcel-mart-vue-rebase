<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm, usePage} from '@inertiajs/vue3';
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import { ref, watch} from 'vue';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import EditOriginAddressModal from "@/Pages/Shipments/Partials/EditOriginAddressModal.vue";
import Modal from "@/Components/Modal.vue";

const activeKey = ref(['1']);
const isEditOriginAddressOpen  = ref(false);
const isEditDestinationAddressOpen  = ref(false);

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
    origin_cities: Array,
});

const form  = useForm({
    insurance: 0,
    option: '',
    option_id: '',
    shipment_id: page.props.shipment.id,
    shipment_date: ''
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

const today = new Date();
</script>

<template>
    <AuthenticatedLayout page-title="Shipment Checkout">
        <Head title="Shipment Checkout"/>
        <h1 class="mt-5 font-bold text-xl">Complete Shipment</h1>
        <div class="flex sm:flex-row flex-col-reverse justify-between gap-10 mt-10">
          <form @submit.prevent="pay" class="card sm:p-10 p-5 bg-white shadow hover:shadow-lg duration-300 sm:w-1/2 w-full">
            <div >
              <h1 class="font-bold text-md">Shipment Options</h1>
              <div class="grid sm:grid-cols-2 grid-col-1 gap-5 mb-10">
                <div v-for="item in shipping_rate_log" class="card shadow">
                  <label class="cursor-pointer">
                    <div class="p-4 font-bold flex flex-row justify-between bg-gray-50 rounded-t-xl">
                      <div class="font-medium text-primary text-sm">{{ item.product_name }}</div>
                      <input type="radio" class="text-primary border-primary focus:ring-opacity-30 focus:ring-primary" :value="item" v-model="form.option" v-on:change="setOption"/>
                    </div>
                    <hr>
                    <div class="p-4">
                      <p class="text-sm">Amount: {{ item.amount_before_tax }}</p>
                      <p class="text-sm">Tax: {{ item.tax }}</p>
                    </div>
                    <hr>
                    <div class="p-4">
                      <div class="font-bold text-sm">{{ naira.format(item.total_amount.replace(',', '')) }}</div>
                    </div>
                  </label>
                </div>
              </div>

              <InputError class="mt-3" :message="form.errors.option_id" />

              <div class="mt-5">
                <InputLabel value="Choose an Insurance Option" />
                <SelectInput v-model="form.insurance" :options="insurance_options" class="mt-3" />
                <InputError :message="form.errors.insurance" />
              </div>

              <div class="mt-5">
                <InputLabel value="Planned Shipment Date" />
                <TextInput type="date" v-model="form.shipment_date" class="mt-3" required :min='today' :max="2023-12-31" />
                <InputError :message="form.errors.shipment_date" />
              </div>

              <div class="mt-5 p-5 card border duration-300 transition-all flex flex-row justify-between items-center" v-if="form.insurance.length > 0">
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
            <a-collapse v-model:activeKey="activeKey" class="border-0 shadow-md">
              <a-collapse-panel key="1" header="Package Information">
                <div class="relative overflow-x-auto duration-300">
                  <div class="mb-3 mt-3 flex flex-row justify-end">
                    <button @click="isEditOriginAddressOpen = true" class="shadow p-2 rounded-full bg-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                      </svg>
                    </button>
                  </div>
                  <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <tbody v-for="item in shipment.shipment_items" :key="item.id" >
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Item Category
                      </th>
                      <td class="px-6 py-2">
                        {{ item_category.name }}
                      </td>
                    </tr>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Value
                      </th>
                      <td class="px-6 py-2">
                        {{ naira.format(item.value) }}
                      </td>
                    </tr>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Description
                      </th>
                      <td class="px-6 py-2">{{ item.description }}</td>
                    </tr>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Quantity
                      </th>
                      <td class="px-6 py-2">
                        {{ item.quantity }} Nos.
                      </td>
                    </tr>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Weight
                      </th>
                      <td class="px-6 py-2">
                        {{ item.weight }}kg
                      </td>
                    </tr>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Height
                      </th>
                      <td class="px-6 py-2">
                        {{ item.height }}cm
                      </td>
                    </tr>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                      <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Length
                      </th>
                      <td class="px-6 py-2">
                        {{ item.length }}cm
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </a-collapse-panel>
              <a-collapse-panel key="2" header="Sender Information">
                <div class="flex flex-row justify-between gap-x-10 shadow p-5 rounded-xl">
                  <div class="card bg-white duration-300 w-full">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-semibold">{{ origin.contact_name }}</h3>
                    </div>
                    <p class="flex gap-x-10">
                      <span class="text-sm text-primary">{{ origin.contact_phone }}</span>
                      <span class="text-sm">{{ origin.contact_email}}</span>
                    </p>
                    <hr class="mt-2">
                    <p class="mt-5"> {{ origin.address_1 }}</p>
                    <p class=""> {{ origin.landmark }}</p>
                    <div class="flex gap-x-10">
                      <div class="text-blue-950 font-bold">{{ origin_location.city }}, {{ origin_location.state }}, {{ origin_location.country }}</div>
                    </div>
                  </div>
                  <div class="mb-3 mt-3 flex flex-row justify-end h-max">
                    <button @click="isEditOriginAddressOpen = true" class="shadow p-2 rounded-full bg-primary flex flex-row justify-center items-center gap-x-3">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-background">
                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32L19.513 8.2z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </a-collapse-panel>
              <a-collapse-panel key="3" header="Receiver Information">
                <div class="flex flex-row justify-between gap-x-10 shadow p-5 rounded-xl">
                  <div class="card bg-white duration-300 w-full">
                    <div class="flex justify-between items-center">
                      <h3 class="text-lg font-semibold">{{ destination.contact_name }}</h3>
                    </div>
                    <p class="flex gap-x-10">
                      <span class="text-sm text-primary">{{ destination.contact_phone }}</span>
                      <span class="text-sm">{{ destination.contact_email}}</span>
                    </p>
                    <hr class="mt-2">
                    <p class="mt-5"> {{ destination.address_1 }}</p>
                    <p class=""> {{ destination.landmark }}</p>
                    <div class="flex gap-x-10">
                      <div class="text-blue-950 font-bold">{{ destination_location.city }}, {{ destination_location.state }}, {{ destination_location.country }}</div>
                    </div>
                  </div>
                  <div class="mb-3 mt-3 flex flex-row justify-end h-max">
                    <button @click="isEditDestinationAddressOpen = true" class="shadow p-2 rounded-full bg-primary flex flex-row justify-center items-center gap-x-3">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-background">
                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32L19.513 8.2z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </a-collapse-panel>
            </a-collapse>
          </div>
        </div>
        <Modal :show="isEditOriginAddressOpen">
          <div>
            <div class="flex flex-row justify-between items-start p-5">
              <div class="">
                <h3>Edit Origin Address</h3>
                <p class="text-sm">Use this form to edit origin address for this booking</p>
              </div>
              <div>
                <svg @click="isEditOriginAddressOpen = false" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
            <hr>
            <div class="p-5">
              <EditOriginAddressModal :address="origin" :countries="countries" :states="origin_states" :cities="origin_cities" />
            </div>
          </div>
        </Modal>
        <Modal :show="isEditDestinationAddressOpen">
          <div>
            <div class="flex flex-row justify-between items-start p-5">
              <div class="">
                <h3>Edit Destination Address</h3>
                <p class="text-sm">Use this form to edit origin address for this booking</p>
              </div>
              <div>
                <svg @click="isEditDestinationAddressOpen = false" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
            <hr>
            <div class="p-5">
              <EditOriginAddressModal :address="destination" :countries="countries" :states="origin_states" :cities="origin_cities" />
            </div>
          </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
