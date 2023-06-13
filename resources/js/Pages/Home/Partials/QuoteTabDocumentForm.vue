<script setup>

import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
</script>

<template>
    <form action="">
        <div class="grid lg:grid-cols-2 gap-x-5  w-full">
            <TextInput
                id="email"
                type="email"
                class="mt-1 w-1/2"
                required
                autofocus
                autocomplete="off"
                placeholder="Postcode"
                model-value=""/>

            <TextInput
                id="email"
                type="email"
                class="mt-3 lg:mt-1 w-1/2"
                required
                autofocus
                autocomplete="off"
                placeholder="Postcode"
                model-value=""/>
        </div>

        <div class="grid lg:grid-cols-3 gap-x-5 mt-5">
            <TextInput
                id="email"
                type="number"
                class="mt-1 w-full"
                required
                autofocus
                autocomplete="off"
                placeholder="Quantity"
                model-value=""/>

            <TextInput
                id="email"
                type="number"
                class="mt-1 w-full"
                required
                autofocus
                autocomplete="off"
                placeholder="Weight"
                model-value=""/>
            <TextInput
                id=""
                type="number"
                class="mt-1 w-full block"
                required
                autofocus
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
                autofocus
                autocomplete="off"
                placeholder="Width"
                model-value=""/>
            <TextInput
                id=""
                type="number"
                class="mt-1 w-full"
                required
                autofocus
                autocomplete="off"
                placeholder="Height"
                model-value=""/>
            <SelectInput
                id=""
                type="number"
                class="mt-1 w-full"
                required
                autofocus
                autocomplete="off"
                place-holder="Select metric"
                model-value=""/>
        </div>
        <div class="flex flex-col items-end justify-end mt-10">
            <PrimaryButton class="w-max text-center px-12" >
                Get A Quote
            </PrimaryButton>
        </div>
    </form>
</template>

<style scoped>

</style>
