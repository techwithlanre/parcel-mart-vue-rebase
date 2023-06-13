<script setup>
import {computed, ref} from 'vue';
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    title: String
});

let show = ref(false);
const isOpen = () => (show.value = !show.value);
const page = usePage();
const fullName = computed(() => {
    return page.props.auth.user.first_name + ' ' + page.props.auth.user.last_name;
})

</script>

<template>
    <div class="p-5">
        <div class="flex items-center justify-between bg-white py-3 px-10 rounded-2xl border">
            <div class="flex mt-2">
                <h1 class="text-lg font-semibold leading-relaxed text-gray-800">{{ title }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex flex-col">
                    <div v-show="show" class="absolute py-2 mt-10 bg-white flex flex-col gap-2 text-gray-600 rounded-md shadow-xl  w-44" >
                        <div class="px-5">Profile</div>
                        <hr>
                        <div class="px-5">Logout</div>
                    </div>
                </div>
                <button @click="show = !show">
                    {{ fullName }}
                </button>
            </div>
        </div>
    </div>
</template>
