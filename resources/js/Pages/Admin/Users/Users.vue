<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import CreateAdminUser from "@/Pages/Admin/Users/Partials/CreateAdminUser.vue";
import SetCreditLimit from "@/Pages/Admin/Users/Partials/SetCreditLimit.vue";

defineProps({
    users: Array
})

const isOpenCreateUser = ref(false);
const isOpenSetCreditLimit = ref(false);
const currentUser = ref({});

let naira = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'NGN'
});

const openSetCreditLimitModal = (user) => {
  currentUser.value = user;
  isOpenSetCreditLimit.value = true;
}

</script>

<template>
    <AuthenticatedLayout page-title="Users">
        <div class="card p-5 bg-white">
            <div class="overflow-x-auto shadow-background shadow mt-10 rounded-xl">
              <!-- Header -->
              <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                <div>
                  <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Users
                  </h2>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Add users, edit and more.
                  </p>
                </div>

                <div>
                  <div class="inline-flex gap-x-2">
                    <div class="hs-dropdown relative inline-block [--placement:bottom-right]" data-hs-dropdown-auto-close="inside">
                      <button id="hs-as-table-table-filter-dropdown" type="button" class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-slate-900 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                          <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                        Filter
                        <span class="pl-2 text-xs font-semibold text-blue-600 border-l border-gray-200 dark:border-gray-700 dark:text-blue-500">
                      1
                    </span>
                      </button>
                      <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden mt-2 divide-y divide-gray-200 min-w-[12rem] z-10 bg-white shadow-md rounded-lg mt-2 dark:divide-gray-700 dark:bg-gray-800 dark:border dark:border-gray-700" aria-labelledby="hs-as-table-table-filter-dropdown">
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                          <label for="hs-as-filters-dropdown-all" class="flex py-2.5 px-3">
                            <input type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 pointer-events-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" id="hs-as-filters-dropdown-all" checked>
                            <span class="ml-3 text-sm text-gray-800 dark:text-gray-200">All</span>
                          </label>
                          <label for="hs-as-filters-dropdown-published" class="flex py-2.5 px-3">
                            <input type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 pointer-events-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" id="hs-as-filters-dropdown-published">
                            <span class="ml-3 text-sm text-gray-800 dark:text-gray-200">Published</span>
                          </label>
                          <label for="hs-as-filters-dropdown-pending" class="flex py-2.5 px-3">
                            <input type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 pointer-events-none focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" id="hs-as-filters-dropdown-pending">
                            <span class="ml-3 text-sm text-gray-800 dark:text-gray-200">Pending</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    <a class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-background font-medium bg-white text-primary shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-slate-900 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800" href="#">
                      View all
                    </a>

                    <Link :href="route('users.create')" class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-background text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm dark:focus:ring-offset-gray-800">
                      <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M2.63452 7.50001L13.6345 7.5M8.13452 13V2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                      Add user
                    </Link>
                  </div>
                </div>
              </div>
              <!-- End Header -->
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr class="bg-gray-100">
                        <th class="text-left p-4 font-medium">Name</th>
                        <th class="text-left p-4 font-medium">Email</th>
                        <th class="text-left p-4 font-medium">Phone</th>
                        <th class="text-left p-4 font-medium">User Type</th>
                        <th class="text-left p-4 font-medium">Credit Limit</th>
                        <th class="text-left p-4 font-medium">Wallet</th>
                        <th class="text-left p-4 font-medium">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="border-b hover:bg-gray-50" v-for="item in users.data">
                        <td class="p-4">
                            <h1 class="text-md font-bold">{{ item.first_name }} {{ item.last_name }}</h1>
                        </td>
                        <td class="p-4">{{ item.email }}</td>
                        <td class="p-4">{{ item.phone }}</td>
                        <td class="p-4 uppercase">
                          <div>{{ item.user_type }}: <span v-if="item.user_type === 'business'" class="font-bold text-primary">{{ item.business_name }}</span></div>
                        </td>
                        <td class="p-4">{{ naira.format(item.credit_limit) }}</td>
                        <td class="p-4 text-primary font-semibold">{{ naira.format(item.wallet.balance) }}</td>
                        <td class="p-4">
                          <div class="hs-dropdown relative inline-flex">
                            <button id="hs-dropdown-custom-icon-trigger" type="button" class="hs-dropdown-toggle p-3 inline-flex justify-center items-center gap-2 rounded-md border border-background font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-background transition-all text-sm dark:bg-slate-900 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800">
                              <svg class="w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                              </svg>
                            </button>

                            <div class="hs-dropdown-menu z-40 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2 dark:bg-gray-800 dark:border dark:border-gray-700" aria-labelledby="hs-dropdown-custom-icon-trigger">
                              <a href="javascript:void(0)" v-if="item.user_type === 'business'" v-on:click="openSetCreditLimitModal(item)" class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                Set Credit Limit
                              </a>
                              <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300" href="#">
                                Downloads
                              </a>
                              <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300" href="#">
                                Team Account
                              </a>
                            </div>
                          </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
      <Pagination :links="users.links"/>
      <!--Create User Modal-->
      <Modal :show="isOpenCreateUser">
        <CreateAdminUser />
      </Modal>
      <Modal :show="isOpenSetCreditLimit">
        <SetCreditLimit @is-modal-open="(value) => isOpenSetCreditLimit = value" :user="currentUser" />
      </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
