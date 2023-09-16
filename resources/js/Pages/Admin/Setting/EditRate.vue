<script setup>
import {onMounted, ref} from 'vue'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineProps({
    provider: Object
})

const page = usePage();

const status = [
    {
        id: 'active',
        name: 'active'
    },
    {
        id: 'inactive',
        name: 'inactive'
    },
];

const form  = useForm({
    name: page.props.provider.name,
    status: page.props.provider.status,
    profit_margin: page.props.provider.profit_margin,
});

const submit = () => {
    form.put(route('setting.rate.update', page.props.provider.id), {

    })
}

const userPermissions = ref([]);

onMounted(() => {
  userPermissions.value = page.props.auth.permissions;
})

const checkPermission = (permission) => {
  return userPermissions.value.includes(permission);
}

</script>

<template>
    <DashboardLayout page-title="Edit Rate">
        <div >
            <div v-show="checkPermission('edit-provider')" class="mx-auto lg:mt-17 mt max-w-2xl p-10 bg-white  rounded-2xl border" >
                <div>
                    <h3 class="font-bold text-2xl ">Edit rate</h3>
                </div>
                <form class="mt-6" @submit.prevent="submit">
                    <div class="">
                        <InputLabel value="Provider"  />
                        <TextInput
                            v-model="form.name"
                            type="text"
                            readonly
                            class="mt-2 flex"
                            required/>

                        <InputError class="mt-2" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Status"  />
                        <SelectInput
                            id="country"
                            type="text"
                            class="mt-3"
                            :options="status"
                            v-model="form.status"/>

                        <InputError class="mt-2" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Profit Margin (%) *"  />
                        <TextInput
                            id="address"
                            type="text"
                            class="mt-2 flex"
                            v-model="form.profit_margin"
                            required
                            autofocus
                            placeholder="Start typing address"
                            autocomplete="address" />
                        <InputError class="mt-2" />
                    </div>
                    <div class="flex flex-col items-center justify-end mt-6">
                        <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Update Rate
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>

</style>
