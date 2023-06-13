<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import { ChevronUpIcon } from "@heroicons/vue/20/solid/index.js";

defineProps({
    title: {
        type: String,
        required: true,
    },

    body: {
        type: String,
        required: false
    },
});

</script>

<template>
    <Disclosure v-slot="{ open }">
        <div>
            <DisclosureButton
                class="flex w-full justify-between rounded-lg
                                bg-background bg-opacity-40 px-5 py-5 text-left text-sm font-medium
                                text-primary hover:bg-primary hover:text-white focus:outline-none focus-visible:ring
                                focus-visible:ring-background focus-visible:ring-opacity-75 duration-300 border border-background" >
                <span class="lg:text-2xl text-sm lg:text-lg">{{ title }}</span>
                <ChevronUpIcon
                    :class="open ? 'rotate-180 transform' : ''"
                    class="h-5 w-5 text-gray-200 duration-300"
                />
            </DisclosureButton>
            <DisclosurePanel class="px-4 pb-2 lg:text-2xl text-sm pt-4 text-gray-500 duration-300">{{ body }}</DisclosurePanel>
        </div>
    </Disclosure>
</template>

<style scoped>

</style>
