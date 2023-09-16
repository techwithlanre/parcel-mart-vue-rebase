<script setup>

defineProps({
    show: {
        default: true
    }
})

</script>

<template>
    <div class="flex flex-col gap-x-20 py-12 lg:px-[250px] bg-primary-dark-dark" v-if="show">
<!--        <div class="flex flex-col lg:flex-row justify-between">
            <ul class="flex flex-col gap-y-5 justify-center lg:p-0 px-10">
                <li><Link class="text-white hover:text-primary">About Us </Link></li>
                <li><Link class="text-white hover:text-primary">Terms & Conditions</Link></li>
                <li><Link class="text-white hover:text-primary">Cookie Policy</Link></li>
                <li><Link class="text-white hover:text-primary">Terms of Trade</Link></li>
                <li><Link class="text-white hover:text-primary">Contact Us</Link></li>
            </ul>

            <ul class="flex flex-col gap-y-5 justify-center lg:p-0 px-10">
                <li><Link class="text-white hover:text-primary">Claims Information</Link></li>
                <li><Link class="text-white hover:text-primary">Prohibited Items</Link></li>
                <li><Link class="text-white hover:text-primary">Compensation Exclusions</Link></li>
                <li><Link class="text-white hover:text-primary">Labelling & Packaging Criteria</Link></li>
            </ul>

            <ul class="flex flex-col gap-y-5 justify-center lg:p-0 px-10">
                <li><Link class="text-white hover:text-primary">Claims Information</Link></li>
                <li><Link class="text-white hover:text-primary">Prohibited Items</Link></li>
                <li><Link class="text-white hover:text-primary">Compensation Exclusions</Link></li>
                <li><Link class="text-white hover:text-primary">Labelling & Packaging Criteria</Link></li>
            </ul>
            <div class="flex bg-primary-dark-dark rounded-xl h-16 gap-x-5 justify-between items-center px-5 lg:mx-0 mx-10 mt-5">
                <Component :is="mastercard" class="h-8 border px-3 py-1 rounded-lg bg-white" />
                <img :src="visa" class="h-8 px-3 py-2 rounded-lg bg-white"  alt=""/>
                <Component :is="mastercard" class="h-8 border px-3 py-1 rounded-lg" />
                <Component :is="mastercard" class="h-8 border px-3 py-1 rounded-lg" />
            </div>
        </div>-->
        <div class="flex justify-center mt-5">
            <p class="text-white font-bold">All rights reserved Parcels Mart. 2023</p>
        </div>
    </div>
</template>

<style scoped>

</style>
