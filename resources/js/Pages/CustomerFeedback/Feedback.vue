<script setup>


import {Link, Head, useForm, usePage} from "@inertiajs/vue3"
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import FeedbackLayout from "@/Pages/CustomerFeedback/Layouts/FeedbackLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";


import PrimaryButton from "@/Components/PrimaryButton.vue";



const page = usePage();

const form = useForm({
  feedback_message: '',
  feedback_subject: '',
  'feedback_file': '',
});

//methods
const submit = () => {

  form.post(route('feedback.store'), { forceFormData: true,});
}

</script>

<template>
    <DashboardLayout page-title="Customer Feedback">
        <Head title="Customer Feedback" />

    <FeedbackLayout>
        <div class="w-full">
          <div class="p-5">
            <h3 class="text-start uppercase">open a ticket</h3>
          </div>
          <hr>
 
          <form @submit.prevent="submit">
            <div class="p-6 flex flex-col gap-y-2">
              <div class="">
                <InputLabel value="Subject *"/>
                <TextInput
                    id="feedback_subject"
                    v-model="form.feedback_subject"
                    type="text"
                    class="mt-2 w-full"
                    placeholder=""
                    autocomplete="feedback_subject"/>
                <InputError class="mt-2" :message="form.errors.feedback_subject" />
              </div>

              <div class="mt-2 mb-2">
                <input type="file" @input="form.feedback_file = $event.target.files[0]" />
                <progress v-if="form.feedback_file" :value="form.progress.percentage" max="100">
                  {{ form.progress.percentage }}%
                </progress>

                <InputError class="mt-2" :message="form.errors.feedback_file" />
              </div>

              <div>
                <InputLabel value="Message *" class="font-normal"/>
                <TextAreaInput v-model="form.feedback_message" rows="7" class="mt-2"
                   placeholder="leave a message.."/>
                <InputError class="mt-2"  :message="form.errors.feedback_message" />
              </div>
              


              <div class="flex flex-col items-center justify-end mt-6">
                <PrimaryButton type="submit" class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  SUBMIT FEEDBACK
                </PrimaryButton>
              </div>
            </div>
          </form>
        </div>
      </FeedbackLayout>
    </DashboardLayout>
</template>