<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
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
            <th class="text-left p-4 font-medium">Shipping Cost</th>
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
              <td class="p-4"><a @click="openModal(quote.id)" href="javascript:void(0)" class="px-3 py-1 bg-background text-primary rounded-full">Set Pricing</a></td>
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