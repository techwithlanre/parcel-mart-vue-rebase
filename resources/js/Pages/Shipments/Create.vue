<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";
import {Head, Link, useForm} from "@inertiajs/vue3";
import {
    Tab,
    TabGroup,
    TabList,
    TabPanel,
    TabPanels
} from "@headlessui/vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import ChooseAddress from "@/Pages/Shipments/Partials/ChooseAddress.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import InputError from "@/Components/InputError.vue";

const activeTab = ref('first')
const activeKey = ref('1');

const isChooseAddressOpen = ref(false)

function toggleQuote(value) {
    isChooseAddressOpen.value = value
}

const tabs = [
    {name: "Parcels", icon: WalletIcon, route: "profile"},
    {name: "Documents", icon: DocumentIcon, route: "settlement-details"}
];

defineProps({
    countries: Array,
    addresses: Array
})

const form = useForm({
    origin: {
        contact_name: '',
        contact_phone: '',
        contact_email: '',
        business_name: '',
        address_1: '',
        landmark: '',
        address_2: '',
        country: '',
        state: '',
        city: '',
        postcode: '',
    },
    destination: {
        contact_name: '',
        contact_phone: '',
        contact_email: '',
        business_name: '',
        address_1: '',
        landmark: '',
        address_2: '',
        country: '',
        state: '',
        city: '',
        postcode: '',
    }
});


</script>

