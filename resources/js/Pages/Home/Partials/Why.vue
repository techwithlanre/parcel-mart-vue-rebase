<script setup>

import {Link} from "@inertiajs/vue3";
</script>

<template>
    <div class="flex lg:flex-row flex-col gap-y-10 gap-x-20 p-6 lg:px-[250px] py-16 mb-10">
        <div class="lg:w-1/3">
            <h1 class="text-3xl lg:text-5xl font-bold">Why Choose Parcels Mart?</h1>
            <p class="text-lg mb-10">If you're looking for cheap parcel deliveries but don't want to compromise on quality or reliability, look no further than Parcels Mart.</p>
            <Link :href="route('register.index')" class="px-5 py-3 hover:bg-white hover:text-primary bg-primary text-white border-2 border-primary rounded-xl">Get Started</Link>
        </div>
        <div class="lg:w-2/3 grid xl:grid-cols-2 grid-cols-1 gap-x-10 gap-y-10 mt-10">
            <div class="flex gap-x-10">
                <img class="h-14 p-2 bg-background rounded-full ring-2 ring-primary ring-opacity-30" src="../../../../images/delivery-man.png" alt="">
                <div class="flex flex-col">
                    <h3 class="font-bold text-xl lg:text-2xl">Online Support</h3>
                    <p class="font-medium text-sm lg:text-lg mt-5">Got a pricing or support query? Our Customer Service team are on hand and happy to help.</p>
                </div>
            </div>
            <div class="flex gap-x-10 items-start">
                <img class="h-14 p-2 bg-background rounded-full ring-2 ring-primary ring-opacity-30" src="../../../../images/delivery-man.png" alt="">
                <div class="flex flex-col">
                    <h3 class="font-bold text-xl lg:text-2xl">Easy to Use</h3>
                    <p class="font-medium text-sm lg:text-lg mt-5">Our easy-to-use system makes booking your parcel delivery a breeze. Get a free instant quote and select a courier service that suits you.</p>
                </div>
            </div>
            <div class="flex gap-x-10">
                <img class="h-14 p-2 bg-background rounded-full ring-2 ring-primary ring-opacity-30" src="../../../../images/delivery-man.png" alt="">
                <div class="flex flex-col">
                    <h3 class="font-bold text-xl lg:text-2xl">Parcel Tracking</h3>
                    <p class="font-medium text-sm lg:text-lg mt-5 pr-5">Follow your parcel’s progress from booking to delivery - all our services come with full parcel tracking as standard.</p>
                </div>
            </div>
            <div class="flex gap-x-10">
                <img class="h-14 p-2 bg-background rounded-full ring-2 ring-primary ring-opacity-30" src="../../../../images/delivery-man.png" alt="">
                <div class="flex flex-col">
                    <h3 class="font-bold text-xl lg:text-2xl">Parcel Protection</h3>
                    <p class="font-medium text-sm lg:text-lg mt-5">For total peace of mind, add on Parcel Protection and safeguard your delivery</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
