<script setup>
import {Link} from "@inertiajs/vue3";

defineProps({
    tabs: Array
});

</script>
<template>
    <div>
        <ul class="flex gap-x-24 items-center px-4 border rounded-lg bg-[#FBFBFB] shadow-xs mb-10">
            <li v-for="tab in tabs">
                <Link :href="tab.route">
                    <button
                        class="flex gap-x-2 items-center py-5 px-6 text-gray-400 hover:text-primary relative group">
                        <Component :is="tab.icon" class="w-5 h-5 fill-current" />
                        <span class="text-sm"> {{ tab.name }} </span>
                        <span class="absolute w-full h-0.5 left-3 bg-primary rounded bottom-0 scale-x-0 group-hover:scale-x-100 transition-transform ease-in-out" />
                    </button>
                </Link>
            </li>
        </ul>
    </div>
</template>

<style scoped>

</style>
