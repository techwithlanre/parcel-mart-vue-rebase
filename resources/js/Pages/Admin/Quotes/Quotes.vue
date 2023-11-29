<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm, Link} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Helper from "../../../Helpers/Helper.js";

const props = defineProps({
  quotes: Array
});

const isOpenModal = ref(false);
const currentlyEditing = ref(0);

const form = useForm({
  amount: '',
})

const convertForm = useForm({

})

const openModal = (id) => {
  isOpenModal.value = true;
  currentlyEditing.value = id;
}

const submitSetPrice = () => {
  form.put(route('admin.set-quote-price', currentlyEditing.value), {
    onSuccess: () => {
      form.reset();
      currentlyEditing.value = 0;
      isOpenModal.value = false;
    }
  })
}



const convertQuoteToShipment = (quote_id) => {
  convertForm.post(route('admin.convert-quote-to-shipment', quote_id));
}

</script>

<template>
  <DashboardLayout page-title="Quotes">
    <div class="card p-5 bg-white">
      <div class="overflow-x-auto shadow-background shadow mt-10 rounded-xl">
        <!-- Header -->
        <div class="px-6 py-4 grid gap-1 md:flex md:justify-between md:items-center border-b border-gray-200">
          <div>
            <h2 class="text-xl font-semibold text-gray-800">
              Quotes List
            </h2>
          </div>
        </div>
        <!-- End Header -->

        <table class="w-full text-sm text-left text-gray-500">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr class="bg-gray-100">
            <th class="text-left p-4 font-medium">User</th>
            <th class="text-left p-4 font-medium">Origin</th>
            <th class="text-left p-4 font-medium">Destination</th>
            <th class="text-left p-4 font-medium">Package Information</th>
            <th class="text-left p-4 font-medium">Documents</th>
            <th class="text-left p-4 font-medium">Amount</th>
            <th class="text-left p-4 font-medium">Actions</th>
          </tr>
          </thead>
          <tbody>
            <tr v-for="quote in quotes.data" class="border-t">
              <td class="p-4 flex flex-col">
                {{ quote.name }}
                <span>{{ quote.email }}</span>
                <span>{{ quote.phone }}</span>
              </td>
              <td class="p-4">
                {{ quote.origin_city.name }}, {{ quote.origin_state.name }}
                <p>{{ quote.origin_country.name }}</p>
              </td>
              <td class="p-4">
                {{ quote.destination_city.name }}, {{ quote.destination_state.name }}
                <p>{{ quote.destination_country.name }}</p>
              </td>
              <td class="p-4">
                <div class="flex flex-col">
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Quantity</span>
                    <span>{{ quote.quantity}}</span>
                  </div>
                  <hr>
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Weight</span>
                    <span>{{ quote.weight}}</span>
                  </div>
                  <hr>
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Width</span><span>{{ quote.width}}</span>
                  </div>
                  <hr>
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Height</span><span>{{ quote.height}}</span>
                  </div>
                  <hr>
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Length</span><span>{{ quote.length}}</span>
                  </div>
                </div>
              </td>
              <td class="p-4 gap-y-3 flex flex-col justify-center ">
                <div>Commercial Invoice: <a :href="quote.commercial_invoice" class="text-primary">View</a></div>
                <div>Parking List: <a :href="quote.parking_list" class="text-primary">View</a></div>
              </td>
              <td class="p-4">{{ Helper.nairaFormat(quote.amount) }}</td>
              <td class="p-4">
                <div class="hs-dropdown relative inline-flex">
                  <button id="hs-dropdown-custom-icon-trigger" type="button" class="hs-dropdown-toggle p-3 inline-flex justify-center items-center gap-2 rounded-md border border-background font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-background transition-all text-sm sdark:bg-slate-900 sdark:hover:bg-slate-800 sdark:border-gray-700 sdark:text-gray-400 sdark:hover:text-white sdark:focus:ring-offset-gray-800">
                    <svg class="w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    </svg>
                  </button>

                  <div class="hs-dropdown-menu z-40 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2 sdark:bg-gray-800 sdark:border sdark:border-gray-700" aria-labelledby="hs-dropdown-custom-icon-trigger">
                    <a @click="openModal(quote.id)"  href="javascript:void(0)"  class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:hover:text-gray-300">
                      Set Pricing
                    </a>
                    <Link v-if="!quote.shipment_id" @click="convertQuoteToShipment(quote.id)"  class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:hover:text-gray-300">
                      Convert To Shipment
                    </Link>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <Modal :show="isOpenModal">
      <div class="flex items-start justify-between p-4 border-b rounded-t">
        <h3 class="text-xl font-semibold text-gray-900 sdark:text-white">
          Set Quote Pricing
          <p class="text-gray-500 font-normal text-sm">Use this form to set pricing for selected quote</p>
        </h3>
        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center sdark:hover:bg-gray-600 sdark:hover:text-white" data-modal-hide="trackingModal">
          <svg @click="isOpenModal = false" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
               stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </button>
      </div>
      <hr>
      <form @submit.prevent="submitSetPrice">
        <div class="p-5">
        </div>
        <div class="p-5">
          <TextInput type="number" v-model="form.amount" placeholder="Enter quote price" />
          <InputError class="mt-2" :message="form.errors.amount" />
          <PrimaryButton class="mt-5" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Set Price</PrimaryButton>
        </div>
      </form>
    </Modal>
  </DashboardLayout>
</template>

<style scoped>

</style>