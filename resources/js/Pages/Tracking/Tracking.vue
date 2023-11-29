<script setup>
import { ref } from 'vue'
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import HomeLayout from "@/Layouts/HomeLayout.vue";
import {useForm, Link} from "@inertiajs/vue3";
import {toast} from "vue3-toastify";
import InputError from "@/Components/InputError.vue";

const activeKey = ref('1');

const form = useForm({
  number: ''
})


const handleButtonClick = e => {
    //console.log('click left button', e);
};
const handleMenuClick = e => {
    //console.log('click', e);
};

const submit = () => {
  form.post(route('home.shipment.track'), {
    onError: () => {
      toast.error(form.errors.number)
    }
  })
}

</script>

<template>
    <HomeLayout :show-footer="false" class="">
        <div class="h-screen">
          <div class="px-6">
            <Link href="https://parcelsmartsolution.com" class="flex flex-row items-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
              </svg>
              Back to home
            </Link>
          </div>
          <div class="relative isolate px-6 mt-36">
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
              <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-primary to-primary-dark opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
            </div>
            <div class="flex flex-row items-center justify-center">
              <form @submit.prevent="submit">
                <div class="w-full flex flex-col text-center">
                  <h3 class="font-bold text-4xl">Track Your Package</h3>
                  <p>Enter your tracking number in the form below to start tracking your package</p>
                  <div class="mt-10">
                    <TextInput
                        id="email"
                        type="text"
                        class="max-w-5xl w-full"
                        v-model="form.number"
                        required
                        autofocus
                        autocomplete="off"
                        placeholder="Enter tracking number"
                    />
                    <InputError class="mt-2" :message="form.errors.number" />
                  </div>
                  <div class="h-4 mt-5">
                    <PrimaryButton class="text-center max-w-5xl">
                      Track
                    </PrimaryButton>
                  </div>
                </div>
              </form>
            </div>
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
              <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary to-primary-dark opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
            </div>
          </div>
        </div>
    </HomeLayout>
</template>

<style scoped>
img {
    transform: translate3d(0vw, 0vh, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg);
    transform-style: preserve-3d;
}
</style>
