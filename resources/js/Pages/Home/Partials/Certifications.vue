<script setup>

import iso1 from '/resources/images/iso1.jpg';
import iso2 from '/resources/images/iso2.jpg';
import iso3 from '/resources/images/iso3.jpg';
</script>

<template>
    <div class="flex justify-center lg:flex-row flex-col gap-x-20 py-16 px-5">
        <div class="flex flex-col lg:px-[250px] ">
            <h1 class="text-3xl lg:text-5xl font-bold text-center">We are Certified!</h1>
            <div class="flex justify-center">
                <img class="grayscale hover:grayscale-0 h-16 w-16 lg:h-28 lg:w-28 opacity-70 hover:opacity-100 duration-500" :src="iso1" alt="">
                <img class="grayscale hover:grayscale-0 h-16 w-16 lg:h-28 lg:w-32 opacity-70 hover:opacity-100 duration-500" :src="iso2" alt="">
                <img class="grayscale hover:grayscale-0 h-16 w-16 lg:h-28 lg:w-32 opacity-70 hover:opacity-100 duration-500" :src="iso3" alt="">
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
