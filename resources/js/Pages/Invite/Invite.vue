<script setup>
import {ref} from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
  code: String,
  referred: Array
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

const form = useForm({

});

const generateCode = () => {
  form.get(route('invite.generate'))
}
</script>

<template>
  <DashboardLayout page-title="Invite">
    <div class="flex flex-col p-6 lg:px-[250px]">
      <h1 class="text-3xl lg:text-3xl font-bold text-center">Your <br>Referral Code</h1>
      <p class="text-center text-sm mb-10">Invite friends and family to earn rewards</p>
      <div class="flex gap-x-20">
        <div class="flex flex-col bg-white w-full ">
          <div v-if="code != null" class="text-center flex flex-col items-center">
            <button @click="copy" class="mx-auto w-max  rounded-full px-5 py-2 text-white font-semibold items-center gap-x-2 flex flex-row text-center bg-primary">
              <span ref="ref_code">{{ code }}</span>
              <button type="button" class="p-1 rounded-full bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                </svg>
              </button>
            </button>
            <div v-if="copyMessage.length > 0" class="duration-500 transition-all ease-in-out text-center mt-5 text-sm px-3 py-1 border rounded-full w-max">{{ copyMessage }}</div>
          </div>
          <PrimaryButton v-else :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-max flex justify-center items-center mx-auto mt-10" @click="generateCode">Generate Referral Code</PrimaryButton>
        </div>
      </div>
      <div class="rounded-xl border mt-20">
        <table class="w-full text-sm text-left text-gray-500 rounded-xl">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 rounded-xl">
            <tr class="bg-gray-100 rounded-t-xl">
              <th class="text-left p-4 font-medium">Name</th>
              <th class="text-left p-4 font-medium">Date</th>
            </tr>
          </thead>
          <tbody>
          <tr class="hover:bg-gray-50" v-for="item in referred">
            <td class="p-4">
              <h1 class="text-md font-bold">{{ item.first_name }} {{ item.last_name }}</h1>
            </td>
            <td class="p-4">
              <date-format>{{ item.created_at }}</date-format>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </DashboardLayout>
</template>

<style scoped>

</style>