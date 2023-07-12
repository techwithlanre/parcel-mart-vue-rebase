<script>
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import {Head, Link, useForm, usePage} from "@inertiajs/vue3";
    import {
        Tab,
        TabGroup,
        TabList,
        TabPanel,
        TabPanels, TransitionRoot
    } from "@headlessui/vue";
    import PrimaryButton from "@/Components/PrimaryButton.vue";
    import TextInput from "@/Components/TextInput.vue";
    import SelectInput from "@/Components/SelectInput.vue";
    import InputLabel from "@/Components/InputLabel.vue";
    import InputError from "@/Components/InputError.vue";
    import {toast} from "vue3-toastify";
    import TextAreaInput from "@/Components/TextAreaInput.vue";
    import {twMerge} from "tailwind-merge";

    export default {
        components: {
            TransitionRoot,
            TextAreaInput,
            PrimaryButton,
            SelectInput,
            Tab, TabPanel, TabPanels, TabList, TabGroup, AuthenticatedLayout, InputError, InputLabel, TextInput, Link, Head },
        props: {
            countries: Array,
            addresses: Array,
            categories: Array,
        },
        mounted() {
            console.log('component mounted')
        },

        data() {
            return {
                showOriginAddressForm: true,
                showOriginDestinationForm: false,
                showPackageDetailsForm: false,
                selectedOriginAddress: 0,
                selectedDestinationAddress: 0,
                selectedTab: 0,
                originCities: [],
                destinationCities: [],
                state: 0,
                originStates: [],
                destinationStates: [],
                originDisabled: false,
                destinationDisabled: true,
                packageDetailsDisabled: true,
                page: usePage(),
                form: useForm({
                    origin: {
                        contact_name: '',
                        contact_phone: '',
                        contact_email: '',
                        business_name: '',
                        address_1: '',
                        landmark: '',
                        address_2: '',
                        country: 0,
                        state: 0,
                        city: 0,
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
                    },
                    shipment: {
                        category: 0,
                        value: '',
                        description: '',
                        quantity: '',
                        weight: '',
                        height: '',
                        length: '',
                        width: ''
                    }
                })
            }
        },
        methods:{
            twMerge,
            getOriginStates: function() {
                axios.get('/api/states/' + this.form.origin.country).then(function(response){
                    this.originStates = response.data.states;
                }.bind(this));
            },

            getOriginCities: function() {
                axios.get('/api/cities/' + this.form.origin.state).then(function(response){
                    this.originCities = response.data.cities;
                }.bind(this));
            },

            getDestinationStates: function() {
                axios.get('/api/states/' + this.form.destination.country).then(function(response){
                    this.destinationStates = response.data.states;
                }.bind(this));
            },

            getDestinationCities: function() {
                axios.get('/api/cities/' + this.form.destination.state).then(function(response){
                    this.destinationCities = response.data.cities;
                }.bind(this));
            },

            changeTab(index) {
                this.selectedTab = index
            },

            validateForm(index) {
                this.changeTab(index)
            },

            initialize: function () {
                this.form.post(route('shipment.initialize'), {

                })
            },

            populateOriginAddress: function(address) {
                this.form.origin.address_1 = address.address;
                this.form.origin.contact_name = address.address_contacts[0].contact_name;
                this.form.origin.contact_phone = address.address_contacts[0].contact_phone;
                this.form.origin.contact_email = address.address_contacts[0].contact_email;
                this.form.origin.business_name = address.address_contacts[0].business_name;
                this.form.origin.landmark = address.landmark;
                this.form.origin.country = address.country_id;
                this.form.origin.state = address.state_id;
                this.form.origin.city = address.city_id;
                this.form.origin.postcode = address.postcode;
            },

            populateDestinationAddress: function(address) {
                this.form.destination.address_1 = address.address;
                this.form.destination.contact_name = address.address_contacts[0].contact_name;
                this.form.destination.contact_phone = address.address_contacts[0].contact_phone;
                this.form.destination.contact_email = address.address_contacts[0].contact_email;
                this.form.destination.business_name = address.address_contacts[0].business_name;
                this.form.destination.landmark = address.landmark;
                this.form.destination.country = address.country_id;
                this.form.destination.state = address.state_id;
                this.form.destination.city = address.city_id;
                this.form.destination.postcode = address.postcode;
            },

            backToOrigin: function () {
                this.originDisabled = false;
                this.changeTab(0)
            },

            gotoDestination: function () {
                this.originDisabled = true;
                this.destinationDisabled = false;
            },
            gotoPackageDetails: function () {
                this.originDisabled = true;
                this.destinationDisabled = true;
                this.packageDetailsDisabled = false;
            }
        },
        created: function(){
            this.form.origin.country = this.page.props.auth.user.country_id;
            this.getOriginStates()
            this.form.destination.country = this.page.props.auth.user.country_id;
            this.getDestinationStates()
        }
    }
