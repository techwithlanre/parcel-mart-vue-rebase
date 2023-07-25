<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm, usePage} from '@inertiajs/vue3';
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {toast} from "vue3-toastify";

const page = usePage()
defineProps({
    shipment: Array,
    origin: Object,
    destination: Object,
    insurance_options: Array,
    shipping_rate_log: Array,
    origin_location: Array,
    destination_location: Array,
});

const form  = useForm({
    insurance: 0,
    option: '',
    option_id: '',
    shipment_id: page.props.shipment.id
});

const pay = () => {
    //do all validation here
    if (form.option_id === '') {
        return toast.error('Select a shipment option');
    }

    form.post(route('shipment.book'), {

    });
}

const setOption = () => {
    form.option_id = form.option.id;
    console.log(form.option_id);
}

</script>

<template>
    <AuthenticatedLayout page-title="Shipment Checkout">
        <Head title="Shipment Checkout"/>
        <div class="flex lg:flex-row flex-col justify-between gap-10">
            <div class="w-full card bg-white p-5">
                <h1 class="font-bold text-xl">Contact Information</h1>
                <h3>Sender Information</h3>
                <div class="relative overflow-x-auto border rounded-2xl mt-5">
                    <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
                        <tbody>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Contact Name
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_name }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Contact Phone
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_phone }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Contact Email
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_email }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Address 1
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.address_1 }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Landmark
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.landmark }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Address 2
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.address_2 }}
                            </td>
                        </tr>
                        <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
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
                    <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
                        <tbody>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Contact Name
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_name }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Contact Phone
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_phone }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Contact Email
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.contact_email }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Address 1
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.address_1 }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Landmark
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.landmark }}
                            </td>
                        </tr>
                        <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                Address 2
                            </th>
                            <td class="px-6 py-2">
                                {{ origin.address_2 }}
                            </td>
                        </tr>
                        <tr class="bg-white sdark:bg-gray-800 sdark:border-gray-700">
                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
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
                    <table class="w-full text-sm text-left text-gray-500 sdark:text-gray-400">
                        <tbody v-for="item in shipment.shipment_items" :key="item.id" >
                            <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                    Item Category
                                </th>
                                <td class="px-6 py-2">
                                    {{ origin.contact_name }}

                                </td>
                            </tr>
                            <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                    Value
                                </th>
                                <td class="px-6 py-2">
                                    {{ item.value }}
                                </td>
                            </tr>
                            <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                    Description
                                </th>
                                <td class="px-6 py-2">
                                    {{ item.description }}
                                </td>
                            </tr>
                            <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                    Quantity
                                </th>
                                <td class="px-6 py-2">
                                    {{ item.quantity }} Nos.
                                </td>
                            </tr>
                            <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                    Weight
                                </th>
                                <td class="px-6 py-2">
                                    {{ item.weight }}cm
                                </td>
                            </tr>
                            <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
                                    Height
                                </th>
                                <td class="px-6 py-2">
                                    {{ item.height }}cm
                                </td>
                            </tr>
                            <tr class="bg-white border-b sdark:bg-gray-800 sdark:border-gray-700">
                                <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap sdark:text-white">
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
                    <h1 class="font-bold text-xl">Shipment Options</h1>
                    <div v-for="item in shipping_rate_log" class="card mt-5 border">
                        <label class="cursor-pointer">
                            <div class="p-4 font-bold flex flex-row justify-between">
                                <div>{{ item.product_name }}</div>
                                <input type="radio" :value="item" v-model="form.option" v-on:change="setOption"/>
                            </div>
                            <hr>
                            <div class="p-4">
                                <p>Amount: {{ item.amount_before_tax }}</p>
                                <p>Tax: {{ item.tax }}</p>
                            </div>
                            <hr>
                            <div class="p-4">
                                <div class="font-bold">Shipment Amount:  {{ item.total_amount }}</div>
                            </div>
                        </label>
                    </div>

                    <div class="mt-10">
                        <h3>Choose an Insurance Option</h3>
                        <SelectInput v-model="form.insurance" :options="insurance_options" />
                        <div class="mt-5 p-5 card border duration-300 transition-all flex flex-row justify-between items-center" v-if="form.insurance.length > 0">
                            <div>
                                <div class="text-lg font-bold">{{ insurance_options[form.insurance - 1].name }}</div>
                                <div>Covers damages upto {{ insurance_options[form.insurance - 1].cover }}</div>
                            </div>
                            <div class="text-green-600 bg-background px-4 py-1 rounded-full">{{ insurance_options[form.insurance - 1].amount }}</div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h3 class=" text-lg">Payment breakdown</h3>
                        <p>Shipping Amount:
                            <span  v-if="form.option">{{ form.option.total_amount }}</span>
                            <span  v-else>0.00</span>
                        </p>
                        <p>Insurance: <span v-if="form.insurance.length > 0">{{ insurance_options[form.insurance - 1].amount }}</span><span v-else>0.00</span> </p>
                    </div>
                    <input type="hidden" v-model="form.option_id">

                    <div class="mt-10">
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Pay
<!--                            <span v-if="form.option">
                                NGN {{ parseFloat(insurance_options[form.insurance - 1].amount) + parseInt() }}
                            </span>
                            <span v-else>0.00</span>-->
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
