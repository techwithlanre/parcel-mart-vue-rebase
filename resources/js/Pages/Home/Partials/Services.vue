<script setup>
import arrowRightUp from '/resources/images/icons/arrow-right-up.svg';
import plane from '/resources/images/icons/plane.svg';
import truck from '/resources/images/icons/truck.svg';
import ship from '/resources/images/icons/ship.svg';
import smartSend from '/resources/images/icons/smart-send.svg';
</script>

<template>
    <div class="bg-background bg-opacity-40 w-full">
        <div class="px-10 py-20">
            <h1 class="text-3xl lg:text-5xl font-bold">We Provide Safe and <br>Reliable Cargo Solutions</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 justify-between gap-x-20 gap-y-10 mt-20">
                <div class="p-5 w-full justify-between gap-y-10 bg-white rounded-xl ring ring-background hover:ring-primary duration-500">
                    <Component :is="plane" class="flex justify-center h-auto max-w-full" />
                    <div class="flex justify-between">
                        <h3 class="font-bold text-lg">Air <br>Freight</h3>
                        <div class="border-background border-2 xl:flex justify-center items-center rounded-full h-10 w-10 hidden">
                            <Component :is="arrowRightUp" class="text-primary" />
                        </div>
                    </div>
                </div>
                <div class="p-5 w-full justify-between gap-y-10 bg-white rounded-xl ring ring-background hover:ring-primary duration-500">
                    <Component :is="ship" class="flex justify-center h-auto max-w-full " />
                    <div class="flex justify-between">
                        <h3 class="font-bold text-lg">Ocean <br>Freight</h3>
                      <div class="border-background border-2 xl:flex justify-center items-center rounded-full h-10 w-10 hidden">
                            <Component :is="arrowRightUp" class="text-primary" />
                        </div>
                    </div>
                </div>

                <div class="p-5 w-full justify-between gap-y-10 bg-white rounded-xl ring ring-background hover:ring-primary duration-500">
                    <Component :is="truck" class="flex justify-center h-auto max-w-full" />
                    <div class="flex justify-between">
                        <h3 class="font-bold text-lg">Road <br> Transportation</h3>
                        <div class="border-background border-2 xl:flex justify-center items-center rounded-full h-10 w-10 hidden">
                            <Component :is="arrowRightUp" class="text-primary h-auto max-w-full" />
                        </div>
                    </div>
                </div>
                <div class="p-5 w-full justify-between gap-y-10 bg-white rounded-xl ring ring-background hover:ring-primary duration-500">
                    <Component :is="smartSend" class="flex justify-center h-auto max-w-full" />
                    <div class="flex justify-between">
                        <h3 class="font-bold text-lg">Smart <br>Parcel delivery</h3>
                        <div class="border-background border-2 xl:flex justify-center items-center rounded-full h-10 w-10 hidden">
                            <Component :is="arrowRightUp" class="text-primary" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
