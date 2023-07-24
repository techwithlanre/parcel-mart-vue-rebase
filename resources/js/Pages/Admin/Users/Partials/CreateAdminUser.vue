<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {Link, useForm} from "@inertiajs/vue3";


defineProps({
    countries: Array
})

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
</script>

<template>
  <div class="mx-auto max-w-2xl p-10 bg-white shadow rounded-xl">
    <div>
      <h3 class="font-bold text-2xl ">Create an admin user</h3>
    </div>
    <form @submit.prevent="submit">
      <div class="grid lg:grid-cols-2 grid-cols-1 justify-between gap-x-4 gap-y-3">
        <div>
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
          <SelectInput
              place-holder="Select Country"
              class="block w-full"
              :model-value="form.country"
              :options="countries"
          />

          <InputError class="mt-2" :message="form.errors.country" />
        </div>
      </div>

      <div class="flex flex-col items-center justify-end mt-6">
        <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Sign Up
        </PrimaryButton>
        <div class="mt-5">
          Already registered?
          <Link :href="route('login')"
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Sign In
          </Link>
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped>

</style>
