<script setup>
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import UpgradeAccount from "@/Pages/Profile/Partials/UpgradeAccount.vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <DashboardLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
        </template>

        <div class="flex flex-col items-left justify-center sm:w-2/5 w-full">
            <div class="mx-auto space-y-6 w-full">
                <div class="p-4 w-full sm:p-8 bg-white rounded-2xl shadow shadow-background">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="w-full"
                    />
                </div>

              <div class="p-4 w-full sm:p-8 bg-white rounded-2xl shadow shadow-background">
                <UpgradeAccount class="max-w-xl" />
              </div>

                <div class="p-4 w-full sm:p-8 bg-white rounded-2xl shadow shadow-background">
                    <UpdatePasswordForm class="max-w-4xl" />
                </div>


            </div>
        </div>
    </DashboardLayout>
</template>
