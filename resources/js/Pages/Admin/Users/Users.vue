<script setup>
import {usePage, Link} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import {onMounted, ref} from "vue";
import CreateAdminUser from "@/Pages/Admin/Users/Partials/CreateAdminUser.vue";
import SetCreditLimit from "@/Pages/Admin/Users/Partials/SetCreditLimit.vue";
import ChangeUserRole from "@/Pages/Admin/Users/Partials/ChangeUserRole.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Helper from "../../../Helpers/Helper.js";

const page = usePage();
const props = defineProps({
  users: Array,
  roles: Array
})

const userPermissions = ref([]);
const isOpenCreateUser = ref(false);
const isOpenUserChangeRole = ref(false);
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
const openUserChangeRoleModal = (user) => {
  currentUser.value = user;
  isOpenUserChangeRole.value = true;
}

onMounted(() => {
  userPermissions.value = page.props.auth.permissions;
})

const checkPermission = (permission) => {
  return userPermissions.value.includes(permission);
}

</script>

<template>
    <DashboardLayout page-title="Users">
        <div class="card p-5 bg-white">
            <div class="overflow-x-auto shadow-background shadow mt-10 rounded-xl">
              <!-- Header -->
              <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200">
                <div>
                  <h2 class="text-xl font-semibold text-gray-800">
                    Users
                  </h2>
                  <p class="text-sm text-gray-600">
                    Add users, edit and more.
                  </p>
                </div>
                <div v-show="checkPermission('create-user')">
                  <div class="inline-flex gap-x-2">
                    <PrimaryButton @click="isOpenCreateUser = true"
                                   class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-background text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm sdark:focus:ring-offset-gray-800">
                      <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                           fill="none">
                        <path d="M2.63452 7.50001L13.6345 7.5M8.13452 13V2" stroke="currentColor" stroke-width="2"
                              stroke-linecap="round"/>
                      </svg>
                      <span>Add User</span>
                    </PrimaryButton>
                  </div>
                </div>
              </div>
              <!-- End Header -->
              <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                  <tr class="bg-gray-100">
                      <th class="text-left p-4 font-medium">Name</th>
                      <th class="text-left p-4 font-medium">Email</th>
                      <th class="text-left p-4 font-medium">Phone</th>
                      <th class="text-left p-4 font-medium">User Type</th>
                      <th class="text-left p-4 font-medium">Role</th>
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
                          <div>{{ item.user_type }}<span v-if="item.user_type === 'business'" class="font-bold text-primary">: {{ item.business_name }}</span></div>
                        </td>
                        <td class="p-4">{{ item.roles[0]?.name }}</td>
                        <td class="p-4">{{ Helper.nairaFormat(item.credit_limit) }}</td>
                        <td class="p-4 text-primary font-semibold">{{ Helper.nairaFormat(item.wallet.balance) }}</td>
                        <td class="p-4">
                          <div class="hs-dropdown relative inline-flex">
                            <button id="hs-dropdown-custom-icon-trigger" type="button" class="hs-dropdown-toggle p-3 inline-flex justify-center items-center gap-2 rounded-md border border-background font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-background transition-all text-sm sdark:bg-slate-900 sdark:hover:bg-slate-800 sdark:border-gray-700 sdark:text-gray-400 sdark:hover:text-white sdark:focus:ring-offset-gray-800">
                              <svg class="w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                              </svg>
                            </button>

                            <div class="hs-dropdown-menu z-40 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2" aria-labelledby="hs-dropdown-custom-icon-trigger">
                              <Link v-show="checkPermission('create-shipment')" :href="route('admin.shipment.origin') + '?user=' + item.id" class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:hover:text-gray-300">
                                Book Shipment
                              </Link>
                              <a href="javascript:void(0)" v-if="item.user_type === 'business'" v-on:click="openSetCreditLimitModal(item)" class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:hover:text-gray-300">
                                Set Credit Limit
                              </a>
                              <a v-show="checkPermission('edit-user')" href="javascript:void(0)"  v-on:click="openUserChangeRoleModal(item)" class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-background sdark:text-gray-400 sdark:hover:bg-gray-700 sdark:hover:text-gray-300">
                                Change User Role
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
        <CreateAdminUser  @is-modal-open="(value) => isOpenCreateUser = value" :roles="roles" />
      </Modal>
      <Modal :show="isOpenSetCreditLimit" :closeable="true">
        <SetCreditLimit @is-modal-open="(value) => isOpenSetCreditLimit = value" :user="currentUser" />
      </Modal>
      <Modal :show="isOpenUserChangeRole" :closeable="true">
        <ChangeUserRole @is-modal-open="(value) => isOpenUserChangeRole = value" :user="currentUser" :roles="roles" />
      </Modal>
    </DashboardLayout>
</template>

<style scoped>

</style>
