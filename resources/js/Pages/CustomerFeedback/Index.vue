<script setup>
import {usePage, Link,} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {onMounted, ref} from "vue";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const page = usePage();
const props = defineProps({
  all_tickets: Array,
})

 const userPermissions = ref([]);

onMounted(() => {
  userPermissions.value = page.props.auth.permissions;
})

const checkPermission = (permission) => {
  return userPermissions.value.includes(permission);
}

</script>

<template>
    <DashboardLayout page-title="All Tickets">
        <div class="card p-5 bg-white">
            <div class="overflow-x-auto shadow-background shadow mt-10 rounded-xl">
              <!-- Header -->
              <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200">
                <div>
                  <h2 class="text-xl font-semibold text-gray-800">
                    Recent Tickets
                  </h2>
                  <p class="text-sm text-gray-600">
                    Reply users, edit and more.
                  </p>
                </div>
                <div >
                    <div v-show="checkPermission('create-user')">
                  <div class="inline-flex gap-x-2">
                    <PrimaryButton
                                   class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-background text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm sdark:focus:ring-offset-gray-800">
                    
                      <Link :href="route('feedback.index')"  class="">Add Ticket</Link>
                    </PrimaryButton>
                  </div>
                </div>
                </div>
              </div>
              <!-- End Header -->
              <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                  <tr class="bg-gray-100">
                    <th class="text-left p-4 font-medium">Ticket ID</th>
                    <th class="text-left p-4 font-medium">Ticket Subject</th>
                      <th class="text-left p-4 font-medium">Name</th>
                      <th class="text-left p-4 font-medium">Email</th>
                      <th class="text-left p-4 font-medium">Date</th>
                      <th class="text-left p-4 font-medium">Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    <tr class="border-b hover:bg-gray-50" v-for="item in all_tickets.data">
                        <td class="p-4">
                            <h1 class="text-md font-bold">{{ item.ticket_id }} </h1>
                        </td>
                        <td class="p-4">
                            <h1 class="text-md font-bold">{{ item.feedback_subject }} </h1>
                        </td>
                        <td class="p-4">{{ item.feedback_name }}</td>
                        <td class="p-4">{{ item.feedback_email }}</td>
                        <td class="p-4">{{ item.submitted_on }}</td>

                        
                        
                        <td class="p-4">
                          <div class="hs-dropdown relative inline-flex">
                            <button id="hs-dropdown-custom-icon-trigger" type="button" class="hs-dropdown-toggle p-3 inline-flex justify-center items-center gap-2 rounded-md border border-background font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-background transition-all text-sm sdark:bg-slate-900 sdark:hover:bg-slate-800 sdark:border-gray-700 sdark:text-gray-400 sdark:hover:text-white sdark:focus:ring-offset-gray-800">
                              <svg class="w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                              </svg>
                            </button>
                        
                            <div class="hs-dropdown-menu z-40 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2 sdark:bg-gray-800 sdark:border sdark:border-gray-700" aria-labelledby="hs-dropdown-custom-icon-trigger">
                                <Link :href="route('feedback.ticket.show', item.ticket_id)"  class="flex  font-medium items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:hover:text-gray-300">View</Link>

                                <a :href="route('feedback.ticket.media', item.ticket_id)" v-if="item.feedback_file != null" class="flex  font-medium items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:hover:text-gray-300">Download Attachment</a>
                              </div>
                     
                          </div>
                        </td>
                    </tr>
                  </tbody>
              </table>
            </div>
        </div>
      <Pagination :links="all_tickets.links"/>
      <!--Create User Modal-->

    </DashboardLayout>
</template>

<style scoped>

</style>
