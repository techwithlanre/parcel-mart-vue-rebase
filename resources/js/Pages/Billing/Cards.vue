<script setup>
import {Head, useForm} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";


defineProps({
    cards: Object
});

const form = useForm({
    pan:"",
    expiry: "",
    cvv: ""
});
const submit = () => {
    form.post(route('billing.cards.post'), {
        onFinish: () => form.reset('pan'),
    });
};

</script>

<template>
    <Head title="Cards" />
<AuthenticatedLayout>
    <div class="flex lg:flex-row lg:gap-x-10 flex-col">
        <div class="lg:mt-17 mt-10 w-full lg:w-2/6 p-10 bg-white rounded-2xl border">
            <h1 class="text-lg font-bold mb-10">Add New Card</h1>
            <form @submit.prevent="submit">
                <div class="flex flex-col gap-y-5">
                    <div class="">
                        <InputLabel value="PAN *" class="font-normal"  />
                        <TextInput
                            type="text"
                            class="mt-1 block w-full"
                            required
                            v-model="form.pan"
                            v-cardformat:formatCardNumber />
                    </div>
                    <div class="form-group">
                        <InputLabel value="Expiry *" class="font-normal"  />
                        <TextInput
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.expiry"
                            required
                            v-cardformat:formatCardExpiry />
                    </div>
                    <div class="form-group">
                        <InputLabel value="CVV *" class="font-normal"  />
                        <TextInput
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.cvv"
                            required
                            v-cardformat:formatCardCVC />
                    </div>
                    <PrimaryButton class="w-full text-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        SAVE
                    </PrimaryButton>
                </div>
            </form>
        </div>
        <div class="lg:mt-17 mt-10 w-full lg:w-4/6 p-10 bg-white rounded-2xl border">
            <table class="w-full">
                <thead class="rounded-t-3xl">
                <tr class="text-sm font-medium text-gray-700 border-b border-gray-200 px-5">
                    <td class="font-bold p-7">PAN</td>
                    <td class="font-bold p-7">Expiry</td>
                    <td class="font-bold p-7"></td>
                </tr>
                </thead>
                <tbody>
                <tr v-if="cards.data.length > 0" v-for="item in cards.data" class="hover:bg-gray-100 transition-colors group border-b px-5">
                    <td class="text-sm p-7">{{ item.pan }}</td>
                    <td class="text-sm p-7">{{ item.expiry }}</td>
                    <td class=""></td>
                </tr>
                <tr v-else class="hover:bg-gray-100 transition-colors group border-b px-5">
                    <td class="text-sm p-7 text-center text-gray-400" colspan="5">No data in table</td>
                </tr>
                </tbody>
            </table>
            <Pagination :links="cards.links" />
        </div>
    </div>

</AuthenticatedLayout>
</template>

<style scoped>

</style>
