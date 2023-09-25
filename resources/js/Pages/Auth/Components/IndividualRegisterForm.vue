<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import {useForm, Link, Head} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";

export default {
    components: {InputLabel, SelectInput, PrimaryButton, InputError, TextInput, Link, Head},
    data() {
        return {
          form: useForm({
            first_name: '',
            last_name: '',
            email: '',
            password: '',
            phone: '',
            country: '0',
            dob: '',
            gender: '',
            password_confirmation: '',
            ref_by: ''
          }),
        }
    },

    props: {
      countries: Array,
      genderOptions: Array
    },

    methods: {
        submit() {
            this.form.post(route('register.index'), {
                onFinish: () => this.form.reset('password', 'password_confirmation'),
            });
        },
    },


}


</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid lg:grid-cols-2 grid-cols-1 justify-between gap-x-4 gap-y-3">
            <div>
              <InputLabel value="First Name *" />
              <TextInput
                  id="first_name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.first_name"
                  
                  autofocus
                  autocomplete="first_name" />

              <InputError class="mt-1" :message="form.errors.first_name" />
            </div>
            <div>
              <InputLabel value="Last Name *" />
              <TextInput
                  id="last_name"
                  type="text"
                  class="mt-1 block w-full"
                  v-model="form.last_name"
                  
                  autocomplete="last_name" />
              <InputError class="mt-1" :message="form.errors.last_name" />
            </div>
        </div>

        <div class="mt-2">
          <InputLabel value="Email *" />
          <TextInput
              id="email"
              type="email"
              class="mt-1 block w-full"
              v-model="form.email"
              
              autocomplete="email" />

          <InputError class="mt-1" :message="form.errors.email" />
        </div>

        <div class="mt-2">
          <InputLabel value="Phone *" />
          <TextInput
              id="phone"
              type="tel"
              class="mt-1 block w-full"
              v-model="form.phone"
              
              autocomplete="phone" />

          <InputError class="mt-1" :message="form.errors.phone" />
        </div>

        <div class="grid lg:grid-cols-2 grid-cols-1 justify-between gap-x-4 gap-y-3 mt-3">
            <div class="">
              <InputLabel value="Password *" />
              <TextInput
                  id="password"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="form.password"
                  
                  autocomplete="new-password" />

              <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="">
              <InputLabel value="Confirm Password *" />
              <TextInput
                  id="password_confirmation"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="form.password_confirmation"
                  
                  autocomplete="new-password" />

              <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>
        </div>

        <div class="grid lg:grid-cols-2 grid-cols-1 justify-between gap-x-4 gap-y-3 mt-3">
          <div class="">
            <InputLabel value="Date of Birth *" />
            <TextInput
                id="dob"
                type="date"
                class="mt-1 block w-full"
                v-model="form.dob"
                autocomplete="date_of_birth" />

            <InputError class="mt-1" :message="form.errors.dob" />
          </div>

          <div class="">
            <InputLabel value="Gender *" />
            <SelectInput
                
                place-holder=""
                class="block w-full mt-1"
                v-model="form.gender"
                :options="genderOptions"
            />

            <InputError class="mt-1" :message="form.errors.gender" />
          </div>
        </div>

        <div class="">
            <div class="mt-2">
              <InputLabel value="Country *" />
                <SelectInput
                    place-holder=""
                    class="block w-full mt-1"
                    v-model="form.country"
                    :options="countries"
                />

                <InputError class="mt-1" :message="form.errors.country" />
            </div>
        </div>

        <div class="mt-2">
          <InputLabel value="Referral Code" />
          <TextInput
              id="ref_by"
              type="text"
              class="mt-1 block w-full"
              v-model="form.ref_by"
              autocomplete="ref_by" />

          <InputError class="mt-1" :message="form.errors.ref_by" />
        </div>

        <div class="flex flex-col items-center justify-end mt-6">
            <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Sign Up
            </PrimaryButton>
            <div class="mt-5">
                Already registered?
                <Link :href="route('login')"
                      class="underline text-gray-600 hover:text-gray-900 rounded-md">
                    Sign In
                </Link>
            </div>
        </div>
    </form>
</template>

<style scoped>

</style>
