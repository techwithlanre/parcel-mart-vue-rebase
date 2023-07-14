<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link, useForm, usePage} from "@inertiajs/vue3";
import {ref, computed} from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";

defineProps({
    data: Array,
    countries: Array
});


const page = usePage();
const visible = ref(false);
const confirmLoading = ref(false);
//const filteredOptions = computed(() => page.props.countries.filter(selected => !editForm.destinations.includes(selected)));
const filteredOptions = computed(() => page.props.countries.filter(selected => !editForm.destinations.includes(selected)));
//console.log(page.props.countries.filter(selected => !editForm.destinations.includes(selected)));
const showModal = (item) => {
    editForm.origin = item.origin;
    editForm.destinations = item.destination_countries.map(item => ({ value: item.id }));
    visible.value = true;
};



const handleOk = () => {
    confirmLoading.value = true;
    setTimeout(() => {
        visible.value = false;
        confirmLoading.value = false;
    }, 2000);
};
</script>

<template>
<AuthenticatedLayout>
    <Head title="Shipment Locations"/>
    <div class="p-5 card border">
        <div>
            <div class="mt-20">
                <h1 class="text-lg">Shipment Locations</h1>
                <div class="relative overflow-x-auto shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Origin Country</th>
                            <th scope="col" class="px-6 py-3">Destination Countries</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in data" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                {{ item.origin.country }}
                            </td>
                            <td class="px-6 py-4 flex gap-x-2">
                                <div v-for="i in item.destination_countries" class="bg-primary text-xs text-white px-4 py-1 rounded-full">{{ i.country }} </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="#" @click="showModal(item)" class="text-primary font-medium hover:text-green-600">Edit</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <a-modal
            v-model:visible="visible"
            title="Edit Shipment Location"
            :confirm-loading="confirmLoading"
            @ok="handleOk">
            <div>
                <div>
                    <InputLabel value="Origin" />
                    <TextInput v-model="editForm.origin.country" type="text" class="mt-2" readonly disabled="" />
                </div>
                <div class="mt-4">
                    <InputLabel value="Allowed Destinations" />
                    <select multiple
                        class="bg-transparent border-gray-300 rounded-md w-full outline-none focus:border-primary focus:ring-2 focus:ring-background"
                        ref="select">
                        <option value="0" disabled selected>{{ placeHolder ?? 'Select'}}</option>
                        <option :value="item.id" v-for="item in countries">{{ item.name }}</option>
                    </select>
                    {{ editForm.destinations }}
<!--                    <a-select
                        v-model:value="editForm.destinations"
                        mode="multiple"
                        placeholder="Select countries"
                        style="width: 100%">
                        <a-select-option v-for="item in countries" :value="item.id">{{ item.name }}</a-select-option>
                    </a-select>-->
                </div>
            </div>
        </a-modal>
    </div>
</AuthenticatedLayout>
</template>

<style scoped>

</style>
