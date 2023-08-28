<script setup>
import { ref } from 'vue'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle, DialogDescription,
} from '@headlessui/vue'
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";

const emits = defineEmits(['closeModal']);
const form = useForm({
  amount: ''
});

const submit = () => {
  form.post(route('wallet.initialize'), {
    onFinish: () => emits('closeModal', false)
  });
}

</script>

<template>
  <div class="relative w-full max-w-2xl max-h-full duration-300">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow sdark:bg-gray-700">
      <!-- Modal header -->
      <div class="flex items-start justify-between p-4 border-b rounded-t sdark:border-gray-600">
        <h3 class="text-xl font-semibold text-gray-900 sdark:text-white">
          Fund Wallet
          <p class="text-gray-500 font-normal text-xs">Use this form to fund your wallet</p>
        </h3>
        <button type="button" class="text-gray-400 bg-transparent duration-300 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center sdark:hover:bg-gray-600 sdark:hover:text-white" data-modal-hide="getQuoteModal">
          <svg @click="emits('closeModal', false)" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
               stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </button>
      </div>
      <form @submit.prevent="submit">
        <div class="mt-2 px-6 mb-5">
          <div>
            <InputLabel value="Amount"/>
            <TextInput v-model="form.amount" class="mt-3 " required type="number" min="100" placeholder="Enter the amount you want add to your wallet"/>
          </div>
        </div>

        <hr>
        <div class="p-6">
          <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-background px-4 py-2 text-sm font-medium text-primary hover:text-white
                                         hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-all duration-300"
              :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Continue
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>
