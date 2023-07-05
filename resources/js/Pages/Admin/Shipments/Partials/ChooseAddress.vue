<script setup>
import {
    Dialog,
    DialogPanel,
    DialogTitle, RadioGroup,
    RadioGroupDescription, RadioGroupLabel, RadioGroupOption,
    TransitionChild,
    TransitionRoot
} from "@headlessui/vue";
import {CloseOutlined} from "@ant-design/icons";
import QuoteTabs from "@/Pages/Home/Partials/QuoteTabs.vue";
import {computed, ref} from "vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {useForm, usePage} from "@inertiajs/vue3";

const page = usePage();

defineProps({
    isOpen: Boolean,
    countries: Array,
    addresses: Array
});

const form = useForm({
   search: ""
});

async function submit() {
    await axios.get(route('search.address')).then((response) => {
        page.props.addresses.push(response.data);
    })
}

const updateAddressesList = () => {

}

const filter = computed(() => {
    const regex = new RegExp(form.search, 'i');

});

</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="$emit('closeQuoteModal', false)" class="relative z-10">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0">
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-500 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all">
                            <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 px-6 mt-6 mb-5 flex justify-between" >
                                <div>Select Origin Address</div>
                                <button @click="$emit('closeQuoteModal', false)" class="text-red-600 text-lg bg-red-100 w-6 h-6 flex justify-center items-center rounded-full">x</button>
                            </DialogTitle>

                            <hr>
                            <div class="p-6">
                                <RadioGroup v-model="selected">
                                    <RadioGroupLabel class="sr-only">Server size</RadioGroupLabel>
                                    <div class="space-y-2">
                                        <RadioGroupOption
                                            as="template"
                                            v-for="address in addresses"
                                            :key="address.id"
                                            :value="address.id"
                                            v-slot="{ active, checked }"
                                        >
                                            <div :class="[active ? 'ring-2 ring-white ring-opacity-60 ring-offset-2 ring-offset-background' : '', checked ? 'bg-primary bg-opacity-75 text-white ' : 'bg-white ']"
                                                 class="relative flex cursor-pointer rounded-lg px-5 py-4 shadow-sm border focus:outline-none">
                                                <div class="flex w-full items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="text-sm">
                                                            <RadioGroupLabel
                                                                as="p"
                                                                :class="checked ? 'text-white' : 'text-gray-900'"
                                                                class="font-medium"
                                                            >
                                                                {{ address.address_contacts[0].contact_name }}
                                                            </RadioGroupLabel>
                                                            <RadioGroupDescription
                                                                as="span"
                                                                :class="checked ? 'text-sky-100' : 'text-gray-500'"
                                                                class="inline"
                                                            >
                                                                <span> {{ address.address_contacts[0].contact_phone }}  {{
                                                                        address.address_contacts[0].contact_email
                                                                    }}</span>
                                                                <span aria-hidden="true"> &middot; </span>
                                                                <span>{{ address.address }}</span>
                                                            </RadioGroupDescription>
                                                        </div>
                                                    </div>
                                                    <div v-show="checked" class="shrink-0 text-white">
                                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                                            <circle
                                                                cx="12"
                                                                cy="12"
                                                                r="12"
                                                                fill="#fff"
                                                                fill-opacity="0.2"
                                                            />
                                                            <path
                                                                d="M7 13l3 3 7-7"
                                                                stroke="#fff"
                                                                stroke-width="1.5"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </RadioGroupOption>
                                    </div>
                                </RadioGroup>
                                <div class="mt-5">
                                    <PrimaryButton>Next</PrimaryButton>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<style scoped>

</style>
