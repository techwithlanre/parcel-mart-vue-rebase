<script setup>
import {Link, useForm} from '@inertiajs/vue3'
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {Button} from "flowbite-vue";
import Pagination from "@/Components/Pagination.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Modal from "@/Components/Modal.vue";
import SelectInput from "@/Components/SelectInput.vue";
import {ref} from "vue";
import {toast} from "vue3-toastify";

const props = defineProps({
  cities: Array,
  countries: Array,
  state_id: Number
});

const showCreateCityModal = ref(false);
const states = ref([]);

const form = useForm({
  state_id: props.state_id,
  city_name: '',
});

const getStates = () => {
  axios.get('/api/states/' + form.country_id).then(function (response) {
    states.value = response.data.states;
  });
};

const createCity = () => {
  form.post(route('city.store'), {
    onSuccess: () => {
      showCreateCityModal.value = false;
      form.reset();
      toast.success('City created successfully');
    }
  });
}
</script>

<template>
  <DashboardLayout page-title="Countries">
    <div class="shadow rounded-xl">
      <div class="flex flex-row items-center justify-between p-5">
        <div>
          <h1 class="text-lg">Cities</h1>
        </div>
        <div>
          <PrimaryButton v-on:click="showCreateCityModal = true" class="px-2 py-1">Create City</PrimaryButton>
        </div>
      </div>
      <div>
        <div class="relative overflow-x-auto shadow sm:rounded-lg">
          <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
            <tr>
              <th scope="col" class="px-6 py-3">ID</th>
              <th scope="col" class="px-6 py-3">Name</th>
              <th scope="col" class="px-6 py-3">State Code</th>
              <th scope="col" class="px-6 py-3">Country Code</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in cities.data" class="bg-white border-b hover:bg-gray-50 max-h-screen duration-500">
              <td class="px-6 py-4">
                #{{ item.id }}
              </td>
              <td class="px-6 py-4">
                {{ item.name }}
              </td>
              <td class="px-6 py-4">
                {{ item.state_code }}
              </td>
              <td class="px-6 py-4">
                {{ item.country_code }}
              </td>
            </tr>
            </tbody>
          </table>
          <div class="px-5">
            <Pagination :links="cities.links" />
          </div>
        </div>
      </div>
    </div>
    <Modal :show="showCreateCityModal" title="Create City">
      <div class="flex flex-row justify-between items-start p-5">
        <div class="">
          <h3>Create City</h3>
          <p class="text-sm">Use this form to create new city</p>
        </div>
        <div>
          <svg @click="showCreateCityModal = false" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
               stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>
      <hr>
      <form @submit.prevent="createCity" class="px-5 pb-10">
        <div class="mt-4">
          <InputLabel value="City" />
          <TextInput type="text" v-model="form.city_name" required />
        </div>
        <div class="mt-4">
          <PrimaryButton type="text" model-value="" >Create</PrimaryButton>
        </div>
      </form>
    </Modal>
  </DashboardLayout>
</template>

<style scoped>

</style>