</script>

<template>

    <AuthenticatedLayout page-title="Create Shipment">
        <Head title="Create Shipment"/>
        <div class="flex lg:flex-row flex-col gap-x-10 gap-y-10">
            <TabGroup :selected-index="selectedTab" :default-index="0">
                <TabList class="space-x-1 rounded-2xl bg-white border duration-300 lg:w-1/6 flex flex-col gap-y-3 p-5">
                    <Tab as="template" :disabled="originDisabled" v-slot="{ selected }" class="duration-300" v-on:click="changeTab(0)">
                        <button
                            :class="['w-full text-left px-5 rounded-lg lg:py-5 border bg-gray-100 py-3 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-primary text-white border' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]">
                            Origin Address
                        </button>
                    </Tab>
                    <Tab as="template" :disabled="destinationDisabled" v-slot="{ selected }" class="duration-300" v-on:click="changeTab(1)">
                        <button
                            :class="['w-full text-left px-5 rounded-lg lg:py-5 border bg-gray-100 py-3 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-primary text-white shadow border' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]">
                            Destination Address
                        </button>
                    </Tab>
                    <Tab as="template" :disabled="packageDetailsDisabled" v-slot="{ selected }" class="duration-300" v-on:click="changeTab(2)">
                        <button
                            :class="['w-full text-left px-5 rounded-lg lg:py-5 border bg-gray-100 py-3 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-primary text-white shadow border' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]">
                            Shipping Details
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
                            <div class="mt-3 flex justify-between gap-x-5 lg:flex-row flex-col px-6 mb-5">
                                <div class="w-full">
                                    <InputLabel value="Select origin address"  />
                                    <select v-model="selectedOriginAddress" v-on:change="populateOriginAddress(selectedOriginAddress)" class="mt-2 bg-transparent border-gray-300 rounded-md w-full outline-none focus:border-primary focus:ring-2 focus:ring-background">
                                        <option value="0" selected disabled></option>
                                        <option v-for="item in addresses" :value="item" :key="item">
                                            {{ item.address_contacts[0].contact_name }} | {{ item.address }}
                                        </option>
                                    </select>

                                    <InputError class="mt-2" />
                                </div>
                            </div>
                            <div class="flex justify-center">
                                OR
                            </div>
                            <form @submit.prevent="validateForm(1)" class="duration-300">
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
                                            placeholder="Start typing address or postcode"
                                            autocomplete="address" />
                                        <InputError class="mt-2" />
                                    </div>

                                    <div class="mt-3 flex justify-between gap-x-5 lg:flex-row flex-col">
                                        <div class="w-full">
                                            <InputLabel value="Country"  />
                                            <SelectInput
                                                id="country"
                                                class="mt-2"
                                                v-on:change="getOriginStates"
                                                :options="countries"
                                                required
                                                v-model="form.origin.country"/>
                                            <InputError class="mt-2" />
                                        </div>
                                        <div class="w-full lg:mt-0 mt-3">
                                            <InputLabel value="State"  />
                                            <SelectInput
                                                id="state"
                                                class="mt-2 flex"
                                                v-on:change="getOriginCities"
                                                :options="originStates"
                                                required
                                                v-model="form.origin.state"/>
                                            <InputError class="mt-2" />
                                        </div>
                                        <div class="w-full lg:mt-0 mt-3">
                                            <InputLabel value="City"  />
                                            <SelectInput
                                                id="city"
                                                type="text"
                                                class="mt-2 flex"
                                                :options="originCities"
                                                required
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
                                        <PrimaryButton type="submit" class="w-full" @click="gotoDestination">
                                            Next
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </TabPanel>
                    <TabPanel class="fade-in duration-300 w-full">
                        <div class="card border bg-white">
                            <h3 class="p-5 text-lg">Enter destination address details</h3>
                            <div class="p-0">
                                <hr class="p-0">
                            </div>
                            <div class="mt-3 flex justify-between gap-x-5 lg:flex-row flex-col px-6 mb-5">
                                <div class="w-full">
                                    <InputLabel value="Select destination address"  />
                                    <select v-model="selectedDestinationAddress" v-on:change="populateDestinationAddress(selectedDestinationAddress)" class="mt-2 bg-transparent border-gray-300 rounded-md w-full outline-none focus:border-primary focus:ring-2 focus:ring-background">
                                        <option value="0" selected disabled></option>
                                        <option v-for="item in addresses" :value="item" :key="item">
                                            {{ item.address_contacts[0].contact_name }} | {{ item.address }}
                                        </option>
                                    </select>

                                    <InputError class="mt-2" />
                                </div>
                            </div>
                            <div class="flex justify-center">
                                OR
                            </div>
                            <form @submit.prevent="validateForm(2)">
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
                                            placeholder="Start typing address or postcode"
                                            autocomplete="address" />
                                        <InputError class="mt-2" />
                                    </div>

                                    <div class="mt-3 flex justify-between gap-x-5 lg:flex-row flex-col">
                                        <div class="w-full">
                                            <InputLabel value="Country" />
                                            <SelectInput
                                                id="country"
                                                type="text"
                                                class="mt-2"
                                                v-on:change="getDestinationStates"
                                                :options="countries"
                                                required
                                                v-model="form.destination.country"/>
                                            <InputError class="mt-2" />
                                        </div>
                                        <div class="w-full lg:mt-0 mt-3">
                                            <InputLabel value="State"  />
                                            <SelectInput
                                                id="state"
                                                type="text"
                                                class="mt-2 flex"
                                                required
                                                v-on:change="getDestinationCities"
                                                :options="destinationStates"
                                                v-model="form.destination.state"/>
                                            <InputError class="mt-2" />
                                        </div>
                                        <div class="w-full lg:mt-0 mt-3">
                                            <InputLabel value="City"  />
                                            <SelectInput
                                                id="city"
                                                type="text"
                                                class="mt-2 flex"
                                                :options="destinationCities"
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


                                    <div class="flex flex-row items-center justify-end mt-6 gap-x-10">
                                        <PrimaryButton @click="backToOrigin" type="button" :class="twMerge('bg-background border')">Previous</PrimaryButton>
                                        <PrimaryButton type="submit" class="w-full" @click="gotoPackageDetails">
                                            Next
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </TabPanel>

                    <TabPanel class="fade-in duration-300 w-full">
                        <div class="card border bg-white">
                            <h3 class="p-5 text-lg">Enter shipping details</h3>
                            <div class="p-0">
                                <hr class="p-0">
                            </div>
                            <form action="" @submit.prevent="initialize">
                                <div class="flex flex-col gap-y-4 p-5">
                                    <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
                                        <div class="w-full">
                                            <InputLabel value="Item category" class="font-normal" />
                                            <SelectInput required v-model="form.shipment.category" :options="categories" place-holder="Select item category" class="mt-2 w-full" />
                                        </div>
                                        <div class="w-full">
                                            <InputLabel value="Item value" class="font-normal" />
                                            <TextInput required type="number" v-model="form.shipment.value" min="1" placeholder="Enter item value" class="mt-2" />
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel value="Item Description" class="font-normal" />
                                        <TextAreaInput v-model="form.shipment.description" rows="7" class="mt-2" required placeholder="A piece of text that clearly describes the item being packaged for shipping, it should leave no room for guesses so that Parcels Mart can know how best to handle it" />
                                    </div>
                                    <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
                                        <div class="w-full">
                                            <InputLabel value="Quantity" class="font-normal" />
                                            <TextInput required type="number" v-model="form.shipment.quantity" min="1" placeholder="20" class="mt-2" />
                                        </div>
                                        <div class="w-full">
                                            <InputLabel value="Weight (KG)" class="font-normal" />
                                            <TextInput required type="number" v-model="form.shipment.weight" min="1" placeholder="15" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
                                        <div class="w-full">
                                            <InputLabel value="Length (CM)" class="font-normal" />
                                            <TextInput required type="number" v-model="form.shipment.length" min="1" placeholder="12" class="mt-2" />
                                        </div>
                                        <div class="w-full">
                                            <InputLabel value="Width (CM)" class="font-normal" />
                                            <TextInput required type="number" v-model="form.shipment.width" min="1" placeholder="8" class="mt-2" />
                                        </div>
                                        <div class="w-full">
                                            <InputLabel value="Height (CM)" class="font-normal" />
                                            <TextInput required type="number" v-model="form.shipment.height" min="1" placeholder="4" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
                                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Next</PrimaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
<!--        <ChooseAddress :is-open="isChooseAddressOpen" @closeQuoteModal="(value) => toggleQuote(value)" :countries="countries" :addresses="addresses" />-->
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
