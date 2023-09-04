<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import {twMerge} from "tailwind-merge";
import {toast} from "vue3-toastify";

const props = defineProps({
  originAddress: Object,
  destinationAddress: Object,
  countries: Array,
  cities: Array,
  states: Array,
  shipmentId: String,
  shipment: Object,
  categories: Array,
});

const originCountries = ref(props.countries);
const originStates = ref(props.states);
const originCities = ref(props.cities);

console.log(props.shipment);

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
      emit('isModalOpen', false);
      if (page.props.flash.error.length > 0) {
        toast.error(page.props.flash.error);
      }
    }
  })
}

const getOriginStates = function () {
  axios.get('/api/states/' + form.origin.country).then(function (response) {
    originStates.value = response.data.states;
    getDestinationCountries();
  });
};
const getOriginCities = function () {
  axios.get('/api/cities/' + form.origin.state).then(function (response) {
    originCities.value = response.data.cities;
    getDestinationCountries();
  });
};

const getDestinationCountries = function () {
  axios.get('/api/allowed-countries/' + form.origin.country).then(function (response) {
    console.log(response.data)
    props.countries = response.data;
  });
}

</script>

<template>
  <div>
    <div class="flex flex-row justify-between items-start p-5">
      <div class="">
        <h3>Edit Package Details</h3>
        <p class="text-sm">Use this form to edit origin address for this booking</p>
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
              <InputLabel value="Item category" class="font-normal"/>
              <SelectInput required v-model="form.shipment.category" :options="categories"
                           place-holder="" class="mt-2 w-full"/>
            </div>
            <div class="w-full">
              <InputLabel value="Item value" class="font-normal"/>
              <TextInput required type="number" v-model="form.shipment.value" min="1"
                         placeholder="" class="mt-2"/>
            </div>
          </div>
          <div>
            <InputLabel value="Item Description" class="font-normal"/>
            <TextAreaInput v-model="form.shipment.description" rows="7" class="mt-2" required
                           placeholder=""/>
          </div>
          <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
            <div class="w-full">
              <InputLabel value="Quantity" class="font-normal"/>
              <TextInput required type="number" v-model="form.shipment.quantity" min="1" placeholder=""
                         class="mt-2"/>
            </div>
            <div class="w-full">
              <InputLabel value="Weight (KG)" class="font-normal"/>
              <TextInput required type="number" v-model="form.shipment.weight" min="1" placeholder=""
                         class="mt-2"/>
            </div>
          </div>
          <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
            <div class="w-full">
              <InputLabel value="Length (CM)" class="font-normal"/>
              <TextInput required type="number" v-model="form.shipment.length" min="1" placeholder=""
                         class="mt-2"/>
            </div>
            <div class="w-full">
              <InputLabel value="Width (CM)" class="font-normal"/>
              <TextInput required type="number" v-model="form.shipment.width" min="1" placeholder=""
                         class="mt-2"/>
            </div>
            <div class="w-full">
              <InputLabel value="Height (CM)" class="font-normal"/>
              <TextInput required type="number" v-model="form.shipment.height" min="1" placeholder="" class="mt-2"/>
            </div>
          </div>
          <div class="flex lg:flex-row flex-col items-center w-full gap-x-10 gap-y-4">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">UPDATE</PrimaryButton>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>