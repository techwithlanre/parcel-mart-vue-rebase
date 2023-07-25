<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

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

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
        </template>

        <div class="flex flex-col items-center justify-center w-2/5 mx-auto">
            <div class="mx-auto space-y-6 w-full">
                <div class="p-4 w-full sm:p-8 bg-white rounded-2xl shadow shadow-background">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="w-full"
                    />
                </div>

                <div class="p-4 w-full sm:p-8 bg-white rounded-2xl shadow shadow-background">
                    <UpdatePasswordForm class="max-w-4xl" />
                </div>

<!--                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>-->
            </div>
        </div>
    </AuthenticatedLayout>
</template>
