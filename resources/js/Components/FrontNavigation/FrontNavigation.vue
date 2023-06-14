<script setup>
import { ref } from 'vue'
import { Dialog, DialogPanel } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import logo from '/resources/images/logo.png';
import {Head, Link} from "@inertiajs/vue3";

const navigation = [
    { name: 'Tracking', href: route('tracking') },
    { name: 'Services', href: route('services') },
    { name: 'Company', href: route('about') },
    { name: 'Contact', href: route('contact') },
]

const mobileMenuOpen = ref(false)
</script>

<template>
    <header class="relative inset-x-0 top-0 z-50">
        <nav class="flex items-center justify-between" aria-label="">
            <div class="flex lg:flex-1">
                <Link :href="route('home') + '#top'" class="-m-1.5 p-1.5">
                    <span class="sr-only">Parcel Smart</span>
                    <img :src="logo" alt="parcel-mart-logo" class="w-28 rounded-full">
                </Link>
            </div>
            <div class="flex lg:hidden">
                <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 duration-300" @click="mobileMenuOpen = true">
                    <span class="sr-only">Open main menu</span>
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>
            </div>
            <div class="hidden lg:flex lg:gap-x-12">
                <Link v-for="item in navigation" :key="item.name" :href="item.href" class="text-sm font-semibold leading-6 text-gray-900 hover:text-primary">{{ item.name }}</Link>
            </div>
            <div class="hidden lg:flex lg:flex-1 lg:justify-end gap-x-5">
                <Link :href="route('home') + '#quote-form'" class="text-sm font-semibold leading-6 py-1 px-8 rounded-lg hover:text-white bg-transparent hover:bg-primary text-primary border-2 border-primary">Get Quote</Link>
                <Link :href="route('login')" class="text-sm font-semibold leading-6 py-1 px-8 rounded-lg hover:text-primary bg-primary hover:bg-white text-white border-2 border-primary">Log In</Link>
            </div>
        </nav>
        <Dialog as="div" class="lg:hidden" @close="mobileMenuOpen = false" :open="mobileMenuOpen">
            <div class="fixed inset-0 z-50" />
            <DialogPanel class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10 duration-300">
                <div class="flex items-center justify-between">
                    <Link :href="route('home')" class="-m-1.5 p-1.5">
                        <span class="sr-only">Your Company</span>
                        <img :src="logo" alt="parcel-mart-logo" class="w-28 rounded-full">
                    </Link>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700 duration-300" @click="mobileMenuOpen = false">
                        <span class="sr-only">Close menu</span>
                        <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <a v-for="item in navigation" :key="item.name" :href="item.href" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:text-primary-dark hover:bg-gray-50">{{ item.name }}</a>
                        </div>
                        <div class="py-6 flex flex-col gap-y-2">
                            <Link :href="route('home') + '#quote-form'" class="text-sm font-semibold leading-6 py-1 px-8 rounded-lg hover:text-white bg-transparent hover:bg-primary text-primary border-2 border-primary">Get Quote</Link>
                            <Link :href="route('login')" class="text-sm font-semibold leading-6 py-1 px-8 rounded-lg hover:text-primary bg-primary hover:bg-white text-white border-2 border-primary">Log In</Link>
                        </div>
                    </div>
                </div>
            </DialogPanel>
        </Dialog>
    </header>
</template>

<style scoped>

</style>
