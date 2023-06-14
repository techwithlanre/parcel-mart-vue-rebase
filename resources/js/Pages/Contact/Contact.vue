<script setup>

import HomeLayout from "@/Layouts/HomeLayout.vue";
import plane from "../../../images/icons/plane.svg";
import arrowRightUp from "../../../images/icons/arrow-right-up.svg";
import ship from "../../../images/icons/ship.svg";
import truck from "../../../images/icons/truck.svg";
import warehouse from "../../../images/icons/warehouse.svg";
import smartSend from "../../../images/icons/smart-send.svg";
import QuoteTabs from "@/Pages/Home/Partials/QuoteTabs.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import {useForm} from "@inertiajs/vue3";



const form = useForm({
    email:"",
    message: "",
    name: ""
});

const submit = () => {
    form.post((route('contact.send')), {
        onFinish: () => form.reset('name', 'email', 'message'),
    })
}
</script>

<template>
    <HomeLayout title="Services" :show-footer="true">
        <div class="bg-opacity-40">
            <div class="flex lg:flex-row flex-col gap-x-36 lg:px-[250px] px-10 py-20">
                <div class="w-full">
                    <h1 class="text-3xl lg:text-5xl font-bold">Contact Us</h1>
                    <p class="text-xl"></p>
                    <div class="mt-20 flex lg:flex-row flex-col gap-x-36">
                        <div class="lg:w-1/2">
                            <form class="w-full" @submit.prevent="submit">
                                <div class="">
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 w-full"
                                        required
                                        autocomplete="off"
                                        placeholder="Enter full name"
                                        v-model="form.name"/>
                                </div>
                                <div class="mt-5">
                                    <TextInput
                                        id="email"
                                        type="email"
                                        class="mt-1 w-full"
                                        required
                                        autocomplete="off"
                                        placeholder="Enter email"
                                        v-model="form.email"/>
                                </div>
                                <div class="mt-5">
                                    <TextAreaInput
                                        rows="10"
                                        id=""
                                        type="number"
                                        class="mt-1 w-full"
                                        required
                                        autocomplete="off"
                                        placeholder="Enter message"
                                        v-model="form.message"/>
                                </div>
                                <div class="flex flex-col items-end justify-end mt-10">
                                    <PrimaryButton class="text-center px-12 " :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Send
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                        <div>
                            <p class="">
                                <strong class="text-xl">LAGOS</strong>
                                <div class="flex gap-x-5 mt-3">
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                      <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                    <span class="text-lg">
                                    NAHCO Office Complex, International cargo airport, Murtala Mohammed airport, Lagos
                                </span>
                                </div>
                            </p>
                            <p class="mt-8">
                                <strong class="text-xl">PORT HARCOURT</strong>
                                <div class="flex gap-x-5 mt-3">
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                      <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                    <span class="text-lg">
                                    NAHCO Office Complex, International cargo airport, Murtala Mohammed airport, Lagos
                                </span>
                                </div>
                            </p>
                            <p class="mt-8">
                                <strong class="text-xl">UNITED KINGDOM</strong><br>
                                <div class="flex gap-x-5 mt-3">
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                      <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                    <span class="text-lg">
                                    16 John Roberts Garden, Carlisle, Cumbria.
                                </span>
                                </div>
                            </p>
                            <p class="mt-8">
                                <strong class="text-xl">Email</strong>
                                <div class="flex gap-x-5 mt-3">
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                      <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                    <span class="text-lg">
                                    enquiries@parcelsmartsolutions.com <br> customercare@parcelsmartsolutions.com
                                </span>
                                </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>

<style scoped>

</style>