<template>
    <Head title="Create Shipment"/>
    <AuthenticatedLayout page-title="Shipments">
        <div class="flex lg:flex-row flex-col gap-x-10 gap-y-10">
            <TabGroup>
                <TabList class="space-x-1 rounded-xl bg-gray-100 duration-300 lg:w-1/6 flex flex-col gap-y-3 p-5">
                    <Tab as="template" v-slot="{ selected }" class="duration-300">
                        <button
                            :class="['w-full text-left px-5 rounded-lg lg:py-2 py-2 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-white shadow' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]">
                            Origin Address
                        </button>
                    </Tab>
                    <Tab as="template" v-slot="{ selected }" class="duration-300">
                        <button
                            :class="['w-full text-left px-5 rounded-lg lg:py-2 py-2 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-white shadow' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]">
                            Destination Address
                        </button>
                    </Tab>
                    <Tab as="template" v-slot="{ selected }" class="duration-300">
                        <button
                            :class="['w-full text-left px-5 rounded-lg lg:py-2 py-2 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-white shadow' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]">
                            Shipping Details
                        </button>
                    </Tab>
                    <Tab as="template" v-slot="{ selected }" class="duration-300">
                        <button
                            :class="['w-full text-left px-5 rounded-lg lg:py-2 py-2 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-white shadow' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]">
                            Payment
                        </button>
                    </Tab>
                    <Tab as="template" v-slot="{ selected }" class="duration-300">
                        <button
                            :class="['w-full text-left px-5 rounded-lg lg:py-2 py-2 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-white shadow' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]">
                            Service Type
                        </button>
                    </Tab>
                </TabList>
                <TabPanels class="w-full lg:w-3/6">
                    <TabPanel class="fade-in duration-300 w-full">
                        <div class="card border bg-white">
                            <h3 class="p-5 text-lg">Enter origin address details</h3>
                            <div class="p-0">
                                <hr class="p-0">
                            </div>
                            <div class="p-6 flex flex-col gap-y-2">
                                <div class="">
                                    <InputLabel value="Contact Name *"  />
                                    <TextInput
                                        id="contact_name"
                                        v-model="form.origin.contact_name"
                                        type="text"
                                        class="mt-2 flex"
                                        required
                                        placeholder="Enter Contact Name"
                                        autocomplete="contact_name" />

                                    <InputError class="mt-2" />
                                </div>
                                <div class="mt-3 flex gap-x-5 justify-between lg:flex-row flex-col">
                                    <div class="w-full">
                                        <InputLabel value="Contact Phone *"  />
                                        <TextInput
                                            id="contact_phone"
                                            v-model="form.origin.contact_phone"
                                            type="text"
                                            class="mt-2 flex"
                                            required
                                            placeholder="Enter Contact Phone"
                                            autocomplete="contact_email" />

                                        <InputError class="mt-2" />
                                    </div>
                                    <div class="w-full lg:mt-0 mt-3">
                                        <InputLabel value="Contact Email (Optional)"  />
                                        <TextInput
                                            id="contact_email"
                                            v-model="form.origin.contact_email"
                                            type="text"
                                            class="mt-2 flex"
                                            placeholder="Enter Contact Email"
                                            autocomplete="contact_email" />
                                        <InputError class="mt-2" />
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <InputLabel value="Business Name (Optional)"  />
                                    <TextInput
                                        id="business_name"
                                        type="text"
                                        v-model="form.origin.business_name"
                                        class="mt-2 flex"
                                        placeholder="Enter Business Name"
                                        autocomplete="business_name" />

                                    <InputError class="mt-2" />
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Address Line 1 *"  />
                                    <TextInput
                                        id="address"
                                        type="text"
                                        class="mt-2 flex"
                                        v-model="form.origin.address_1"
                                        required
                                        autofocus
                                        placeholder="Start typing address"
                                        autocomplete="address" />
                                    <InputError class="mt-2" />
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Nearest Landmark"  />
                                    <TextInput
                                        id="landmark"
                                        type="text"
                                        class="mt-2 flex"
                                        v-model="form.origin.landmark"
                                        required
                                        autofocus
                                        placeholder="Start typing address or postcode"
                                        autocomplete="address" />
                                    <InputError class="mt-2" />
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Address Line 2"  />
                                    <TextInput
                                        id="address_2"
                                        type="text"
                                        class="mt-2 flex"
                                        v-model="form.origin.address_2"
                                        autofocus
                                        placeholder="Start typing address or postcode"
                                        autocomplete="address" />
                                    <InputError class="mt-2" />
                                </div>

                                <div class="mt-3 flex justify-between gap-x-5 lg:flex-row flex-col">
                                    <div class="w-full">
                                        <InputLabel value="Country"  />
                                        <SelectInput
                                            id="country"
                                            type="text"
                                            class="mt-3"
                                            :options="countries"
                                            v-model="form.origin.country"/>
                                        <InputError class="mt-2" />
                                    </div>
                                    <div class="w-full lg:mt-0 mt-3">
                                        <InputLabel value="State"  />
                                        <SelectInput
                                            id="state"
                                            type="text"
                                            class="mt-2 flex"
                                            v-model="form.origin.state_id"/>
                                        <InputError class="mt-2" />
                                    </div>
                                    <div class="w-full lg:mt-0 mt-3">
                                        <InputLabel value="City"  />
                                        <SelectInput
                                            id="city"
                                            type="text"
                                            class="mt-2 flex"
                                            v-model="form.origin.city"/>
                                        <InputError class="mt-2" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Postcode"  />
                                    <TextInput
                                        id="postcode"
                                        type="text"
                                        class="mt-2 flex"
                                        v-model="form.origin.postcode"
                                        placeholder="Enter postcode"
                                        autocomplete="postcode" />
                                    <InputError class="mt-2" />
                                </div>


                                <div class="flex flex-col items-center justify-end mt-6">
                                    <PrimaryButton class="w-full">
                                        Next
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                    <TabPanel class="fade-in duration-300 w-full">
                        <div class="card border bg-white">
                            <h3 class="p-5 text-lg">Enter destination address details</h3>
                            <div class="p-0">
                                <hr class="p-0">
                            </div>
                            <div class="p-6 flex flex-col gap-y-2">
                                <div class="">
                                    <InputLabel value="Contact Name *"  />
                                    <TextInput
                                        id="contact_name"
                                        v-model="form.destination.contact_name"
                                        type="text"
                                        class="mt-2 flex"
                                        required
                                        placeholder="Enter Contact Name"
                                        autocomplete="contact_name" />

                                    <InputError class="mt-2" />
                                </div>
                                <div class="mt-3 flex gap-x-5 justify-between lg:flex-row flex-col">
                                    <div class="w-full">
                                        <InputLabel value="Contact Phone *"  />
                                        <TextInput
                                            id="contact_phone"
                                            v-model="form.destination.contact_phone"
                                            type="text"
                                            class="mt-2 flex"
                                            required
                                            placeholder="Enter Contact Phone"
                                            autocomplete="contact_email" />

                                        <InputError class="mt-2" />
                                    </div>
                                    <div class="w-full lg:mt-0 mt-3">
                                        <InputLabel value="Contact Email (Optional)"  />
                                        <TextInput
                                            id="contact_email"
                                            v-model="form.destination.contact_email"
                                            type="text"
                                            class="mt-2 flex"
                                            placeholder="Enter Contact Email"
                                            autocomplete="contact_email" />
                                        <InputError class="mt-2" />
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <InputLabel value="Business Name (Optional)"  />
                                    <TextInput
                                        id="business_name"
                                        type="text"
                                        v-model="form.destination.business_name"
                                        class="mt-2 flex"
                                        placeholder="Enter Business Name"
                                        autocomplete="business_name" />

                                    <InputError class="mt-2" />
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Address Line 1 *"  />
                                    <TextInput
                                        id="address"
                                        type="text"
                                        class="mt-2 flex"
                                        v-model="form.destination.address_1"
                                        required
                                        autofocus
                                        placeholder="Start typing address"
                                        autocomplete="address" />
                                    <InputError class="mt-2" />
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Nearest Landmark"  />
                                    <TextInput
                                        id="landmark"
                                        type="text"
                                        class="mt-2 flex"
                                        v-model="form.destination.landmark"
                                        required
                                        autofocus
                                        placeholder="Start typing address or postcode"
                                        autocomplete="address" />
                                    <InputError class="mt-2" />
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Address Line 2"  />
                                    <TextInput
                                        id="address_2"
                                        type="text"
                                        class="mt-2 flex"
                                        v-model="form.destination.address_2"
                                        autofocus
                                        placeholder="Start typing address or postcode"
                                        autocomplete="address" />
                                    <InputError class="mt-2" />
                                </div>

                                <div class="mt-3 flex justify-between gap-x-5 lg:flex-row flex-col">
                                    <div class="w-full">
                                        <InputLabel value="Country"  />
                                        <SelectInput
                                            id="country"
                                            type="text"
                                            class="mt-3"
                                            :options="countries"
                                            v-model="form.destination.country"/>
                                        <InputError class="mt-2" />
                                    </div>
                                    <div class="w-full lg:mt-0 mt-3">
                                        <InputLabel value="State"  />
                                        <SelectInput
                                            id="state"
                                            type="text"
                                            class="mt-2 flex"
                                            v-model="form.destination.state_id"/>
                                        <InputError class="mt-2" />
                                    </div>
                                    <div class="w-full lg:mt-0 mt-3">
                                        <InputLabel value="City"  />
                                        <SelectInput
                                            id="city"
                                            type="text"
                                            class="mt-2 flex"
                                            v-model="form.destination.city"/>
                                        <InputError class="mt-2" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Postcode"  />
                                    <TextInput
                                        id="postcode"
                                        type="text"
                                        class="mt-2 flex"
                                        v-model="form.destination.postcode"
                                        placeholder="Enter postcode"
                                        autocomplete="postcode" />
                                    <InputError class="mt-2" />
                                </div>


                                <div class="flex flex-col items-center justify-end mt-6">
                                    <PrimaryButton class="w-full">
                                        Next
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                    <TabPanel class="fade-in duration-300 w-full">
                        <div class="card border bg-white">
                            <h3 class="p-5 text-lg">Enter shipping details</h3>
                            <div class="p-0">
                                <hr class="p-0">
                            </div>
                            <form action="" @submit.prevent="">
                                <div class="flex flex-col gap-y-4 p-5">
                                    <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
                                        <div class="w-full">
                                            <InputLabel value="Item category" class="font-normal" />
                                            <SelectInput model-value="" place-holder="Select item category" class="mt-2 w-full" />
                                        </div>
                                        <div class="w-full">
                                            <InputLabel value="Item value" class="font-normal" />
                                            <TextInput type="number" model-value="" min="0" placeholder="Enter item value" class="mt-2" />
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel value="Item Description" class="font-normal" />
                                        <TextAreaInput model-value="" rows="7" class="mt-2" placeholder="A piece of text that clearly describes the item being packaged for shipping, it should leave no room for guesses so that Parcels Mart can know how best to handle it" />
                                    </div>
                                    <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
                                        <div class="w-full">
                                            <InputLabel value="Quantity" class="font-normal" />
                                            <TextInput type="number" model-value="" min="0" placeholder="20" class="mt-2" />
                                        </div>
                                        <div class="w-full">
                                            <InputLabel value="Weight (KG)" class="font-normal" />
                                            <TextInput type="number" model-value="" min="0" placeholder="15" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
                                        <div class="w-full">
                                            <InputLabel value="Length (CM)" class="font-normal" />
                                            <TextInput type="number" model-value="" min="0" placeholder="20" class="mt-2" />
                                        </div>
                                        <div class="w-full">
                                            <InputLabel value="Width (CM)" class="font-normal" />
                                            <TextInput type="number" model-value="" min="0" placeholder="15" class="mt-2" />
                                        </div>
                                        <div class="w-full">
                                            <InputLabel value="Height (CM)" class="font-normal" />
                                            <TextInput type="number" model-value="" min="0" placeholder="15" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
                                        <PrimaryButton>Next</PrimaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </TabPanel>
                    <TabPanel class="fade-in duration-300 w-full">
                        d
                        <!--                    <QuoteTabDocumentForm />-->
                    </TabPanel>
                    <TabPanel class="fade-in duration-300 w-full">
                        e
                        <!--                    <QuoteTabDocumentForm />-->
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
        <ChooseAddress :is-open="isChooseAddressOpen" @closeQuoteModal="(value) => toggleQuote(value)" :countries="countries" :addresses="addresses" />
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
