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
            <InputLabel value="From" />
            <div class="grid lg:grid-cols-2 gap-x-5 w-full">
                <SelectInput
                    place-holder="Select Country"
                    class="block w-full"
                    :model-value="form.country_from"
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
                    :model-value="form.address_from"/>
            </div>
        </div>
        <div class="mt-4">
            <InputLabel value="To" />
            <div class="grid lg:grid-cols-2 gap-x-5 w-full">
                <SelectInput
                    place-holder="Select Country"
                    class="block w-full"
                    :model-value="form.country_to"
                    :options="countries"
                    required
                />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-3 lg:mt-1 w-1/2"
                    required
                    autocomplete="off"
                    placeholder="Enter address"
                    :model-value="form.address_to"/>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-x-5 mt-5">
            <TextInput
                id="email"
                type="number"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Quantity"
                model-value=""/>

            <TextInput
                id="email"
                type="number"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Weight"
                model-value=""/>
            <TextInput
                id=""
                type="number"
                class="mt-1 w-full block"
                required
                autocomplete="off"
                placeholder="Length"
                model-value=""/>
        </div>
        <div class="grid lg:grid-cols-3 gap-x-5 mt-5">
            <TextInput
                id=""
                type="number"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Width"
                model-value=""/>

            <TextInput
                id=""
                type="number"
                class="mt-1 w-full"
                required
                autocomplete="off"
                placeholder="Height"
                model-value=""/>
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
