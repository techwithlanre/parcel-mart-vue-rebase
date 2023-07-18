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

const submit = () => {
    form.post(route('wallet.initialize'));
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
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout page-title="Wallet">
        <div class="grid sm:grid-cols-2 gap-10 mt-10">
            <div class="flex rounded-xl p-5 border bg-white w-full gap-x-36 justify-between items-center ">
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
        <div class="card p-5 border bg-white mt-10">
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
                <table class="table-auto w-full ">
                    <thead class="border-b">
                    <tr class="bg-gray-100">
                        <th class="text-left p-4 font-medium">Amount</th>
                        <th class="text-left p-4 font-medium">Type</th>
                        <th class="text-left p-4 font-medium">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="border-b hover:bg-gray-50" v-for="item in transactions.data">
                        <td class="p-4">
                            <h1 class="text-md font-bold">N{{ Math.abs(item.amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</h1>
                        </td>
                        <td class="p-4">{{ item.type }}</td>
                        <td class="p-4"><date-format :date="item.created_at" /></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <Pagination :links="transactions.links"/>
        <TransitionRoot appear :show="isOpen" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-10">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black bg-opacity-25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white pt-6 text-left align-middle shadow-xl transition-all"
                            >
                                <form @submit.prevent="submit">
                                    <DialogTitle as="h3" class="text-lg font-medium leading-6 px-6 text-gray-900">
                                        Fund Wallet
                                    </DialogTitle>
                                    <DialogDescription class="px-6">Use this form to fund your wallet</DialogDescription>
                                    <div class="mt-2 px-6 mb-5">
                                        <div>
                                            <InputLabel value="Amount"/>
                                            <TextInput v-model="form.amount" class="mt-3 " required type="number" min="0" placeholder="Enter the amount you want add to your wallet"/>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="p-6">
                                        <button
                                            type="submit"
                                            class="inline-flex justify-center rounded-md border border-transparent bg-background px-4 py-2 text-sm font-medium text-primary hover:text-white
                                         hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-all duration-300"
                                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Continue
                                        </button>
                                    </div>
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
