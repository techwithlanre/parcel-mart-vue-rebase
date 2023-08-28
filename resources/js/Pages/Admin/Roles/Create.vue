<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import {useForm} from "@inertiajs/vue3";
import {toast} from "vue3-toastify";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

export default {
    name: "Create",
    components: {DashboardLayout, SelectInput, TextInput, InputError, InputLabel, PrimaryButton, AuthenticatedLayout},
    data() {
        return {
            form: useForm({
                name: ""
            })
        }
    },

    methods: {
        submit: function (){
            this.form.post(route('roles.store'))
        }
    }
}
</script>

<template>
    <DashboardLayout page-title="Create Role">
        <div >
            <div class="mx-auto lg:mt-17 mt max-w-2xl p-10 bg-white  rounded-2xl border">
                <div>
                    <h3 class="font-bold text-2xl ">Create new role</h3>
                </div>
                <form class="mt-6" @submit.prevent="submit">
                    <div class="">
                        <InputLabel value="Role"  />
                        <TextInput
                            v-model="form.name"
                            type="text"
                            placeholder="Enter role name"
                            class="mt-2 flex"
                            required/>

                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>


                    <div class="flex flex-col items-center justify-end mt-6">
                        <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Create
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>

</style>
