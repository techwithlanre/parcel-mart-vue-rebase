<script setup>

import {ref} from "vue";
import {Link, Head, useForm, usePage} from "@inertiajs/vue3"
import DashboardLayout from "@/Layouts/DashboardLayout.vue";


import FeedbackLayout from "@/Pages/CustomerFeedback/Layouts/FeedbackLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";

import PrimaryButton from "@/Components/PrimaryButton.vue";


defineProps({
    getFeedback: Object,
    ticketID: String,
})

const page = usePage();

const form = useForm({
  feedback_message: '',
});



//methods
const submit = (data) => {

  form.post(route('feedback.tickets.reply.save', data));
}

</script>

<template>
    <DashboardLayout :page-title="`reply: Ticket #${ticketID} - ${getFeedback.feedback_subject}`" >
        <Head title="Ticket Reply" />
        <p class="text-center text-sm mb-4 text-right">
        <Link :href="route('feedback.ticket.show', ticketID)"  class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-background text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm sdark:focus:ring-offset-gray-800">Back</Link>
        </p>

    <FeedbackLayout>
        
        <div class="w-full">
          <div class="p-5">
            <h3 class="text-start uppercase">#{{ticketID}} - {{getFeedback.feedback_subject}}</h3>
          </div>
          <hr>
          <form @submit.prevent="submit(ticketID)">
            <div class="p-6 flex flex-col gap-y-2">

              <div>
                <InputLabel value="Message *" class="font-normal"/>
                <TextAreaInput v-model="form.feedback_message" rows="7" class="mt-2"
                   placeholder="leave a message.."/>
                <InputError class="mt-2"  :message="form.errors.feedback_message" />
              </div>
              
              <div class="flex flex-col items-center justify-end mt-6">
                <PrimaryButton type="submit" class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  SUBMIT FEEDBACK REPLY
                </PrimaryButton>
              </div>
            </div>
          </form>
        </div>
      </FeedbackLayout>
    </DashboardLayout>
</template>