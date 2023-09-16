<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {toast} from "vue3-toastify";

const props = defineProps({
  originAddress: Object,
  destinationAddress: Object,
  countries: Array,
  cities: Array,
  states: Array,
  shipmentId: String,
  shipment: Object,
});


const destinationCountries = ref(props.countries);
const destinationStates = ref(props.states);
const destinationCities = ref(props.cities);

const form = useForm({
  origin: {
    contact_name: props.originAddress.contact_name,
    contact_phone: props.originAddress.contact_phone,
    contact_email: props.originAddress.contact_email,
    business_name: props.originAddress.business_name,
    address_1: props.originAddress.address_1,
    landmark: props.originAddress.landmark,
    address_2: props.originAddress.address_2,
    country: props.originAddress.country,
    state: props.originAddress.state,
    city: props.originAddress.city,
    postcode: props.originAddress.postcode,
  },
  destination: {
    contact_name: props.destinationAddress.contact_name,
    contact_phone: props.destinationAddress.contact_phone,
    contact_email: props.destinationAddress.contact_email,
    business_name: props.destinationAddress.business_name,
    address_1: props.destinationAddress.address_1,
    landmark: props.destinationAddress.landmark,
    address_2: props.destinationAddress.address_2,
    country: props.destinationAddress.country,
    state: props.destinationAddress.state,
    city: props.destinationAddress.city,
    postcode: props.destinationAddress.postcode,
  },
  shipment: {
    category: props.shipment.shipment_items[0].item_category_id,
    value: props.shipment.shipment_items[0].value,
    description: props.shipment.shipment_items[0].description,
    quantity: props.shipment.shipment_items[0].quantity,
    weight: props.shipment.shipment_items[0].weight,
    height: props.shipment.shipment_items[0].height,
    length: props.shipment.shipment_items[0].length,
    width: props.shipment.shipment_items[0].width
  }
});

const emit = defineEmits(['isModalOpen'])
const page = usePage();
const submit = () => {
  form.put(route('shipment.recalculate', props.shipmentId), {
    onFinish: (response) => {
      location.reload();
      emit('isModalOpen', false);
      if (page.props.flash?.error?.length > 0) {
        toast.error(page.props.flash.error);
      }

    },
  })
}

const getDestinationStates = function () {
  axios.get('/api/states/' + form.destination.country).then(function (response) {
    destinationStates.value = response.data.states;
    //getDestinationCountries();
  });
};
const getDestinationCities = function () {
  axios.get('/api/cities/' + form.destination.state).then(function (response) {
    destinationCities.value = response.data.cities;
    //getDestinationCountries();
  });
};

const getDestinationCountries = function () {
  axios.get('/api/allowed-countries/' + form.origin.country).then(function (response) {
    props.countries = response.data;
  });
}

</script>

<template>
  <div>
    <div class="flex flex-row justify-between items-start p-5">
      <div class="">
        <h3>Edit Destination Address</h3>
        <p class="text-sm">Use this form to edit origin address for this booking</p>
      </div>
      <div>
        <svg  @click="emit('isModalOpen', false)" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
    </div>
    <hr>
    <div class="p-5">
      <form @submit.prevent="submit" class="duration-500">
        <div class="p-6 flex flex-col gap-y-2">
          <div class="">
            <InputLabel value="Contact Name *"/>
            <TextInput
                id="contact_name"
                v-model="form.destination.contact_name"
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
                  v-model="form.destination.contact_phone"
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
                  v-model="form.destination.contact_email"
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
                v-model="form.destination.business_name"
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
                v-model="form.destination.address_1"
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
                v-model="form.destination.landmark"
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
                v-model="form.destination.address_2"
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
                  v-on:change="getDestinationStates"
                  :options="destinationCountries"
                  required
                  v-model="form.destination.country"/>
              <InputError class="mt-2"/>
            </div>
            <div class="w-full lg:mt-0 mt-2">
              <InputLabel value="State"/>
              <SelectInput
                  id="state"
                  class="mt-2 flex"
                  v-on:change="getDestinationCities"
                  :options="destinationStates"
                  required
                  v-model="form.destination.state"/>
              <InputError class="mt-2"/>
            </div>
            <div class="w-full lg:mt-0 mt-2">
              <InputLabel value="City"/>
              <SelectInput
                  id="city"
                  type="text"
                  class="mt-2 flex"
                  :options="destinationCities"
                  required
                  v-model="form.destination.city"/>
              <InputError class="mt-2"/>
            </div>
          </div>
          <div class="mt-2">
            <InputLabel value="Postcode"/>
            <TextInput
                id="postcode"
                type="text"
                class="mt-2 flex"
                v-model="form.destination.postcode"
                placeholder=""
                autocomplete="postcode"/>
            <InputError class="mt-2"/>
          </div>


          <div class="flex flex-col items-center justify-end mt-6">
            <PrimaryButton type="submit" class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              {{ form.processing ? 'Calculating rate...' : 'Update' }}
            </PrimaryButton>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>