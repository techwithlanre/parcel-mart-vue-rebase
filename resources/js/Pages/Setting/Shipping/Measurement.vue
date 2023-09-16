<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import {toast} from "vue3-toastify";
import TopNav from "@/Components/TopNav.vue";

import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const tabs = [
    { name: "Description", icon: WalletIcon, route: "description" },
    { name: "Measurement", icon: DocumentIcon, route: "measurement" }
];

const count = ref(5);
const visible = ref(false);
const confirmLoading = ref(false);

const showModal = () => {
    visible.value = true;
};

const handleOk = () => {
    confirmLoading.value = true;
    setTimeout(() => {
        submit();
        visible.value = false;
        confirmLoading.value = false;

    }, 2000);
};

const form = useForm({
    description: ''
});

defineProps({
    descriptions: Array
});

const submit = () => {
    form.post(route('shipping.setting.description.post'), {
        onFinish: () => toast.success('Description Added'),
    })
}

const options = [
    {
        "id": 1,
        "name": "km/cm"
    },
    {
        "id": 2,
        "name": "lbs/in"
    },
];

</script>

<template>
    <Head title="Shipping Setting::Description" />
    <AuthenticatedLayout>
        <TopNav :tabs="tabs"/>
        <div>
            <div class="mt-10 border rounded-3xl bg-white p-10">
                <h3 class="font-bold">Unit of Measurement</h3>
                <div class="flex flex-col lg:flex-row gap-x-10 mt-5">
                    <div>
                        <SelectInput model-value="" :options="options" class="max-w-2xl" place-holder="Select metric" />
                    </div>
                    <div class="lg:mt-1 sm:mt-5">
                        <TextInput
                            id="email"
                            type="email"
                            class="block w-full"
                            placeholder="Default weight per parcel"
                            required
                            autofocus
                            autocomplete="off"
                        />
                    </div>
                    <div class="lg:mt-1 sm:mt-5">
                        <PrimaryButton :disabled="form.processing">
                            Save
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<style scoped>

</style>
