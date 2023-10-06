<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {ref, computed} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Modal from "@/Components/Modal.vue"
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";

defineProps({
    data: Array,
    countries: Array
});


const page = usePage();
const showCreateCityModal = ref(false);
const visible = ref(false);
const confirmLoading = ref(false);
const currentlyEditing = ref({});
const filteredOptions = computed(() => page.props.countries.filter(selected => !editForm.destinations.includes(selected)));
const states = ref([]);

const showModal = (item) => {
  currentlyEditing.value = item;
  visible.value = true;
};

const form = useForm({
  country_id: '',
  state_id: '',
  city_name: '',
});



const handleOk = () => {
    confirmLoading.value = true;
    setTimeout(() => {
        visible.value = false;
        confirmLoading.value = false;
    }, 2000);
};

const getStates = () => {
  axios.get('/api/states/' + form.country_id).then(function (response) {
    states.value = response.data.states;
  });
};

const createState = () => {
  form.post(route('city.store'), {
    onSuccess: () => {
      showCreateCityModal.value = false;
      form.reset();
    }
  });
}


</script>

<template>
<DashboardLayout page-title="Shipment Locations">
    <div class="shadow rounded-xl">
      <div class="flex flex-row items-center justify-between  px-5 ">
        <div>
          <h1 class="text-lg">Shipment Locations</h1>
        </div>
        <div>
          <PrimaryButton v-on:click="showCreateCityModal = true">Create City</PrimaryButton>
        </div>
      </div>
      <div>
        <div class="relative overflow-x-auto shadow sm:rounded-lg">
          <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 sdark:bg-gray-700 sdark:text-gray-400">
            <tr>
              <th scope="col" class="px-6 py-3">Origin Country</th>
              <th scope="col" class="px-6 py-3">Destination Countries</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in data" class="bg-white border-b sdark:bg-gray-800 hover:bg-gray-50 max-h-screen">
              <td class="px-6 py-4">
                {{ item.origin.country }}
              </td>
              <td class="px-6 py-4">
                <a href="#" @click="showModal(item)" class="text-primary font-medium hover:text-green-600">Edit</a>
              </td>
              <td class="px-6 py-4 flex flex-row gap-5 overflow-auto">
                <div v-for="i in item.destination_countries" class="bg-background text-xs text-primary px-4 py-1 rounded-full h-max w-max">{{ i.country }} </div>
              </td>

            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <Modal
            :show="visible"
            title="Edit Shipment Location">
            <div class="p-5">
                <div>
                    <InputLabel value="Enter City" />
                    <TextInput type="text" class="mt-2"  readonly disabled="" />
                </div>
                <div class="mt-4">
                    <InputLabel value="Allowed Destinations" />
                    <select multiple
                        class="bg-transparent border-gray-300 rounded-md w-full outline-none focus:border-primary focus:ring-2 focus:ring-background"
                        ref="select">
                        <option :selected="item.id === currentlyEditing.destination_countries[0].id" :value="item.id" :key="item.id" v-for="item in countries">{{ item.name }}</option>
                    </select>
                </div>
            </div>
        </Modal>
      <Modal :show="showCreateCityModal" title="Create City">
        <div class="p-5">
          <h3>Create City</h3>
        </div>
        <hr>
        <form @submit.prevent="createState" class="px-5 pb-10">
          <div class="mt-4">
            <InputLabel value="Country" />
            <SelectInput v-model="form.country_id" :options="countries" v-on:change="getStates" />
          </div>
          <div class="mt-4">
            <InputLabel value="State" />
            <SelectInput v-model="form.state_id" :options="states" > </SelectInput>
          </div>
          <div class="mt-4">
            <InputLabel value="City" />
            <TextInput type="text" v-model="form.city_name" />
          </div>
          <div class="mt-4">
            <PrimaryButton type="text" model-value="" >Create</PrimaryButton>
          </div>
        </form>
      </Modal>
    </div>
</DashboardLayout>
</template>

<style scoped>

</style>
