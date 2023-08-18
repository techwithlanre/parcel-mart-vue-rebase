<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const props = defineProps({
  code: String
})

const ref_code = ref(null)
const copyMessage = ref("")

const copy = () => {
  navigator.clipboard.writeText(props.code).then(() => {
        copyMessage.value = "Copied";
        setTimeout(() => {
          copyMessage.value = ""; // Clear the result message after 5 seconds
        }, 5000); // 5000 milliseconds = 5 seconds
      },
      (err) => {
        console.error("Could not copy text: ", err);
      }
  );
}
</script>

<template>
  <DashboardLayout page-title="Invite & Earn">
    <div class="flex flex-col p-6 lg:px-[250px]">
      <h1 class="text-3xl lg:text-5xl font-bold text-center">Your <br>Referral Code</h1>
<!--      <p class="text-center text-sm mb-10">Don't just take our words, hear from our customers</p>-->
      <div class="flex gap-x-20">
        <div class="flex flex-col bg-white w-full">
          <div class="text-center flex flex-col items-center">
            <button @click="copy" class="mx-auto w-max  rounded-full px-5 py-2 text-white font-semibold items-center gap-x-2 flex flex-row text-center bg-primary">
              <span ref="ref_code">{{ code }}</span>
              <button type="button" class="p-1 rounded-full bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                </svg>
              </button>
            </button>
            <div v-if="copyMessage.length > 0" class="duration-300 transition-all ease-in-out text-center mt-5 text-sm px-3 py-1 border rounded-full w-max">{{ copyMessage }}</div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<style scoped>

</style>