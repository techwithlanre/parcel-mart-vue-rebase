<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import {Head, useForm, usePage} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const page = usePage()

defineProps({
    countries: Array,
    address: Object
})

const form = useForm({
    address: page.props.address.address,
    landmark: page.props.address.landmark,
    country_id: page.props.address.country_id,
    state_id: page.props.address.state_id,
    city_id: page.props.address.city_id,
    business_name: page.props.address.address_contacts[0].contact_name,
    contact_name: page.props.address.address_contacts[0].contact_name,
    contact_email: page.props.address.address_contacts[0].contact_email,
    contact_phone: page.props.address.address_contacts[0].contact_phone,
    postcode: page.props.address.postcode,
});

const submit = () => {
    form.put(route('address-book.update', page.props.address.id), {
        onFinish: () => form.reset(),
    })
}

</script>

<template>
    <Head title="Edit Address" />
    <DashboardLayout page-title="Edit Address">
        <div >
            <div class="mx-auto lg:mt-17 mt max-w-2xl p-10 bg-white  rounded-2xl border">
                <div>
                    <h3 class="font-bold text-2xl ">Edit address</h3>
                </div>
                <form class="mt-6" @submit.prevent="submit">
                    <div class="">
                        <InputLabel value="Contact Name *"  />
                        <TextInput
                            id="contact_name"
                            v-model="form.contact_name"
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
                                v-model="form.contact_phone"
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
                                v-model="form.contact_email"
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
                            v-model="form.business_name"
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
                            v-model="form.address"
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
                            v-model="form.landmark"
                            required
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
                                v-model="form.country_id"/>
                            <InputError class="mt-2" />
                        </div>
                        <div class="w-full lg:mt-0 mt-3">
                            <InputLabel value="State"  />
                            <SelectInput
                                id="state"
                                type="text"
                                class="mt-2 flex"
                                v-model="form.state_id"/>
                            <InputError class="mt-2" />
                        </div>
                        <div class="w-full lg:mt-0 mt-3">
                            <InputLabel value="City"  />
                            <SelectInput
                                id="city"
                                type="text"
                                class="mt-2 flex"
                                v-model="form.city"/>
                            <InputError class="mt-2" />
                        </div>
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Postcode"  />
                        <TextInput
                            id="postcode"
                            type="text"
                            class="mt-2 flex"
                            v-model="form.postcode"
                            autofocus
                            placeholder="Enter postcode"
                            autocomplete="postcode" />
                        <InputError class="mt-2" />
                    </div>


                    <div class="flex flex-col items-center justify-end mt-6">
                        <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Update Address
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>

</style>
