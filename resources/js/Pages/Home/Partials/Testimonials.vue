<script setup>
import {Swiper, SwiperSlide, useSwiper} from 'swiper/vue';
import 'swiper/css';

const swiperController = useSwiper();

const onSwiper = (swiper) => {
    //console.log(swiper);
};
const onSlideChange = () => {
    //console.log('slide change');
};

const testimonials = [
    {
        stars: 5,
        title: "Trusted Logistics Partner",
        body: "Parcels Mart has been our trusted logistics partner for several years now, and we couldn't be happier with their services. Their key strength lies in their personalized customer service. No matter the size of our shipment or the complexity of our logistics requirements, Parcels Mart always goes above and beyond to ensure a smooth and efficient process. Their team of experts is knowledgeable, responsive, and dedicated to meeting our unique needs. With Parcels Mart, we have experienced significant cost savings and transparent pricing, making them an invaluable asset to our business.",
        cite: "John",
        citeTitle: "CEO, Techmate"
    },
    {
        stars: 4,
        title: "Timely & Reliable",
        body: "As a retailer, timely and reliable delivery is crucial to our business success. Parcels Mart has consistently delivered on their promise of express logistics and last-mile delivery. Their extensive network of delivery agents ensures that our parcels reach our customers promptly and efficiently, even in remote locations. Their cutting-edge technology and AI-driven approach have enabled us to track our shipments in real-time, providing us and our customers with peace of mind. Parcels Mart's commitment to sustainability and eco-friendly packaging aligns perfectly with our values, making them our preferred logistics partner.",
        cite: "Sarah Holmes",
        citeTitle: "Professional Personal Shopper"
    },
    {
        stars: 4,
        title: "Exceptional & Dedicated",
        body: "We have been working with Parcels Mart for our international shipping needs, and they have been exceptional. Their expertise in customs clearing and their knowledge of complex regulations have made the shipping process seamless for us. With Parcels Mart, we no longer worry about customs-related delays or compliance issues. Their air freight and sea freight services have always been reliable and competitively priced. Parcels Mart's dedication to sustainability and their use of renewable energy sources further solidifies our trust in their commitment to responsible logistics practices.",
        cite: "David Ekene",
        citeTitle: "Operations Manager, Merline Oil"
    },
    {
        stars: 5,
        title: "Trusted Logistics Partner",
        body: "Parcels Mart has been instrumental in helping us streamline our e-commerce logistics. Their integrated solutions and seamless integration with our online platforms have significantly improved our order fulfillment process. Their warehousing services have allowed us to efficiently manage our inventory, while their e-commerce logistics expertise ensures that our customers receive their orders on time. The personalized touch they provide sets them apart from other logistics companies we have worked with in the past. We highly recommend Parcels Mart to any e-commerce business looking for a reliable and customer-centric logistics partner.",
        cite: "Josephine Emmanuel",
        citeTitle: "Acres of Gem"
    },
];

</script>

<template>
    <div class="flex flex-col p-6 px-[50px] xl:px-[150px]">
        <section class="bg-background/70 rounded-2xl">
            <div class="mx-auto max-w-[1340px] px-4 py-16 sm:px-6 sm:py-24 lg:me-0 lg:pe-0 lg:ps-8">
                <div class="grid grid-cols-1 gap-y-8 lg:grid-cols-3 lg:items-center lg:gap-x-16">
                    <div class="max-w-xl ltr:sm:text-left rtl:sm:text-right">
                        <h2 class="xl:text-4xl text-2xl font-bold tracking-tight sm:text-4xl">
                            Don't just take our word for it...
                            <br class="hidden sm:block mt-2" />
                            Read reviews from our customers
                        </h2>

                        <p class="mt-4 text-gray-500">
                            More than hundreds of businesses and individuals rely on Parcels Mart to handle their logistics
                        </p>

<!--                        <div class="hidden lg:mt-8 lg:flex lg:gap-4">
                            <button @click="swiperController.slidePrev()" class="prev-button rounded-full border hover:border-background border-primary p-3 text-primary/80 hover:bg-background hover:text-primary transition-all">
                                <span class="sr-only">Previous Slide</span>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    strokeWidth="1.5"
                                    stroke="currentColor"
                                    class="h-5 w-5 rtl:rotate-180"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M15.75 19.5L8.25 12l7.5-7.5"
                                    />
                                </svg>
                            </button>

                            <button
                                class="next-button rounded-full border border-primary hover:border-background duration-500 p-3 text-primary/80 hover:bg-background hover:text-primary transition-all"
                                @click="swiperController.slideNext()">
                                <span class="sr-only">Next Slide</span>
                                <svg
                                    class="h-5 w-5 rtl:rotate-180"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M9 5l7 7-7 7"
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                    />
                                </svg>
                            </button>
                        </div>-->
                    </div>

                    <div class="-mx-6 lg:col-span-2 lg:mx-0">
                        <div class="swiper-container !overflow-hidden">
                            <swiper
                                :slides-per-view="1"
                                :space-between="10"
                                @swiper="onSwiper"
                                @slideChange="onSlideChange"
                            >
                                <swiper-slide class="swiper-slide" v-for="item in testimonials">
                                    <blockquote
                                        class="flex h-full flex-col justify-between bg-white p-12"
                                    >
                                        <div>
                                            <div class="flex gap-0.5 text-green-500">
                                                <svg class="h-5 w-5"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg" v-for="star in item.stars">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                                    />
                                                </svg>
                                            </div>

                                            <div class="mt-4">
                                                <p class="text-xl font-bold text-primary sm:text-3xl">
                                                    {{ item.title }}
                                                </p>

                                                <p class="mt-4 leading-relaxed text-gray-500">
                                                    {{ item.body }}
                                                </p>
                                            </div>
                                        </div>

                                        <footer class="mt-8 text-sm text-gray-500">
                                            &mdash; {{ item.cite }}
                                            <p class="text-xs italic">{{ item.citeTitle }}</p>
                                        </footer>
                                    </blockquote>
                                </swiper-slide>
                            </swiper>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>

</style>
