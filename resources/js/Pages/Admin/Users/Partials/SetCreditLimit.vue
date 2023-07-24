<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
  user: Object
})

const form = useForm({
  amount: props.user.credit_limit
});

const emit  = defineEmits(['isModalOpen']);

const submit = () => {
  form.put(route('users.set-credit-limit', props.user.id), {
    onFinish: emit('isModalOpen', false)
  })
}

</script>

<template>
  <div>
    <div class="flex flex-row justify-between items-start p-5">
      <div class="">
        <h3>Set Business Credit Limit: <span class="px-3 py-1 text-primary bg-background ml-4 rounded-full">{{ user.business_name }}</span></h3>
        <p class="text-sm">Use this form to set business credit limit</p>
      </div>
      <div>
        <svg @click="emit('isModalOpen', false)" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
    </div>
    <hr>
    <div class="p-5">
      <form action="" @submit.prevent="submit">
        <div class="flex flex-col gap-y-4 p-5">
          <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
            <div class="w-full">
              <InputLabel value="Credit Limit Amount" class="font-normal"/>
              <TextInput required type="number" v-model="form.amount" min="1"
                         placeholder="" class="mt-2"/>
            </div>
          </div>
          <div class="flex lg:flex-row flex-col items-center w-full gap-x-10 gap-y-4">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">SET CREDIT LIMIT</PrimaryButton>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>