<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {PlusIcon} from "@heroicons/vue/24/outline/index.js";
import { ref } from 'vue'
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle, DialogDescription,
} from '@headlessui/vue'
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import {twMerge} from "tailwind-merge";
import AddFundModal from "@/Pages/Wallet/AddFundModal.vue";
import Modal from "@/Components/Modal.vue";


const form = useForm({
    amount: ''
});


const page = usePage();

defineProps({
  balance: String,
  transactions: Array,
  overdraft_wallet_balance: String
})

const isOpen = ref(false)

function closeModal() {
    isOpen.value = false
}
function openModal() {
    isOpen.value = true
}

const shipmentStatusOptions = [
    {
        id: "all",
        name: "all"
    },
    {
        id: "deposit",
        name: "deposit"
    },
    {
        id: "withdraw",
        name: "withdraw"
    }
];
const filterForm = useForm({
    type: ''
})

const handleFilter = () => {
    axios.get(route('wallet.filter'), { params: { type: filterForm.type }}).then((response) => {
        page.props.transactions = response.data
    })
}

let naira = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'NGN'
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout page-title="Wallet">
        <div class="grid sm:grid-cols-2 gap-10 mt-10">
            <div class="flex rounded-xl p-5 shadow bg-white w-full gap-x-36 justify-between items-center ">
                <div>
                    <h3 class="text-sm">Wallet Balance</h3>
                    <div class="text-2xl font-bold">{{ balance }}</div>
                </div>
                <button @click="openModal" class="flex items-center gap-x-1 bg-background rounded p-2 text-sm">
                    <PlusIcon class="h-4 text-primary" />
                    <span class="text-xs text-primary">Add Fund</span>
                </button>
            </div>

            <div v-if="page.props.auth.user.user_type === 'business'" class="flex rounded-xl p-5 border bg-white w-full gap-x-36 justify-between items-center">
                <div>
                    <h3 class="text-sm">Overdraft Wallet</h3>
                    <div class="text-2xl font-bold">{{ overdraft_wallet_balance }}</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl shadow p-5 bg-white mt-10">
            <div class="flex flex-row justify-between mb-10 items-start">
                <h1 class="text-lg">Wallet History</h1>
                <div>
                    <InputLabel value="Filter" class="mb-3" />
                    <a-select
                        :class="twMerge('w-[200px] rounded-lg')"
                        ref="select"
                        v-model:value="filterForm.type"
                        @focus="focus"
                        @change="handleFilter"
                    >
                        <a-select-option v-for="item in shipmentStatusOptions" :value="item.id">{{ item.name }}</a-select-option>
                    </a-select>

                </div>
            </div>

            <div class="overflow-x-auto border-x border-t rounded-xl">
              <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr class="bg-gray-100">
                        <th class="text-left p-4 font-medium">Amount</th>
                        <th class="text-left p-4 font-medium">Type</th>
                        <th class="text-left p-4 font-medium">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="border-b hover:bg-gray-50" v-for="item in transactions.data">
                        <td class="p-4">
                            <h1 class="text-md font-normal">{{ naira.format(Math.abs(item.amount)) }}</h1>
                        </td>
                        <td class="p-4">{{ item.type }}</td>
                        <td class="p-4"><date-format :date="item.created_at" /></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <Pagination :links="transactions.links"/>
      {{isOpen}}
      <Modal :show="isOpen">
        <AddFundModal @close-modal="(value) => isOpen = value" />
      </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
