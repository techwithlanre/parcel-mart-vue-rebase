<script setup>

import {Tab, TabGroup, TabList, TabPanel, TabPanels} from "@headlessui/vue";
import QuoteTabParcelForm from "@/Pages/Home/Partials/QuoteTabParcelForm.vue";
import QuoteTabDocumentForm from "@/Pages/Home/Partials/QuoteTabDocumentForm.vue";
defineProps({
    countries: Array
})
</script>

<template>
    <QuoteTabParcelForm :countries="countries" />
<!--    <TabGroup>
        <TabList class="flex space-x-1 rounded-xl bg-gray-100 p-1 duration-300">
            <Tab as="template" v-slot="{ selected }" class="duration-300">
                <button
                    :class="['w-full rounded-lg lg:py-3 py-2 lg:text-lg font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2',
                                selected ? 'bg-white shadow' : 'text-primary hover:bg-white hover:text-primary-dark duration-300', ]" >
                    Parcel
                </button>
            </Tab>
            <Tab as="template" v-slot="{ selected }" class="duration-300">
                <button
                    :class="['w-full rounded-lg lg:py-3 lg:text-lg py-2 font-medium leading-5 text-primary',
                                'ring-white ring-opacity-60 ring-offset-2 ring-offset-background focus:outline-none focus:ring-2', selected
                                ? 'bg-white shadow' : 'text-primary hover:bg-white/[0.12] hover:text-primary-dark', ]" >
                    Document
                </button>
            </Tab>
        </TabList>

        <TabPanels class="mt-5">
            <TabPanel class="fade-in duration-300 w-full">
                <QuoteTabParcelForm :countries="countries" />
            </TabPanel>
            <TabPanel class="fade-in duration-300 w-full">
&lt;!&ndash;                <QuoteTabDocumentForm />&ndash;&gt;
            </TabPanel>
        </TabPanels>
    </TabGroup>-->
</template>

<style scoped>

</style>
