<script setup>
import { ref } from 'vue'
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import HomeLayout from "@/Layouts/HomeLayout.vue";

const activeKey = ref('1');


const handleButtonClick = e => {
    //console.log('click left button', e);
};
const handleMenuClick = e => {
    //console.log('click', e);
};

</script>

<template>
    <HomeLayout :show-footer="false">
        <div class="relative isolate px-6 h-screen mt-36">
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-primary to-primary-dark opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
            </div>
            <div class="flex flex-row items-center justify-center">
                <div class="w-full flex flex-col text-center">
                    <h3 class="font-bold text-4xl">Track Your Package</h3>
                    <p>Enter your tracking number in the form below to start tracking your package</p>
                    <div class="mt-10">
                        <TextInput
                            id="email"
                            type="email"
                            class="max-w-5xl w-full"
                            required
                            autofocus
                            autocomplete="off"
                            placeholder="Enter tracking number"
                            model-value=""/>
                    </div>
                    <div class="h-4 mt-5">
                        <PrimaryButton class="text-center max-w-5xl">
                            Track
                        </PrimaryButton>
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-primary-dark opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
            </div>
        </div>
    </HomeLayout>
</template>

<style scoped>
img {
    transform: translate3d(0vw, 0vh, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg);
    transform-style: preserve-3d;
}
</style>
