<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm} from "@inertiajs/vue3";

defineProps({
    countries: Array
});


const form  = useForm({
    country_from: "0",
    address_from: "",
    country_to: "0",
    address_to: "",
    quantity: "",
    weight: "",
    width: "",
    height: "",
    length: "",
    name: "",
    email: "",
    phone: "",
    //metric: "",

});

const submit = () => {
    form.post(route('send.quote.form'), {
        onFinish: () => form.reset(),
    });
}
</script>

<template>
    <form @submit.prevent="submit">
        <div>
            <TextInput
                id="quantity"
                type="text"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Enter your name"
                v-model="form.name"/>
        </div>
        <div class="mt-4">
            <TextInput
                id="quantity"
                type="text"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Phone number"
                v-model="form.phone"/>
        </div>
        <div class="mt-4">
            <TextInput
                id="quantity"
                type="email"
                class="mt-1 w-full"
                autocomplete="off"
                placeholder="Email"
                v-model="form.email"/>
        </div>
        <div class="mt-4">
            <InputLabel value="From" />
            <div class="grid lg:grid-cols-2 gap-x-5 w-full">
                <SelectInput
                    place-holder="Select Country"
                    class="block w-full"
                    v-model="form.country_from"
                    :options="countries"
                />

                <TextInput
                    id="country_from"
                    type="text"
                    class="mt-3 lg:mt-1 w-1/2"
                    required
                    autofocus
                    autocomplete="off"
                    placeholder="Enter address"
                    v-model="form.address_from"/>
            </div>
        </div>
        <div class="mt-4">
            <InputLabel value="To" />
            <div class="grid lg:grid-cols-2 gap-x-5 w-full">
                <SelectInput
                    place-holder="Select Country"
                    class="block w-full"
                    v-model="form.country_to"
                    :options="countries"
                    required
                />

                <TextInput
                    id="email"
                    type="text"
                    class="mt-3 lg:mt-1 w-1/2"
                    required
                    autocomplete="off"
                    placeholder="Enter address"
                    v-model="form.address_to"/>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-x-5 mt-5">
            <TextInput
                id="quantity"
                type="number"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Quantity"
                v-model="form.quantity"/>

            <TextInput
                id="email"
                type="number"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Weight"
                v-model="form.weight"/>
            <TextInput
                id=""
                type="number"
                class="mt-1 w-full block"
                required
                autocomplete="off"
                placeholder="Length"
                v-model="form.length"/>
        </div>
        <div class="grid lg:grid-cols-3 gap-x-5 mt-5">
            <TextInput
                id=""
                type="number"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Width"
                v-model="form.width"/>

            <TextInput
                id=""
                type="number"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Height"
                v-model="form.height"/>
<!--            <SelectInput
                id=""
                type="number"
                class="mt-1 w-full"
                required
                autofocus
                autocomplete="off"
                place-holder="Select metric"
                model-value=""/>-->
        </div>
        <div class="flex flex-col items-end justify-end mt-10">
            <PrimaryButton class="w-max text-center px-12" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Get A Quote
            </PrimaryButton>
        </div>
    </form>
</template>

<style scoped>

</style>
