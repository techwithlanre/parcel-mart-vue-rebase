<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm, usePage} from '@inertiajs/vue3';
import SelectInput from "@/Components/SelectInput.vue";


defineProps({
    shipment: Array,
    origin: Object,
    destination: Object,
    insurance_options: Array
});

const form  = useForm({
    insurance: 0
});

</script>

<template>
    <AuthenticatedLayout page-title="Shipment Checkout">
        <Head title="Shipment Checkout"/>
        <div class="flex lg:flex-row flex-col justify-between gap-10">
            <div class="w-full card bg-white border p-5">
                <h1 class="font-bold text-xl">Contact Information</h1>
                <h3>Sender Information</h3>
                <div class="relative overflow-x-auto border rounded-2xl mt-5">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <tbody>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Name
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.contact_name }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Phone
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.contact_phone }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Email
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.contact_email }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Address 1
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.address_1 }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Landmark
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.landmark }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Address 2
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.address_2 }}
                            </td>
                        </tr>
                        <tr class="bg-white dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Location
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.city }}, {{ origin.state }}, {{ origin.country }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="mt-10">Receiver Information</h3>
                <div class="relative overflow-x-auto border rounded-2xl mt-">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <tbody>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Name
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.contact_name }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Phone
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.contact_phone }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Email
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.contact_email }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Address 1
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.address_1 }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Landmark
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.landmark }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Address 2
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.address_2 }}
                            </td>
                        </tr>
                        <tr class="bg-white dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Location
                            </th>
                            <td class="px-6 py-4">
                                {{ origin.city }}, {{ origin.state }}, {{ origin.country }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card p-5  w-full bg-white">
                <h1 class="font-bold text-xl mb-10">Shipment Information</h1>
                <div class="relative overflow-x-auto border rounded-2xl mt-">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <tbody v-for="item in shipment.shipment_items" :key="item.id" >
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Item Category
                                </th>
                                <td class="px-6 py-4">
                                    {{ origin.contact_name }}
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Contact Name
                                </th>
                                <td class="px-6 py-4">
                                    {{ origin.contact_name }}
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Contact Name
                                </th>
                                <td class="px-6 py-4">
                                    {{ origin.contact_name }}
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Contact Name
                                </th>
                                <td class="px-6 py-4">
                                    {{ origin.contact_name }}
                                </td>
                            </tr>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Contact Name
                                </th>
                                <td class="px-6 py-4">
                                    {{ origin.contact_name }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-for="item in shipment.shipment_items" :key="item.id" class="flex flex-col gap-5">
                    <p>Item Category: <span>{{ item.item_category_id }}</span></p>
                    <p>Value: {{ item.value }}</p>
                    <p>Description: {{ item.description }}</p>
                    <p>Quantity: {{ item.quantity }}</p>
                    <p>Weight: {{ item.weight }}kg</p>
                    <p>Height: {{ item.height }}cm</p>
                    <p>Length: {{ item.length }}cm</p>
                </div>
            </div>
            <div class="card p-5 border w-full bg-white">
                <h1 class="font-bold text-xl">Payment Information</h1>
                <div>
                    <p>Amount: {{ shipment.price }}</p>
                    <p>Tax: {{ shipment.tax }}</p>
                    <p>Amount After Tax {{ shipment.price + shipment.tax }}</p>
                </div>
                <div class="mt-10">
                    <h3>Choose an Insurance Option</h3>
                    <SelectInput v-model="form.insurance" :options="insurance_options" />
                    <div class="mt-5 p-5 card border duration-300 transition-all flex flex-row justify-between items-center" v-if="form.insurance.length > 0">
                        <div>
                            <div class="text-lg font-bold">{{ insurance_options[form.insurance - 1].name }}</div>
                            <div>Covers damages upto {{ insurance_options[form.insurance - 1].cover }}</div>
                        </div>
                        <div class="text-green-600 bg-background px-4 py-1 rounded-full">{{ insurance_options[form.insurance - 1].cover }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
