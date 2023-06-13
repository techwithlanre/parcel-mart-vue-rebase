<script setup>
import {defineComponent} from 'vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link} from "@inertiajs/vue3";



</script>

<template>
    <Head title="Address Book" />
    <AuthenticatedLayout>
        <div>
            <div class="flex flex-col justify-end items-end">
                <Link :href="route('address-book.create')"
                      class="bg-primary px-3 py-2  text-sm text-white hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 mb-10 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Address
                </Link>
                <input placeholder="Search" class="p-2 border rounded-lg">
            </div>
            <div class="mt-10 border rounded-3xl bg-white">
                <table class="w-full">
                    <thead class="rounded-t-3xl">
                        <tr class="text-sm font-medium text-gray-700 border-b border-gray-200 px-5">
                            <td class="font-bold p-7">Name</td>
                            <td class="font-bold p-7">Phone</td>
                            <td class="font-bold p-7">Country</td>
                            <td class="font-bold p-7">City</td>
                            <td class="font-bold p-7">Postcode</td>
                            <td class="font-bold p-7"></td>
                        </tr>
                    </thead>
                    <tbody>
                    <tr v-for="i in 3" class="hover:bg-gray-100 transition-colors group border-b px-5">
                        <td class="text-sm p-7">John Doe</td>
                        <td class="text-sm p-7">08123456789</td>
                        <td class="text-sm p-7">Nigeria</td>
                        <td class="text-sm p-7">Lagos</td>
                        <td class="text-sm p-7">200423</td>
                        <td class="">
                            <div class="group-hover:flex group-hover:items-center group-hover:text-gray-500 group-hover:gap-x-2">
                                <button class="p-2 hover:rounded-md hover:bg-gray-200">
                                    <InvisibleIcon class="w-6 h-6 fill-current" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="flex gap-x-2 justify-center p-8">
                    <button class="flex justify-center items-center w-8 h-8">
                        <ChevronLeftIcon class="w-6 h-6 to-gray-800 stroke-current hover:text-primary" />
                    </button>
                    <button
                        v-for="i in 6"
                        class="flex items-center justify-center w-8 h-8 font-medium rounded-full"
                        :class=" i === 1 ? 'bg-primary text-white' : 'text-gray-400 hover:bg-primary-dark hover:text-white duration-300'">
                        {{ i }}
                    </button>
                    <button class="flex justify-center items-center w-8 h-8">
                        <ChevronRightIcon class="w-6 h-6 to-gray-800 stroke-current hover:text-primary" />
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
