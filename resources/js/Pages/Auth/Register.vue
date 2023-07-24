<script>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import SelectInput from "@/Components/SelectInput.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import IndividualRegisterForm from "@/Pages/Auth/Components/IndividualRegisterForm.vue";
import BusinessRegisterForm from "@/Pages/Auth/Components/BusinessRegisterForm.vue";

export default {
    components: {
        BusinessRegisterForm,
        IndividualRegisterForm,
        PrimaryButton,
        SelectInput,
        InputError,
        TextInput,
        GuestLayout,
        Link,
        Head
    },
    data() {
        return {
            currentTab: 1,
        }
    },

    props: {
        countries: Array,
    },

    methods: {
        submit() {
            this.form.post(route('register.index'), {
                onFinish: () => this.form.reset('password', 'password_confirmation'),
            });
        },

        toggleTabs(index) {
            this.currentTab = index;
            console.log(this.currentTab);
        }
    }
}
</script>

<template>
    <Head title="Register" />
    <GuestLayout title="Get Started" subtitle="Create an account to get started">
        <div class="mb-10 px-2 rounded-md py-2 border-2 border-background">
            <ul class="flex mb-0 list-none flex-wrap flex-row">

                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a class="text-xs font-bold uppercase px-5 py-3 shadow-lg rounded block leading-normal hover:text-gray-900" @click="toggleTabs(1)" v-bind:class="{'text-gray-500 bg-white': currentTab !== 1, 'text-white bg-primary': currentTab === 1}">
                        Individual
                    </a>
                </li>
                <li class="-mb-px mr-2 last:mr-0 flex-auto text-center">
                    <a class="text-xs font-bold uppercase px-5 py-3 shadow-lg rounded block leading-normal hover:text-gray-900" @click="toggleTabs(2)" v-bind:class="{'text-gray-500 bg-white': currentTab !== 2, 'text-white bg-primary': currentTab === 2}">
                        Business
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content tab-space">
            <div id="tab1" v-bind:class="{'hidden': currentTab !== 1, 'block': currentTab === 1}">
                <IndividualRegisterForm :countries="countries" />
            </div>
            <div v-bind:class="{'hidden': currentTab !== 2, 'block': currentTab === 2}">
                <BusinessRegisterForm :countries="countries" />
            </div>
        </div>
    </GuestLayout>
</template>
