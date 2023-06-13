<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    inputModelValue: {
        type: String,
        required: true,
    },

    selectModelValue: {
        type: String,
        required: true,
    },

    selectOptions: {
        type: Object
    },

    inputPlaceHolder: {
        type: String,
    },

    selectPlaceHolder: {
        type: String,
        default: "Select"
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>


<template>
    <div>
        <div class="relative mt-2 rounded-md shadow-sm">
            <input
                type="text"
                id="price"
                :value="modelValue"
                :placeholder="placeholder"
                @input="$emit('update:modelValue', $event.target.value)"
                ref="input"
                class="block w-full rounded-md border-0 py-1.5 pl-7 pr-20 text-gray-900 ring-1 ring-inset ring-gray-300
                placeholder:text-gray-400 focus:ring-inset sm:text-sm sm:leading-6 bg-transparent border-gray-200  placeholder-gray-300 outline-none focus:border-dashed focus:border-primary focus:ring-1 focus:ring-primary focus:ring-offset-2" placeholder="0.00" />
            <div class="absolute inset-y-0 right-0 flex items-center">
                <label for="currency" class="sr-only">Currency</label>
                <select id="currency" name="currency" class="h-full rounded-md border-0 bg-transparent py-0 pl-2 pr-7 text-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                    <option disabled value="" selected>{{ selectPlaceHolder }}</option>
                    <option :value="item.id" v-for="item in selectOptions">{{ item.name }}</option>
                </select>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
