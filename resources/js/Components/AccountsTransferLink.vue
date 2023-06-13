<script setup>
import ChevronRightIcon from "/resources/images/icons/chevron-right.svg";



</script>


<template>
    <div class="bg-white w-full px-5 py-8 mb-10 rounded-xl flex justify-between items-center cursor-pointer">
        <div class="flex gap-5 items-center">
            <div class="w-10 h-10 p-1 rounded-lg bg-light-yellow flex justify-center items-center">
                <Component :is="ChevronRightIcon" class="w-6 h-6 fill-current text-white" />
            </div>
            <div class="font-black text-lightpurple">Transfer</div>
        </div>
        <div>
            <Component :is="ChevronRightIcon" class="w-6 h-6 fill-current" />
        </div>
    </div>
</template>
