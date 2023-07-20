<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import {Head, useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import {ref} from "vue";

defineProps({
    countries: Array,
})

const states = ref([]);
const cities = ref([]);

const form = useForm({
    address: '',
    address_2: '',
    landmark: '',
    country_id: '',
    state_id: '',
    city_id: '',
    business_name: '',
    contact_name: '',
    contact_email: '',
    contact_phone: '',
    postcode: '',
});

const submit = () => {
    form.post(route('address-book.store'), {
        onSuccess: () => {
          form.reset();
        },
    })
}

const getCities = () => {
  axios.get('/api/cities/' + form.state_id).then(function (response) {
    cities.value = response.data.cities;
  });
}

const getStates = () => {
  axios.get('/api/states/' + form.country_id).then(function (response) {
    states.value = response.data.states;
  });
};

</script>

<template>
  <div class="mx-auto px-10 mb-10 bg-white">
    <form class="mt-6" @submit.prevent="submit">
      <div class="">
        <InputLabel value="Contact Name *"  />
        <TextInput
            id="contact_name"
            v-model="form.contact_name"
            type="text"
            class="mt-2 flex"
            required
            autofocus
            placeholder=""
            autocomplete="contact_name" />

        <InputError :message="form.errors.contact_name" class="mt-2" />
      </div>
      <div class="mt-3 flex gap-x-5 justify-between lg:flex-row flex-col">
        <div class="w-full">
          <InputLabel value="Contact Phone *"  />
          <TextInput
              id="contact_phone"
              v-model="form.contact_phone"
              type="text"
              class="mt-2 flex"
              required
              placeholder=""
              autocomplete="contact_email" />

          <InputError :message="form.errors.contact_phone" class="mt-2" />
        </div>
        <div class="w-full lg:mt-0 mt-3">
          <InputLabel value="Contact Email (Optional)"  />
          <TextInput
              id="contact_email"
              v-model="form.contact_email"
              type="text"
              class="mt-2 flex"
              placeholder=""
              autocomplete="contact_email" />
          <InputError :message="form.errors.contact_email" class="mt-2" />
        </div>
      </div>

      <div class="mt-3">
        <InputLabel value="Business Name (Optional)"  />
        <TextInput
            id="business_name"
            type="text"
            v-model="form.business_name"
            class="mt-2 flex"
            placeholder=""
            autocomplete="business_name" />

        <InputError :message="form.errors.business_name" class="mt-2" />
      </div>
      <div class="mt-3">
        <InputLabel value="Address Line 1 *"  />
        <TextInput
            id="address"
            type="text"
            class="mt-2 flex"
            v-model="form.address"
            required
            maxlength="45"
            placeholder=""
            autocomplete="address" />
        <InputError :message="form.errors.address" class="mt-2" />
      </div>
      <div class="mt-3">
        <InputLabel value="Nearest Landmark"  />
        <TextInput
            id="landmark"
            type="text"
            class="mt-2 flex"
            v-model="form.landmark"
            maxlength="45"
            required
            placeholder=""
            autocomplete="address" />
        <InputError :message="form.errors.landmark" class="mt-2" />
      </div>
      <div class="mt-3">
        <InputLabel value="Address Line 2"  />
        <TextInput
            id="address_2"
            type="text"
            class="mt-2 flex"
            v-model="form.address_2"
            maxlength="45"
            placeholder=""
            autocomplete="address" />
        <InputError :message="form.errors.address_2" class="mt-2" />
      </div>

      <div class="mt-3 flex justify-between gap-x-5 lg:flex-row flex-col">
        <div class="w-full">
          <InputLabel value="Country"  />
          <SelectInput
              id="country"
              type="text"
              class="mt-3"
              v-on:change="getStates"
              required
              :options="countries"
              v-model="form.country_id"/>
          <InputError :message="form.errors.country_id" class="mt-2" />
        </div>
        <div class="w-full lg:mt-0 mt-3">
          <InputLabel value="State"  />
          <SelectInput
              id="state"
              type="text"
              v-on:change="getCities"
              class="mt-2 flex"
              required
              :options="states"
              v-model="form.state_id"/>
          <InputError :message="form.errors.state_id" class="mt-2" />
        </div>
        <div class="w-full lg:mt-0 mt-3">
          <InputLabel value="City"  />
          <SelectInput
              id="city"
              type="text"
              class="mt-2 flex"
              :options="cities"
              required
              v-model="form.city_id"/>
          <InputError class="mt-2" />
        </div>
      </div>
      <div class="mt-3">
        <InputLabel value="Postcode"  />
        <TextInput
            id="postcode"
            type="text"
            class="mt-2 flex"
            v-model="form.postcode"
            placeholder=""
            autocomplete="postcode" />
        <InputError :message="form.errors.postcode" class="mt-2" />
      </div>


      <div class="flex flex-col items-center justify-end mt-6">
        <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Create Address
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>

<style scoped>

</style>
