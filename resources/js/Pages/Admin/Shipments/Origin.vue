<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import ShipmentLayout from "@/Pages/Shipments/Layouts/ShipmentLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {onMounted, ref} from "vue";
import Checkbox from "@/Components/Checkbox.vue";

//props
const props = defineProps({
  countries: Array,
  states: Array,
  cities: Array,
  shipment_id: Number,
  origin_address: Object,
  addresses: Array,
  user: Object
})

const page = usePage();

//refs
const statesList = ref([]);
const selectedAddress = ref(0);
const citiesList = ref([]);
const form = useForm({
  shipment_id: '',
  contact_name: '',
  contact_phone: '',
  contact_email: '',
  business_name: '',
  address_1: '',
  landmark: '',
  address_2: '',
  country_id: '',
  state_id: '',
  city_id: '',
  postcode: '',
  save_address: false,
  user: props.user.first_name + " " + props.user.last_name,
  user_id: props.user.id
});

//methods
const submit = () => {
  form.post(route('admin.shipment.origin.store'));
}

const getStates = () => {
  axios.get('/api/states/' + form.country_id).then(function (response) {
    statesList.value = response.data.states;
  });
}

const getCities = () => {
  axios.get('/api/cities/' + form.state_id).then(function (response) {
    citiesList.value = response.data.cities;
  });
}

onMounted(() => {
  form.contact_name = props.origin_address.contact_name;
  form.contact_email = props.origin_address.contact_email;
  form.contact_phone = props.origin_address.contact_phone;
  form.address_1 = props.origin_address.address_1;
  form.landmark = props.origin_address.landmark;
  form.address_2 = props.origin_address.address_2;
  form.country_id = props.origin_address.country_id;
  form.state_id = props.origin_address.state_id;
  form.city_id = props.origin_address.city_id;
  form.postcode = props.origin_address.postcode;
  if (!page.url.endsWith('origin')) {
    let res = page.url. split("/");
    let pos = res. indexOf('origin');
    form.shipment_id = res[pos+1];
    console. log(form.shipment_id);
    statesList.value = props.states;
    citiesList.value = props.cities;
  }
});

const populateAddress = (address) => {
  form.country_id = address.country_id;
  form.address_1 = address.address_1;
  form.address_2 = address.address_2;
  form.contact_name = address.address_contacts[0].contact_name;
  form.contact_phone = address.address_contacts[0].contact_phone;
  form.contact_email = address.address_contacts[0].contact_email;
  form.business_name = address.address_contacts[0].business_name;
  form.landmark = address.landmark;
  form.postcode = address.postcode;
  getStates();
  form.state_id = address.state_id;
  getCities();
  form.city_id = address.city_id;
};

</script>

