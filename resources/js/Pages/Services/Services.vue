<script setup>

import HomeLayout from "@/Layouts/HomeLayout.vue";
import plane from "../../../images/air.png";
import aircraft from "../../../images/aircraft.png";
import box from "../../../images/box.png";
import delivery from "../../../images/delivery.png";
import arrowRightUp from "../../../images/icons/arrow-right-up.svg";
import ship from "../../../images/icons/ship.svg";
import truck from "../../../images/icons/truck.svg";
import warehouse from "../../../images/warehouse.png";
import cargo from "../../../images/cargo.png";
import smartSend from "../../../images/icons/smart-send.svg";


const services = [
    {
        "icon": "https://cdni.iconscout.com/illustration/premium/thumb/shipping-service-3494273-2963284.png",
        "title": "Express Logistics",
        "description":"Our express logistics service ensures fast and reliable delivery of your parcels, with options for door-to-door, same-day, and next-day delivery. We understand the importance of time-sensitive shipments and provide tailored solutions to meet your urgent delivery requirements.",
    },
    {
        "icon": delivery,
        "title": "Last Mile Delivery",
        "description":"Our extensive network of delivery agents enables us to provide efficient last mile delivery services. No matter where your parcels need to reach, we have the capabilities to ensure they reach their final destination promptly and securely.",
    },
    {
        "icon": "https://static.vecteezy.com/system/resources/previews/021/192/952/original/cardboard-box-with-checklist-check-mark-document-warehouse-parcel-shipping-tracking-delivery-shipping-icon-sign-or-symbol-3d-background-illustration-png.png",
        "title": "Customs Clearing",
        "description":"Navigating complex customs regulations can be a daunting task. Parcels Mart offers expert customs clearing services to simplify the process for you. Our experienced team will handle all documentation, ensuring compliance with customs requirements and smooth passage of your shipments.",
    },
    {
        "icon": plane,
        "title": "Air Freight",
        "description":"Whether it's domestic or international air freight, we provide efficient and reliable services to meet your shipping needs. With partnerships with major airlines, we guarantee timely delivery and competitive rates for your valuable cargo.",
    },
    {
        "icon": cargo,
        "title": "Large Cargos",
        "description":"We specialize in handling heavy and bulky shipments. Our experienced team will manage every aspect of the shipping process, including transportation, documentation, and delivery to the final destination. Trust us to handle your large cargos with utmost care and efficiency.",
    },
    {
        "icon": box,
        "title": "Dangerous Goods Handling",
        "description":"Shipping dangerous goods requires expertise and compliance with strict safety regulations. Parcels Mart has a dedicated team of highly trained professionals who will ensure the safe transportation of your hazardous materials while adhering to all safety guidelines.",
    },
    {
        "icon": "https://media.istockphoto.com/id/1181050865/vector/shipping-isometric-concept-laptop-with-truck.jpg?s=612x612&w=0&k=20&c=dT9yTx11lFVMEOsOD3E2nfsxs-jqhnfk98DgfCAtFaU=",
        "title": "Ecommerce Logistics",
        "description":"We understand the unique logistics challenges faced by e-commerce businesses. Our customized logistics solutions cater to the specific needs of online retailers, including inventory management, order fulfillment, and seamless integration with major e-commerce platforms.",
    },
    {
        "icon": "data:image/jpeg;base64,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",
        "title": "Sea Freight",
        "description":"Our sea freight services offer cost-effective and reliable solutions for heavy and large volume cargo. With strategic partnerships with major shipping lines, we ensure timely and efficient delivery of your goods while keeping costs optimized.",
    },
    {
        "icon": "https://static.vecteezy.com/system/resources/previews/011/232/444/non_2x/people-deliver-export-and-import-shipping-of-goods-with-transport-technologies-in-supply-chain-management-illustrations-vector.jpg",
        "title": "Trans-border Logistics",
        "description":"Parcels Mart specializes in cross-border logistics, offering end-to-end solutions that include customs clearance, documentation, and transportation. We have the expertise and resources to handle your international shipments with efficiency and reliability.",
    },
    {
        "icon": aircraft,
        "title": "Aircraft Charters",
        "description":"When time is of the essence, our aircraft charter services provide the fastest and most efficient solution. We work closely with you to arrange the best charter option for your urgent shipments, ensuring they reach their destination with utmost speed and security.",
    },
    {
        "icon": warehouse,
        "title": "Warehousing",
        "description":"We offer specialized warehousing solutions in multiple locations worldwide. Our state-of-the-art facilities are equipped with advanced inventory management systems, ensuring the safety and proper storage of your goods.",
    },
    {
        "icon": "https://img.freepik.com/free-vector/flat-design-container-ship_23-2149147988.jpg",
        "title": "Shipping Consulting",
        "description":"Parcels Mart understands the complexities of shipping and supply chain management. Our experienced shipping consultants provide tailored solutions to individuals, startups, and multinational companies. We offer expert guidance and advice to optimize your logistics operations.",
    },
];

</script>

<template>
    <HomeLayout title="Services" :show-footer="true">
        <div class="bg-opacity-40">
            <div class="lg:px-[250px] px-10 py-20">
                <div class="max-w-3xl">
                    <h1 class="text-3xl lg:text-5xl font-bold">Our Services</h1>
                    <p class="text-xl">Parcels Mart is a global logistics company committed to delivering excellence in every aspect of its operations. With a focus on technology-driven solutions, personalized customer service, and a dedication to sustainability, Parcels Mart is your trusted partner for all your logistics needs.</p>
                </div>
                <div class="grid xl:grid-cols-3 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 justify-between gap-x-20 gap-y-10 mt-20">
                    <div v-for="item in services" class="flex flex-col p-10 w-full justify-between gap-y-10 bg-white rounded-xl ring ring-background hover:ring-primary duration-500">
                        <img :src="item.icon" alt="" class="flex justify-center w-36 text-primary" />
                        <div class="flex justify-between">
                            <div>
                                <h3 class="font-bold text-2xl">{{ item.title }}</h3>
                                <p class="text-lg">{{ item.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>

<style scoped>

</style>
