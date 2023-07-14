<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm, usePage} from '@inertiajs/vue3';
import {toast} from "vue3-toastify";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";

const page = usePage()
defineProps({
    shipment: Array,
    origin: Object,
    destination: Object,
    insurance_options: Array,
    shipping_rate_log: Object,
    origin_location: Array,
    destination_location: Array,
    item_category: Object
});

const form  = useForm({
    insurance: 0,
    option: '',
    shipment_id: page.props.shipment.id
});

const pay = () => {
    //do all validation here
    if (form.option === '') {
        return toast.error('Select a shipment option');
    }

    form.post(route('shipment.book'), {

    });
}

</script>

<template>
    <AuthenticatedLayout page-title="Shipment Checkout">
        <Head title="Shipment Checkout"/>
        <div class="flex lg:flex-row flex-col justify-between gap-10 mt-10">
            <div class="card p-5 border bg-white w-full">
                <h1 class="font-bold text-xl">Shipment Status</h1>
                <div>Tracking Number: <span class="font-bold text-primary">{{ shipment.number }}</span></div>

                <div>
                    <h2 class="sr-only">Steps</h2>
                    <div class="mt-10">
                        <div class="overflow-hidden rounded-full bg-gray-200">
                            <div class="h-2 w-1/3 rounded-full bg-primary"></div>
                        </div>
                        <ol class="mt-4 grid grid-cols-3 text-sm font-medium text-gray-500">
                            <li class="flex items-center justify-start text-primary sm:gap-1.5">
                                <span class="sm:inline"> Processing </span>
                            </li>

                            <li class="flex items-center justify-center sm:gap-1.5">
                                <span class="sm:inline"> In Transit </span>
                            </li>

                            <li class="flex items-center justify-end sm:gap-1.5">
                                <span class="sm:inline"> Delivered </span>
                            </li>
                        </ol>
                    </div>
                </div>

            </div>
            <div class="card p-5 border bg-white w-full">
                <h1 class="font-bold text-xl">History</h1>
            </div>
        </div>
        <div class="flex lg:flex-row flex-col justify-between gap-10 mt-10">
            <div class="w-full card bg-white p-5">
                <h1 class="font-bold text-xl">Contact Information</h1>
                <h3>Sender Information</h3>
                <div class="relative overflow-x-auto border rounded-2xl mt-5">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <tbody>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Name
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_name }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Phone
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_phone }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Email
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_email }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Address 1
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.address_1 }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Landmark
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.landmark }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Address 2
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.address_2 }}
                            </td>
                        </tr>
                        <tr class="bg-white dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Location
                            </th>
                            <td class="px-6 py-2">
                                {{ origin_location.city }}, {{ origin_location.state }}, {{ origin_location.country }}
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
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Name
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_name }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Phone
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_phone }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Contact Email
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_email }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Address 1
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.address_1 }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Landmark
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.landmark }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Address 2
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.address_2 }}
                            </td>
                        </tr>
                        <tr class="bg-white dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Location
                            </th>
                            <td class="px-6 py-2">
                                {{ destination_location.city }}, {{ destination_location.state }}, {{ destination_location.country }}
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
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Item Category
                            </th>
                            <td class="px-6 py-2">
                                {{ item_category.name }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Value
                            </th>
                            <td class="px-6 py-2">
                                {{ item.value }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Description
                            </th>
                            <td class="px-6 py-2">
                                {{ item.description }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Quantity
                            </th>
                            <td class="px-6 py-2">
                                {{ item.quantity }} Nos.
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Weight
                            </th>
                            <td class="px-6 py-2">
                                {{ item.weight }}cm
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Height
                            </th>
                            <td class="px-6 py-2">
                                {{ item.height }}cm
                            </td>
                        </tr>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Length
                            </th>
                            <td class="px-6 py-2">
                                {{ item.length }}cm
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <form @submit.prevent="pay" class="card p-5 w-full bg-white">
                <div>
                    <h1 class="font-bold text-xl">Summary</h1>


                    <div class="card mt-5 border">
                        <div class="p-4 font-bold flex flex-row justify-between">
                            <div>Payment Summary</div>
                        </div>
                        <hr>
                        <div class="p-4">
                            <p>Amount: {{ shipping_rate_log.amount_before_tax }}</p>
                            <p>Tax: {{ shipping_rate_log.tax }}</p>
                            <p>Insurance: {{ insurance_options[0].amount }}</p>
                        </div>
                        <hr>
                        <div class="p-4">
                            <div class="font-bold">Shipment Amount:  {{ shipping_rate_log.total_amount }}</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
