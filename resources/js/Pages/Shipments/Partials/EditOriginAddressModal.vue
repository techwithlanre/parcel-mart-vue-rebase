<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {onMounted} from "vue";

const props = defineProps({
  address: Object,
  countries: Array,
  cities: Array,
  states: Array
});

const form = useForm({
  origin: {
    contact_name: props.address.contact_name,
    contact_phone: props.address.contact_phone,
    contact_email: props.address.contact_email,
    business_name: props.address.business_name,
    address_1: props.address.address_1,
    landmark: props.address.landmark,
    address_2: props.address.address_2,
    country: props.address.country,
    state: props.address.state,
    city: props.address.city,
    postcode: props.address.postcode,
  }
});

</script>

<template>
  <form @submit.prevent="validateForm(2)" class="duration-300">
    <div class="p-6 flex flex-col gap-y-2">
      <div class="">
        <InputLabel value="Contact Name *"/>
        <TextInput
            id="contact_name"
            v-model="form.origin.contact_name"
            type="text"
            class="mt-2 flex"
            required
            placeholder=""
            autocomplete="contact_name"/>
        <InputError class="mt-2"/>
      </div>
      <div class="mt-2 flex gap-x-5 justify-between lg:flex-row flex-col">
        <div class="w-full">
          <InputLabel value="Contact Phone *"/>
          <TextInput
              id="contact_phone"
              v-model="form.origin.contact_phone"
              type="text"
              class="mt-2 flex"
              required
              placeholder=""
              autocomplete="contact_email"/>

          <InputError class="mt-2"/>
        </div>
        <div class="w-full lg:mt-0 mt-2">
          <InputLabel value="Contact Email (Optional)"/>
          <TextInput
              id="contact_email"
              v-model="form.origin.contact_email"
              type="text"
              class="mt-2 flex"
              placeholder=""
              autocomplete="contact_email"/>
          <InputError class="mt-2"/>
        </div>
      </div>

      <div class="mt-2">
        <InputLabel value="Business Name (Optional)"/>
        <TextInput
            id="business_name"
            type="text"
            v-model="form.origin.business_name"
            class="mt-2 flex"
            placeholder=""
            autocomplete="business_name"/>

        <InputError class="mt-2"/>
      </div>
      <div class="mt-2">
        <InputLabel value="Address Line 1 *"/>
        <TextInput
            id="address"
            type="text"
            class="mt-2 flex"
            maxlength="45"
            v-model="form.origin.address_1"
            required
            placeholder=""
            autocomplete="address"/>
        <InputError class="mt-2"/>
      </div>
      <div class="mt-2">
        <InputLabel value="Nearest Landmark"/>
        <TextInput
            id="landmark"
            type="text"
            class="mt-2 flex"
            v-model="form.origin.landmark"
            maxlength="45"
            required
            placeholder=""
            autocomplete="address"/>
        <InputError class="mt-2"/>
      </div>
      <div class="mt-2">
        <InputLabel value="Address Line 2"/>
        <TextInput
            id="address_2"
            type="text"
            class="mt-2 flex"
            maxlength="45"
            v-model="form.origin.address_2"
            placeholder=""
            autocomplete="address"/>
        <InputError class="mt-2"/>
      </div>

      <div class="mt-2 flex justify-between gap-x-5 lg:flex-row flex-col">
        <div class="w-full">
          <InputLabel value="Country"/>
          <SelectInput
              id="country"
              class="mt-2"
              v-on:change="getOriginStates"
              :options="countries"
              required
              v-model="form.origin.country"/>
          <InputError class="mt-2"/>
        </div>
        <div class="w-full lg:mt-0 mt-2">
          <InputLabel value="State"/>
          <SelectInput
              id="state"
              class="mt-2 flex"
              v-on:change="getOriginCities"
              :options="states"
              required
              v-model="form.origin.state"/>
          <InputError class="mt-2"/>
        </div>
        <div class="w-full lg:mt-0 mt-2">
          <InputLabel value="City"/>
          <SelectInput
              id="city"
              type="text"
              class="mt-2 flex"
              :options="cities"
              required
              v-model="form.origin.city"/>
          <InputError class="mt-2"/>
        </div>
      </div>
      <div class="mt-2">
        <InputLabel value="Postcode"/>
        <TextInput
            id="postcode"
            type="text"
            class="mt-2 flex"
            v-model="form.origin.postcode"
            placeholder=""
            autocomplete="postcode"/>
        <InputError class="mt-2"/>
      </div>


      <div class="flex flex-col items-center justify-end mt-6">
        <PrimaryButton type="submit" class="w-full">
          Next
        </PrimaryButton>
      </div>
    </div>
  </form>
</template>

<style scoped>

</style>