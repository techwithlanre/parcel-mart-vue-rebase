<script setup>
import {Link, useForm} from '@inertiajs/vue3'
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import {Inertia} from "@inertiajs/inertia";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  countries: Array,
  search: String
});

const form = useForm({
  search: props.search
});

const search = () => {
  Inertia.get(route('countries'), { search: form.search }, {
    preserveState: true
  });
}

</script>

<template>
    <DashboardLayout page-title="Countries">
      <div class="shadow rounded-xl border border-background/30">
        <div class="flex flex-row items-center justify-between p-5">
          <div>
            <h1 class="text-lg">Countries</h1>
          </div>
          <div class="flex flex-row">
            <TextInput v-model="form.search" class="rounded-r-none max-w-4xl" autofocus type="search" placeholder="Country name" />
            <PrimaryButton @click="search" class="rounded-l-none px-1">Search</PrimaryButton>
          </div>
        </div>
        <div>
          <div class="relative overflow-x-auto shadow sm:rounded-lg">
            <table class="display w-full text-sm text-left text-gray-500">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3">ID</th>
                <th scope="col" class="px-6 py-3">Name</th>
                <th scope="col" class="px-6 py-3">Code</th>
                <th scope="col" class="px-6 py-3">Capital</th>
                <th scope="col" class="px-6 py-3">Region</th>
                <th scope="col" class="px-6 py-3">Sub Region</th>
                <th scope="col" class="px-6 py-3">Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="item in countries.data" class="bg-white border-b duration-500 hover:bg-gray-50 max-h-screen">
                <td class="px-6 py-4">
                  #{{ item.id }}
                </td>
                <td class="px-6 py-4">
                  {{ item.name }}
                </td>
                <td class="px-6 py-4">
                  {{ item.iso2 }}
                </td>
                <td class="px-6 py-4">
                  {{ item.capital }}
                </td>
                <td class="px-6 py-4">
                  {{ item.region }}
                </td>
                <td class="px-6 py-4">
                  {{ item.subregion }}
                </td>
                <td class="px-6 py-4">
                  <Link :href="route('states', item.id)" class="mr-1 mb-1 px-4 py-1 text-sm leading-4 border rounded
              hover:bg-primary-dark hover:text-gray-200 focus:border-primary focus:text-white text-primary duration-500">States</Link>
                </td>
              </tr>
              </tbody>
            </table >
            <div class="px-5">
              <Pagination :links="countries.links" />
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
</template>

<style scoped>

</style>