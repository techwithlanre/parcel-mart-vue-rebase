<script setup>
import {defineComponent, ref} from 'vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import {twMerge} from "tailwind-merge";
import addressBook from "../../../images/address-book.png";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import CreateAddress from "@/Pages/AddressBook/CreateAddress.vue";
import EditOriginAddressModal from "@/Pages/Shipments/Partials/EditOriginAddressModal.vue";

defineProps({
  addresses: Object,
  countries: Array,
});

const isOpenCreate = ref(false)
</script>

<template>
    <Head title="Address Book" />
    <AuthenticatedLayout page-title="Address Book">
        <div>
            <div  v-if="addresses.data.length > 0" class="flex flex-col justify-end items-end">
                <a @click="isOpenCreate = true"
                      class="bg-primary px-3 py-2 mt-10 text-sm text-white hover:text-gray-50 rounded-md focus:outline-none focus:ring-2 mb-10 focus:ring-offset-2 focus:ring-background">
                    Create Address
                </a>
            </div>
            <div v-if="addresses.data.length > 0" class="grid lg:grid-cols-3 gap-x-5 gap-y-10 justify-center items-center">
                <div class="card shadow-md bg-white p-5"  v-for="item in addresses.data">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold">{{ item.address_contacts[0].contact_name }}</h3>
                        <div>
                            <button >
                                <a-dropdown>
                                    <a class="ant-dropdown-link" @click.prevent>
                                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                      </svg>
                                    </a>
                                    <template #overlay>
                                        <a-menu>
                                            <a-menu-item>
                                                <Link :href="route('address-book.edit', item.id)">Edit</Link>
                                            </a-menu-item>
                                            <a-menu-item>
                                                <Link :href="route('address-book.destroy', item.id)" :class="twMerge('text-red-600 font-bold')">Delete</Link>
                                            </a-menu-item>
                                        </a-menu>
                                    </template>
                                </a-dropdown>
                            </button>
                        </div>
                    </div>
                    <p class="flex gap-x-10">
                        <span class="text-sm text-primary">{{ item.address_contacts[0].contact_phone }}</span>
                        <span class="text-sm">{{ item.address_contacts[0].contact_email }}</span>
                    </p>
                    <hr class="mt-2">
                    <p class="mt-5"> {{ item.address }}</p>
                    <p class=""> {{ item.landmark }}</p>
                    <div class="flex gap-x-10">
                        <div class="text-blue-950 font-bold">{{ item.country.name }}</div>
<!--                        <div class="text-blue-950 font-bold">{{ item.state?.name }}</div>-->
                        <div class="text-blue-950 font-bold">{{ item.city.name }}</div>
                    </div>
                </div>
            </div>
          <div v-else class="flex flex-row justify-center mt-20">
            <div class="flex flex-col items-center">
              <img :src="addressBook" alt="" class="">
              <h1 class="mt-5 text-center">You don't have any address created. Click the button below to create one</h1>
              <a @click="isOpenCreate = true" class="mt-5"><PrimaryButton>Create Address</PrimaryButton></a>
            </div>
          </div>
            <Pagination v-if="addresses.data.length > 0" :links="addresses.links"/>
        </div>
      <Modal :show="isOpenCreate">
        <div>
          <div class="flex flex-row justify-between items-start p-5">
            <div class="">
              <h3>Create address</h3>
              <p class="text-sm">Use this form to create a new address</p>
            </div>
            <div>
              <button @click="isOpenCreate = false">
                <svg  xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-900 cursor-pointer" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </button>
            </div>
          </div>
          <hr>
          <div class="p-5">
            <CreateAddress :countries="countries" />
          </div>
        </div>
      </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
