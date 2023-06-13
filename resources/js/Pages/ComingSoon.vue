<script setup>
import { ref } from 'vue'
import ParcelForm from "@/Components/Front/ParcelForm/ParcelForm.vue";
import DocumentForm from "@/Components/Front/DocumentForm/DocumentForm.vue";
import {Head, Link} from "@inertiajs/vue3";
import apple from "/resources/images/icons/apple.svg";
import googlePlay from "/resources/images/icons/google-play.svg";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import phoneMockup from '/resources/images/phone-mockup-2.png';
import map from '/resources/images/map-2.png';

const activeKey = ref('1');


const handleButtonClick = e => {
    console.log('click left button', e);
};
const handleMenuClick = e => {
    console.log('click', e);
};

</script>

<template>
    <div class="relative isolate px-6 lg:px-0 bg-[]">
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-primary to-primary-dark opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
        </div>
        <div class="flex flex-col pt-16 justify-center items-center gap-x-32 text-center mt-36 mx-auto">
            <div class="lg:w-1/2">
                <h1 class="lg:text-6xl text-5xl font-bold">Coming Soon</h1>
                <p class=" lg:text-2xl  text-gray-600">We providing fast, reliable, and affordable delivery services. Whether you need to send a package across town or across the country, we've got you covered.</p>
                <Link :href="route('home')"><PrimaryButton class="flex justify-center items-center w-max mt-14 mx-auto">Back to Home</PrimaryButton></Link>
            </div>
        </div>
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-primary-dark opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
        </div>
    </div>
</template>

<style scoped>
img {
    transform: translate3d(0vw, 0vh, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg);
    transform-style: preserve-3d;
}
</style>
