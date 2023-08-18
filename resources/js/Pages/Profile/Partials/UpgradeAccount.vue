<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm, usePage} from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";


defineProps({
  business_name: {
    type: String,
  },
  credit_limit: {
    type: String,
  },
});

const user = usePage().props.auth.user;

const form = useForm({
  business_name: user.business_name,
});

</script>

<template>
  <section class="space-y-6">
    <header>
      <div class="text-lg font-medium text-gray-900 flex flex-row justify-between items-center">
        <p>Upgrade to Business Account</p>
        <p v-if="user.user_type === 'business' " class="text-sm px-3 py-1 bg-background text-primary rounded-full"><span>Credit Limit</span>: <span class="font-bold">{{ user.credit_limit }}</span></p>
      </div>

      <p class="mt-1 text-sm text-gray-600">
       A business account allows you to make bookings on credit based on your credit limit. By default, your credit limit will be set to 0. You can contact support to increase your credit limit.
      </p>
    </header>

    <form @submit.prevent="form.put(route('profile.upgrade'))">
      <div>
        <InputLabel for="name" value="Business Name" />
        <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            placeholder="Enter business name"
            :disabled="user.user_type === 'business'"
            required
            v-model="form.business_name"
            autofocus
            autocomplete="name"
        />

        <InputError class="mt-2" :message="form.errors.business_name" />
      </div>

      <PrimaryButton class="mt-5" :class="{'opacity-25 ' : form.processing, 'cursor-not-allowed': user.user_type === 'business'}" :disabled="form.processing || user.user_type === 'business'">Upgrade</PrimaryButton>
      <div v-show="form.recentlySuccessful" class="mt-3 duration-300 transition-all slide-down-appear">Request sent</div>
    </form>
  </section>
</template>
