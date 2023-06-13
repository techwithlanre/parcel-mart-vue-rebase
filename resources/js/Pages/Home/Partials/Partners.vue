<script setup>
import fedex from '/resources/images/fedex.png';
import ups from '/resources/images/ups.png';
import dhl from '/resources/images/dhl.png';
import dpd from '/resources/images/dpd.png';
import aramex from '/resources/images/aramex.png';
</script>

<template>
    <div class="my-10" v-animate-onscroll="'animated flash'">
        <div class="flex justify-between items-center overflow-x-auto">
            <span class="grayscale hover:grayscale-0 duration-500 flex-shrink-0 opacity-70 hover:opacity-100"><img :src="fedex" class="lg:h-36 h-16 w-auto" alt=""></span>
            <span class="grayscale hover:grayscale-0 duration-500 flex-shrink-0 opacity-70 hover:opacity-100"><img :src="ups" class="lg:h-16 h-6 w-auto" alt=""></span>
            <span class="grayscale hover:grayscale-0 duration-500 flex-shrink-0 opacity-70 hover:opacity-100"><img :src="dhl" class="lg:w-36 w-16" alt=""></span>
            <span class="grayscale hover:grayscale-0 duration-500 flex-shrink-0 opacity-70 hover:opacity-100"><img :src="dpd" class="lg:h-36 h-16 w-auto" alt=""></span>
            <span class="grayscale hover:grayscale-0 duration-500 flex-shrink-0 opacity-70 hover:opacity-100"><img :src="aramex" class="lg:w-40 w-16" alt=""></span>
        </div>
    </div>
</template>

<style scoped>

</style>
