<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import {Head, useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";

defineProps({
    countries: Array
})

const form = useForm({
    address: '',
    address_2: '',
    landmark: '',
    country_id: 161,
    state_id: 77002,
    city_id: 76932,
    business_name: '',
    contact_name: '',
    contact_email: '',
    contact_phone: '',
    postcode: '',
});

const submit = () => {
    form.post(route('address-book.store'), {
        onFinish: () => form.reset(),
    })
}

</script>

<template>
    <Head title="Create Address" />
    <AuthenticatedLayout page-title="Create Address">
        <div >
            <div class="mx-auto lg:mt-17 mt max-w-2xl p-10 bg-white  rounded-2xl shadow">
                <div>
                    <h3 class="font-bold text-2xl ">Create a new address</h3>
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
                            autofocus
                            placeholder=""
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
                                placeholder=""
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
                                placeholder=""
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
                            placeholder=""
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
                            
                            maxlength="45"
                            placeholder=""
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
                            placeholder=""
                            autocomplete="address" />
                        <InputError class="mt-2" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Address Line 2"  />
                        <TextInput
                            id="address_2"
                            type="text"
                            class="mt-2 flex"
                            v-model="form.address_2"
                            maxlength="45"
                            placeholder=""
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
                            placeholder=""
                            autocomplete="postcode" />
                        <InputError class="mt-2" />
                    </div>


                    <div class="flex flex-col items-center justify-end mt-6">
                        <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Create Address
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
