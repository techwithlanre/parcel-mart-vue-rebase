<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import ShipmentLayout from "@/Pages/Shipments/Layouts/ShipmentLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm} from "@inertiajs/vue3";
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const form = useForm({
  origin: {
    contact_name: '',
    contact_phone: '',
    contact_email: '',
    business_name: '',
    address_1: '',
    landmark: '',
    address_2: '',
    country: '',
    state: '',
    city: '',
    postcode: '',
  },
});

</script>

<template>
  <DashboardLayout>
    <ShipmentLayout>
      <form @submit.prevent="validateForm(2)" class="w-full">
        <div class="p-6 flex flex-col gap-y-2">
          <div class="">
            <InputLabel value="Contact Name *"/>
            <TextInput
                id="contact_name"
                v-model="form.origin.contact_name"
                type="text"
                class="mt-2 w-full"
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
                  :options="originStates"
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
                  :options="originCities"
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
                required
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
    </ShipmentLayout>
  </DashboardLayout>
</template>

<style scoped>

</style>