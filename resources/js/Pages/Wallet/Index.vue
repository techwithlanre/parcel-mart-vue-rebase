<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
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

const isOpen = ref(false)

function closeModal() {
    isOpen.value = false
}
function openModal() {
    isOpen.value = true
}


</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex">
            <div class="flex rounded-xl items-start p-5 border bg-white lg:w-max w-full gap-x-10 justify-between">
                <div>
                    <h3 class="text-sm">Wallet Balance</h3>
                    <div class="text-2xl font-bold">$2,395.09</div>
                </div>
                <div class="flex items-center gap-x-1 bg-background rounded p-2 text-sm">
                    <PlusIcon class="h-4 text-primary" />
                    <button @click="openModal" class="text-xs text-primary">Add Fund</button>
                </div>
            </div>
        </div>
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
                                <DialogTitle as="h3" class="text-lg font-medium leading-6 px-6 text-gray-900">
                                    Fund Wallet
                                </DialogTitle>
                                <DialogDescription class="px-6">Use this form to fund your wallet</DialogDescription>
                                <div class="mt-2 px-6 mb-5">
                                    <div>
                                        <InputLabel value="Amount"/>
                                        <TextInput model-value="" class="mt-3 " type="number" min="0" placeholder="Enter the amount you want add to your wallet"/>
                                    </div>
                                </div>

                                <hr>
                                <div class="p-6">
                                    <button
                                        type="button"
                                        class="inline-flex justify-center rounded-md border border-transparent bg-background px-4 py-2 text-sm font-medium text-primary hover:text-white
                                         hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-all duration-300"
                                    >
                                        Continue
                                    </button>
                                </div>
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
