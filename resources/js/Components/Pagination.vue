<script setup>
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <div class="flex gap-x-2 justify-end py-10 overflow-x w-full" v-if="links.length > 0">
        <template v-for="(link, p) in links" :key="p" class="h-36">
            <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-1 text-sm leading-4 text-gray-400 border rounded duration-500" v-html="link.label" />
            <Link v-else class="mr-1 mb-1 px-4 py-1 text-sm leading-4 border rounded
              hover:bg-primary-dark hover:text-gray-200 focus:border-primary focus:text-white text-primary duration-500"
              :class="{ 'bg-primary text-white': link.active }" :href="link.url" v-html="link.label" />
        </template>
    </div>
</template>
<script>
export default {
    props: {
        links: Array
    },
}
</script>
