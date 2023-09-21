<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Helper from "@/Helpers/Helper.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  quotes: Array
})

</script>

<template>
  <DashboardLayout page-title="Quotes">
    <div class="card p-5 bg-white">
      <div class="overflow-x-auto shadow-background shadow mt-10 rounded-xl">
        <!-- Header -->
        <div class="px-6 py-4 grid gap-1 md:flex md:justify-between md:items-center border-b border-gray-200">
          <div>
            <h2 class="text-xl font-semibold text-gray-800">
              Quotes List
            </h2>
          </div>
        </div>
        <!-- End Header -->

        <table class="w-full text-sm text-left text-gray-500">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr class="bg-gray-100">
            <th class="text-left p-4 font-medium">Origin</th>
            <th class="text-left p-4 font-medium">Destination</th>
            <th class="text-left p-4 font-medium">Package Information</th>
            <th class="text-left p-4 font-medium">Documents</th>
            <th class="text-left p-4 font-medium">Status</th>
            <th class="text-left p-4 font-medium">Shipping Cost</th>
          </tr>
          </thead>
          <tbody>
            <tr v-for="quote in quotes.data" class="border-t">
              <td class="p-4">
                {{ quote.origin_city.name }}, {{ quote.origin_state.name }}
                <p>{{ quote.origin_country.name }}</p>
              </td>
              <td class="p-4">
                {{ quote.destination_city.name }}, {{ quote.destination_state.name }}
                <p>{{ quote.destination_country.name }}</p>
              </td>
              <td class="p-4">
                <div class="flex flex-col">
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Quantity</span><span>{{ quote.quantity}}</span>
                  </div>
                  <hr>
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Weight</span><span>{{ quote.weight}}</span>
                  </div>
                  <hr>
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Width</span><span>{{ quote.width}}</span>
                  </div>
                  <hr>
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Height</span><span>{{ quote.height}}</span>
                  </div>
                  <hr>
                  <div class="flex flex-row justify-between items-center p-1">
                    <span>Length</span><span>{{ quote.length}}</span>
                  </div>
                </div>
              </td>
              <td class="p-4 gap-y-3 flex flex-col justify-center ">
                <div>Commercial Invoice: <a :href="quote.commercial_invoice" class="text-primary">View</a></div>
                <div>Parking List: <a :href="quote.parking_list" class="text-primary">View</a></div>
              </td>
              <td class="p-4">{{ quote.status }}</td>
              <td class="p-4"></td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>
  </DashboardLayout>
</template>

<style scoped>

</style>