<template>
  <DashboardLayout page-title="Admin Booking - Shipment Origin">
    <ShipmentLayout>
      <div class="w-full">
        <div class="p-5">
          <h3>Origin Address</h3>
        </div>
        <hr>
        <div class="p-5">
          <div class="shadow rounded-xl border p-5"><span class="font-bold">User: </span>{{ user.first_name + " " + user.last_name}}</div>
        </div>
        <form @submit.prevent="submit">
          <div class="p-6 flex flex-col gap-y-2">
            <TextInput type="hidden" v-model="form.user_id" />
            <div class="mt-2">
              <InputLabel value="Contact Name *"/>
              <TextInput
                  id="contact_name"
                  v-model="form.contact_name"
                  type="text"
                  class="mt-2 w-full"
                  placeholder=""
                  autocomplete="contact_name"/>
              <InputError class="mt-2" :message="form.errors.contact_name" />
            </div>
            <div class="mt-2 flex gap-x-5 justify-between lg:flex-row flex-col">
              <div class="w-full">
                <InputLabel value="Contact Phone *"/>
                <TextInput
                    id="contact_phone"
                    v-model="form.contact_phone"
                    type="text"
                    class="mt-2 flex"

                    placeholder=""
                    autocomplete="contact_email"/>

                <InputError class="mt-2" :message="form.errors.contact_phone" />
              </div>
              <div class="w-full lg:mt-0 mt-2">
                <InputLabel value="Contact Email *"/>
                <TextInput
                    id="contact_email"
                    v-model="form.contact_email"
                    type="text"
                    class="mt-2 flex"
                    placeholder=""
                    autocomplete="contact_email"/>
                <InputError class="mt-2" :message="form.errors.contact_email" />
              </div>
            </div>

            <div class="mt-2">
              <InputLabel value="Business Name (Optional)"/>
              <TextInput
                  id="business_name"
                  type="text"
                  v-model="form.business_name"
                  class="mt-2 flex"
                  placeholder=""
                  autocomplete="business_name"/>

              <InputError class="mt-2" :message="form.errors.business_name" />
            </div>
            <div class="mt-2">
              <InputLabel value="Address Line 1 *"/>
              <TextInput
                  id="address"
                  type="text"
                  class="mt-2 flex"
                  maxlength="45"
                  v-model="form.address_1"

                  placeholder=""
                  autocomplete="address"/>
              <InputError class="mt-2" :message="form.errors.address_1" />
            </div>
            <div class="mt-2">
              <InputLabel value="Nearest Landmark *"/>
              <TextInput
                  id="landmark"
                  type="text"
                  class="mt-2 flex"
                  v-model="form.landmark"
                  maxlength="45"

                  placeholder=""
                  autocomplete="address"/>
              <InputError class="mt-2" :message="form.errors.landmark" />
            </div>
            <div class="mt-2">
              <InputLabel value="Address Line 2 *"/>
              <TextInput
                  id="address_2"
                  type="text"
                  class="mt-2 flex"
                  maxlength="45"
                  v-model="form.address_2"
                  placeholder=""
                  autocomplete="address"/>
              <InputError class="mt-2" :message="form.errors.address_2" />
            </div>

            <div class="mt-2 flex justify-between gap-x-5 lg:flex-row flex-col">
              <div class="w-full">
                <InputLabel value="Country *"/>
                <SelectInput
                    id="country"
                    class="mt-2"
                    v-on:change="getStates"
                    :options="countries"
                    v-model="form.country_id"/>
                <InputError class="mt-2" :message="form.errors.country_id" />
              </div>
              <div class="w-full lg:mt-0 mt-2">
                <InputLabel value="State *"/>
                <SelectInput
                    id="state"
                    class="mt-2 flex"
                    v-on:change="getCities"
                    :options="statesList"
                    v-model="form.state_id"/>
                <InputError class="mt-2" :message="form.errors.state_id" />
              </div>
              <div class="w-full lg:mt-0 mt-2">
                <InputLabel value="City *"/>
                <SelectInput
                    id="city"
                    type="text"
                    class="mt-2 flex"
                    :options="citiesList"
                    v-model="form.city_id"/>
                <InputError class="mt-2" :message="form.errors.city_id" />
              </div>
            </div>
            <div class="mt-2">
              <InputLabel value="Postcode *"/>
              <TextInput
                  id="postcode"
                  type="text"
                  class="mt-2 flex"
                  v-model="form.postcode"
                  placeholder=""

                  autocomplete="postcode"/>
              <InputError class="mt-2" :message="form.errors.postcode" />
            </div>
            <div class="mt-2 flex  items-center gap-x-4">
              <Checkbox
                  id="postcode"
                  v-model="form.save_address"
                  placeholder=""
                  autocomplete="postcode"/>
              <InputLabel value="Save Address"/>
            </div>
            <div class="flex flex-col items-center justify-end mt-6">
              <PrimaryButton type="submit" class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Next
              </PrimaryButton>
            </div>
          </div>
        </form>
      </div>
    </ShipmentLayout>
  </DashboardLayout>
</template>

<style scoped>

</style>