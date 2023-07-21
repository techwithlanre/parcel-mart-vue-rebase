<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";

const trackForm = useForm({
  number: ''
})

const trackShipment = () => {
  trackForm.post(route('shipment.track'), {
    onFinish: () => trackForm.reset(),
  })
}


const emits = defineEmits(['closeModal']);



</script>

<template>
  <div class="relative w-full max-w-2xl max-h-full duration-300">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
      <!-- Modal header -->
      <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
          Track Shipment
          <p class="text-gray-500 font-normal text-xs">Use this form to track your shipment, enter your tracking number below</p>
        </h3>
        <button type="button" class="text-gray-400 bg-transparent duration-300 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="getQuoteModal">
          <svg @click="emits('closeModal', false)" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
               stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </button>
      </div>
      <!-- Modal body -->
      <form @submit.prevent="trackShipment">
        <div class="p-6 space-y-6">
          <InputLabel  value="Tracking Number"/>
          <TextInput v-model="trackForm.number" required type="text" />
        </div>
        <!-- Modal footer -->
        <div class="flex justify-end items-center px-6 mb-6 space-x-2 border-gray-200 rounded-b dark:border-gray-600">
          <PrimaryButton class="" :class="{ 'opacity-25': trackForm.processing }" :disabled="trackForm.processing">Track</PrimaryButton>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>