<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {Link, usePage} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Helper from "../../../Helpers/Helper.js";
import {ref} from "vue";
import AddFundModal from "@/Pages/Wallet/AddFundModal.vue";
import Modal from "@/Components/Modal.vue";
import AddInsuranceOptionModal from "@/Pages/Admin/InsuranceOptions/Partials/AddInsuranceOptionModal.vue";
import EditInsuranceOptionModal from "@/Pages/Admin/InsuranceOptions/Partials/EditInsuranceOptionModal.vue";

const page = usePage();
const props = defineProps({
  insurance_options: Array
});

const currentlyEditing = ref({});
const showAddModal = ref(false);
const showEditModal = ref(false);

const openEditInsuranceOptionModal = (value) => {
  currentlyEditing.value = value;
  showEditModal.value = true;
}

const checkPermission = () => (permission) => {
  return page.props.auth.permissions.includes(permission);
}

</script>

<template>
  <DashboardLayout page-title="Insurance Options">
    <div class="mb-5 flex flex-row justify-end">
        <PrimaryButton @click="showAddModal = true" class="w-max">Create Insurance Option</PrimaryButton>
    </div>
    <div class="card border bg-white rounded-xl" v-show="checkPermission('read-role')">
      <div class="p-5">
        <h1 class="font-semibold">Insurance Options List</h1>
        <p>List of insurance options available to users</p>
      </div>
      <div class="overflow-x-auto rounded-t-0 rounded-b-xl">
        <table class="w-full text-sm text-left text-gray-500">
          <thead class="border-b">
          <tr class="bg-gray-100">
            <th class="text-left p-4 font-medium">Name</th>
            <th class="text-left p-4 font-medium">Description</th>
            <th class="text-left p-4 font-medium">Percentage</th>
            <th class="text-left p-4 font-medium">Coverage</th>
            <th class="text-left p-4 font-medium"></th>
          </tr>
          </thead>
          <tbody>
          <tr class="border-b hover:bg-gray-50" v-for="item in insurance_options">
            <td class="p-4">
              <h1 class="text-md">{{ item.name }}</h1>
            </td>
            <td class="p-4">{{ item.description }}</td>
            <td class="p-4">{{ item.percentage ?? 0 }}%</td>
            <td class="p-4">{{ Helper.nairaFormat(item.cover) }}</td>
            <td class="p-4">
              <div>
                <a @click="openEditInsuranceOptionModal(item)" v-show="checkPermission('edit-role')" class="btn text-primary hover:text-primary-dark">Edit</a> |
<!--                <Link v-show="checkPermission('delete-role')" class="text-red-500 hover:text-red-700">Delete</Link>-->
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <Modal :show="showAddModal">
      <AddInsuranceOptionModal @close-modal="(value) => showAddModal = value" />
    </Modal>
    <Modal :show="showEditModal">
      <EditInsuranceOptionModal @close-modal="(value) => showEditModal = value" :insurance_options="currentlyEditing.value" />
    </Modal>
  </DashboardLayout>
</template>

<style scoped>

</style>