<template>
    <div class="bg-white overflow-x-hidden">
        <Head title="Home" />
        <div class="bg-white overflow-x-hidden">
            <div class="p-6 px-[50px] sm:px-[50px]">
                <FrontNavigation id="#top" />
                <Hero />
                <Partners />
            </div>
        </div>
        <QuoteForm :countries="countries" id="quote-form" />
        <Services />
        <Why />
        <Testimonials  />
        <Faq />
        <Certifications />
        <Footer />
    </div>
</template>

<script setup>
import FrontNavigation from "@/Components/FrontNavigation/FrontNavigation.vue";
import Hero from "@/Components/Front/Hero/Hero.vue";
import {Head, Link} from "@inertiajs/vue3";
import Partners from "@/Pages/Home/Partials/Partners.vue";
import Why from "@/Pages/Home/Partials/Why.vue";
import Testimonials from "@/Pages/Home/Partials/Testimonials.vue";
import Faq from "@/Pages/Home/Partials/Faq.vue";
import Certifications from "@/Pages/Home/Partials/Certifications.vue";
import Services from "@/Pages/Home/Partials/Services.vue";
import QuoteForm from "@/Pages/Home/Partials/QuoteForm.vue";
import Footer from "@/Pages/Home/Partials/Footer.vue";

defineProps({
    countries: Array
})

</script>
