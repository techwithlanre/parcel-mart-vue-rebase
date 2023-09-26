<script setup>
import {ref, onMounted} from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {useForm, Link, usePage} from "@inertiajs/vue3";

const page = usePage();

const props = defineProps({
  customer_feedback: Object,
  c_role: String,
  getReplies: Array
})

const userPermissions = ref([]);

onMounted(() => {
  userPermissions.value = page.props.auth.permissions;
})

const checkPermission = (permission) => {
  return userPermissions.value.includes(permission);
}

const form = useForm({});
const markAsReply = (ticket, reply) => {
  form.post(route('feedback.reply.read', [ticket, reply]));
}
</script>

<template>
  <DashboardLayout :page-title="`Ticket - ${customer_feedback.feedback_subject}`" v-if="checkPermission('read-ticket')">

    <div class="flex flex-col mx-auto sm:max-w-5xl">
      <div class="flex flex-col p-6">
        <h1 class="text-3xl lg:text-3xl font-bold text-start capitalize"><b
            class="uppercase">#{{ customer_feedback.ticket_id }}</b> - {{ customer_feedback.feedback_subject }} </h1>
        <p class="text-sm mb-4 text-right">
          <Link v-if="checkPermission('create-user')" :href="route('feedback.tickets')"
                class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-background text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm sdark:focus:ring-offset-gray-800">
            Back
          </Link>
          <Link v-else :href="route('feedback.tickets.user')"
                class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-background text-primary hover:bg-primary hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm sdark:focus:ring-offset-gray-800">
            Back
          </Link>

        </p>
      </div>

      <div class="flex lg:flex-row flex-col justify-center  gap-10 mt-10 mb-5">
        <div class="card rounded-xl shadow border shadow-background/50 bg-white w-full">
          <div class="flex lg:flex-row bg-gray-50 rounded-xl justify-between">
            <div class="col-md-6">
              <p class="px-5 pt-3 font-bold">
                {{ customer_feedback.feedback_email }}
              </p>
              <p class="text-md px-5  pb-1">
                <span v-if="c_role == 1" class="rounded-xl bg-green-400 text-white px-5 py-1 text-xs font-normal capitalize hover:text-green-100">admin</span>
                <span v-else class="btn btn-sm rounded-xl bg-green-400 text-white px-5 py-1 text-sm font-medium capitalize hover:text-green-600">customer</span>
              </p>
            </div>
            <div class="col-md-6">
              <p class="text-sm px-5  pb-1 pt-3"><i class="fa fa-user"></i>
                <date-format short-month :has-time="true" :date="customer_feedback.submitted_on" />
              </p>
              <p class="text-center">
                <Link v-if="checkPermission('reply-ticket')" :href="route('feedback.tickets.reply', customer_feedback.ticket_id)"
                      class="btn btn-sm bg-green-400 text-white  px-5 py-1 text-sm hover:text-green-600 rounded-md">
                  Reply
                </Link>
              </p>
            </div>
          </div>
          <hr class="">
          <div class="flex flex-col font-bold space-y-4 mt-5 px-5">
            <div>Message:</div>
          </div>
          <div class="mt-5 px-5 mb-10">&nbsp;&nbsp;&nbsp;&nbsp;{{ customer_feedback.feedback_message }}</div>
        </div>
      </div>
      <section v-show="getReplies.length > 0" class="shadow rounded-xl">
        <p class="uppercase text-center font-bold text-primary py-5 bg-background rounded-t-xl">all replies</p>
        <div v-for="reply in getReplies" class="flex lg:flex-row flex-col justify-center gap-10 p-5">
          <div class="card rounded-xl  shadow border  shadow-background/50 bg-white w-full">
            <div class="flex lg:flex-row bg-gray-50 rounded-xl justify-between">
              <div class="">
                <p class="px-5 pt-3 font-bold">
                  <i class="">reply: </i>
                  {{ reply.feedback_name }}
                </p>

                <p class="text-md px-5  pb-1">
                  <span v-if="c_role == 1" class="rounded-xl bg-green-400 text-white px-5 py-1 text-xs font-normal capitalize hover:text-green-100">admin</span>
                  <span v-else class="btn btn-sm rounded-xl bg-green-400 text-white px-5 py-1 text-sm font-medium capitalize hover:text-green-600">customer</span>
                </p>
              </div>
              <div class="col-md-6">

                <p class="text-sm px-5  pb-1 pt-3"><i class="fa fa-user"></i>
                  <date-format short-month :has-time="true" :date="reply.submitted_on" />
                </p>
                <p v-if="reply.status === true" class="font-bold text-md px-5  pb-1"><i
                    class="bi bi-arrrow-right-circle-fill"></i>
                  <span class="btn btn-sm rounded-xl bg-green-400 text-white px-5 py-1 text-sm font-medium capitalize hover:text-green-600">NEW</span>
                  <Link v-if="checkPermission('create-user')" @click.prevent="markAsReply(customer_feedback.id,reply.id)"
                        class="py-2 px-3 inline-flex justify-center items-center gap-2 font-semibold  text-primary  text-sm capitalize">
                    mark as read
                  </Link>
                </p>

              </div>
            </div>
            <div class="flex flex-col font-bold space-y-4 mt-5 px-5">
              <div>Message:</div>
            </div>
            <div class="mt-5 px-5 mb-10">&nbsp;&nbsp;&nbsp;&nbsp;{{ reply.feedback_message }}</div>

          </div>

        </div>
      </section>
    </div>
  </DashboardLayout>

</template>

<style scoped>

</style>