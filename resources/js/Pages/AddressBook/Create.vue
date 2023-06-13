<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import {Head, useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";


const form = useForm({
    address: '',
    business_name: '',
    contact_name: '',
    contact_email: '',
    contact_phone: '',
});

const submit = () => {
    form.post(route('address-book/store'), {
        onFinish: () => form.reset(),
    })
}

</script>

<template>
    <Head title="Create Addressss" />
    <AuthenticatedLayout>
        <div >
            <div class="mx-auto lg:mt-17 mt-10 max-w-2xl p-10 bg-white  rounded-2xl border">
                <div>
                    <h3 class="font-bold text-2xl ">Create a new address</h3>
                </div>
                <form class="mt-10" @submit.prevent="submit">
                    <div>
                        <InputLabel value="Find Address *"  />
                        <TextInput
                            id="find_address"
                            type="text"
                            class="mt-3 flex"
                            v-model="form.address"
                            required
                            autofocus
                            placeholder="Start typing address or postcode"
                            autocomplete="address" />
                        <InputError class="mt-2" />
                    </div>

                    <div class="mt-5">
                        <InputLabel value="Business Name"  />
                        <TextInput
                            id="business_name"
                            type="text"
                            v-model="form.business_name"
                            class="mt-3 flex"
                            placeholder="Enter Business Name"
                            autocomplete="business_name" />

                        <InputError class="mt-2" />
                    </div>

                    <div class="mt-5">
                        <InputLabel value="Contact Name *"  />
                        <TextInput
                            id="contact_name"
                            v-model="form.contact_name"
                            type="text"
                            class="mt-3 flex"
                            required
                            placeholder="Enter Contact Name"
                            autocomplete="contact_name" />

                        <InputError class="mt-2" />
                    </div>
                    <div class="mt-5">
                        <InputLabel value="Contact Email"  />
                        <TextInput
                            id="contact_email"
                            v-model="form.contact_email"
                            type="text"
                            class="mt-3 flex"
                            placeholder="Enter Contact Email"
                            autocomplete="contact_email" />

                        <InputError class="mt-2" />
                    </div>
                    <div class="mt-5">
                        <InputLabel value="Contact Phone *"  />
                        <TextInput
                            id="contact_phone"
                            v-model="form.contact_phone"
                            type="text"
                            class="mt-3 flex"
                            required
                            placeholder="Enter Contact Phone"
                            autocomplete="contact_email" />

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
