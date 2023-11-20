<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import {toast} from "vue3-toastify";

const props = defineProps({
  insurance_option: Object
});
const form = useForm({
  name: '',
  description: '',
  percentage: ''
});
const emits = defineEmits(['closeModal']);

const submit = () => {
  form.post(route('insurance.options.update'), {
    onFinish: () => emits('closeModal', false),
    onSuccess: () => {
      form.reset();
      emit('isModalOpen', false);
      toast.success('Insurance option updated successfully');
    }
  });
}
</script>

<template>
  <div class="relative w-full max-w-2xl max-h-full duration-500">
    <div class="relative bg-white rounded-lg shadow">
      <div class="flex items-start justify-between p-4 border-b rounded-t">
        <h3 class="text-xl font-semibold text-gray-900">
          Edit Insurance Options
          {{ insurance_option }}
          <p class="text-gray-500 font-normal text-xs mt-1">Use this form to create new insurance option for shipments</p>
        </h3>
        <button type="button" class="text-gray-400 bg-transparent duration-500 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center sdark:hover:bg-gray-600 sdark:hover:text-white" data-modal-hide="getQuoteModal">
          <svg @click="emits('closeModal', false)" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
               stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </button>
      </div>
      <form @submit.prevent="submit">
        <div class="mt-2 px-6">
          <div>
            <InputLabel value="Name"/>
            <TextInput v-model="form.name" class="mt-2" required type="text" min="100" placeholder=""/>
          </div>
          <div class="mt-3">
            <InputLabel value="Description"/>
            <TextAreaInput v-model="form.description" class="mt-2" required placeholder=""/>
          </div>
          <div class="mt-3">
            <InputLabel value="Percentage (%)"/>
            <TextInput v-model="form.percentage" class="mt-2" required type="number" min="1" placeholder=""/>
          </div>
        </div>

        <hr>
        <div class="p-6">
          <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-background px-4 py-2 text-sm font-medium text-primary hover:text-white
                                         hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-all duration-500"
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
