<script setup>
import {Link} from '@inertiajs/vue3'
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {Button} from "flowbite-vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
  states: Array
})
</script>

<template>
  <DashboardLayout page-title="States">
    <div class="shadow rounded-xl">
      <div class="flex flex-row items-center justify-between  p-5 ">
        <div>
          <h1 class="text-lg">States</h1>
        </div>
        <div>

        </div>
      </div>
      <div>
        <div class="relative overflow-x-auto shadow sm:rounded-lg">
          <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3">ID</th>
              <th scope="col" class="px-6 py-3">Name</th>
              <th scope="col" class="px-6 py-3">Country Code</th>
              <th scope="col" class="px-6 py-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in states.data" class="bg-white border-b duration-500 hover:bg-gray-50 max-h-screen">
              <td class="px-6 py-4">
                #{{ item.id }}
              </td>
              <td class="px-6 py-4">
                {{ item.name }}
              </td>
              <td class="px-6 py-4">
                {{ item.country_code }}
              </td>
              <td class="px-6 py-4">
                <Link :href="route('cities', item.id)" class="mr-1 mb-1 px-4 py-1 text-sm leading-4 border rounded
              hover:bg-primary-dark hover:text-gray-200 focus:border-primary focus:text-white text-primary duration-500">Cities</Link>
              </td>
            </tr>
            </tbody>
          </table>
          <div class="px-5">
            <Pagination :links="states.links" />
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<style scoped>

</style>