<script >
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

export default {
    name: "GuestLayout",
    data() {
        return {

        }
    },

    props: {
        title: String,
        subtitle: String
    }
}
</script>

<template>
    <div class="
    min-h-screen
    flex flex-col
    sm:justify-center
    items-center
    pt-24
    sm:pt-0
    bg-gray-100
    overflow-x-hidden
">
        <div class="w-full ring-1 ring-gray-200 sm:max-w-xl mt-6 p-10 bg-white shadow-xs hover:drop-shadow-lg hover:drop-shadow-primary overflow-hidden rounded-3xl duration-500">
            <div class="flex flex-col gap-10">
                <Link href="/" class="mx-auto flex justify-center">
                    <img src="../../images/logo.png" alt="parcel-mart-logo" class="w-28 rounded-full">
                </Link>
                <div class="flex flex-col text-center mb-3">
                    <h1 class="text-2xl font-bold ">{{ title }}</h1>
                    <p class="text-lg text-gray-500">{{ subtitle }}</p>
                </div>
            </div>
            <slot />
        </div>
    </div>
</template>
