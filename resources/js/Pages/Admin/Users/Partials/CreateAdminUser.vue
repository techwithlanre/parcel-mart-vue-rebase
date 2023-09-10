<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {Link, useForm} from "@inertiajs/vue3";
import {toast} from "vue3-toastify";


defineProps({
  countries: Array,
  roles: Array
});

const emit = defineEmits(['isModalOpen'])

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    country: '0',
    role: ''
});

const submit = () => {
  form.post(route('users.store'), {
    onFinish: () => {
      emit('isModalOpen', false);
      toast.success('User created')
    }
  })
}
</script>

<template>
  <div class="mx-auto max-w-2xl bg-white shadow rounded-xl">
    <div class="flex flex-row justify-between items-start p-5">
      <div class="">
        <h3>Create Admin User</h3>
        <p class="text-sm">Use this form to create admin users</p>
      </div>
      <div>
        <svg @click="emit('isModalOpen', false)" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
    </div>
    <hr>
    <form @submit.prevent="submit" class="p-5">
      <div class="grid lg:grid-cols-2 grid-cols-1 justify-between gap-x-4 gap-y-3">
        <div>
          <InputLabel value="First Name" class="" />
          <TextInput
              id="first_name"
              type="text"
              class="mt-1 block w-full"
              v-model="form.first_name"
              required
              autofocus
              placeholder="First Name"
              autocomplete="first_name" />

          <InputError class="mt-2" :message="form.errors.first_name" />
        </div>
        <div>
          <InputLabel value="Last Name" class="" />
          <TextInput
              id="last_name"
              type="text"
              class="mt-1 block w-full"
              v-model="form.last_name"
              required
              autofocus
              placeholder="Last Name"
              autocomplete="last_name" />
          <InputError class="mt-2" :message="form.errors.last_name" />
        </div>
      </div>

      <div class="mt-4">
        <InputLabel value="Email" class="mt-3" />
        <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            placeholder="Email"
            autocomplete="email" />

        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel value="Phone" class="mt-3" />
        <TextInput
            id="phone"
            type="tel"
            class="mt-1 block w-full"
            v-model="form.phone"
            required
            placeholder="Phone"
            autocomplete="phone" />

        <InputError class="mt-2" :message="form.errors.phone" />
      </div>

      <div class="grid lg:grid-cols-2 grid-cols-1 justify-between gap-x-4 gap-y-3 mt-3">
        <div class="">
          <InputLabel value="Password" class="mt-3" />
          <TextInput
              id="password"
              type="password"
              class="mt-1 block w-full"
              v-model="form.password"
              required
              placeholder="Password"
              autocomplete="new-password" />

          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="">
          <InputLabel value="Confirm Password" class="mt-3" />
          <TextInput
              id="password_confirmation"
              type="password"
              class="mt-1 block w-full"
              v-model="form.password_confirmation"
              required
              placeholder="Confirm Password"
              autocomplete="new-password" />

          <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>
      </div>

      <div class="">
        <div class="mt-4">
          <InputLabel value="Role" class="mt-3" />
          <SelectInput
              place-holder="Select Role"
              required
              class="block w-full"
              v-model="form.role"
              :options="roles"
          />

          <InputError class="mt-2" :message="form.errors.role" />
        </div>
      </div>

      <div class="flex flex-col items-center justify-end mt-6">
        <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Create User
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>

<style scoped>

</style>
