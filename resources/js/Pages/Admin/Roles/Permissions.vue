<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link, useForm, usePage} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

export default {
    components: {DashboardLayout, Checkbox, PrimaryButton, AuthenticatedLayout, Link, Head},
    props: {
        permissions: Array,
        role: String
    },
    data() {
        return {
            form: useForm({
                'create-user': this.permissions['create-user'],
                'read-user': this.permissions['read-user'],
                'edit-user': this.permissions['edit-user'],
                'delete-user': this.permissions['delete-user'],
            }),
            page: usePage()
        }
    },

    methods: {
        submit: function () {
            this.form.post(route('roles.update-permissions', this.role.id))
        }
    }
}
</script>

<template>
    <Head title="Roles Management" />
    <DashboardLayout page-title="Roles Management">
        <div class="mb-5 flex flex-row justify-end">
            <Link class="btn btn-primary" :href="route('roles.create')"><PrimaryButton class="w-max">Create Role</PrimaryButton></Link>
        </div>
        <div class="card p-5 border bg-white">
            <h1>Update permissions for <span class="font-bold">{{ role.name }}</span></h1>
            <form @submit.prevent="submit">
                <div class="overflow-x-auto border-x border-t rounded-xl mt-5">
                    <table class="table-auto w-full ">
                        <thead class="border-b">
                        <tr class="bg-gray-100">
                            <th class="text-left p-4 font-medium">Permission</th>
                            <th class="text-left p-4 font-medium">Create</th>
                            <th class="text-left p-4 font-medium">Read</th>
                            <th class="text-left p-4 font-medium">Update</th>
                            <th class="text-left p-4 font-medium">Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <h1 class="tex-tmd font-bold">Users</h1>
                            </td>
                            <td class="p-4"><Checkbox v-model="form['create-user']" :checked="form['create-user']" /></td>
                            <td class="p-4"><Checkbox v-model="form['read-user']" :checked="form['read-user']" /></td>
                            <td class="p-4"><Checkbox v-model="form['edit-user']" :checked="form['edit-user']" /></td>
                            <td class="p-4"><Checkbox v-model="form['delete-user']" :checked="form['delete-user']" /></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <PrimaryButton class="mt-10 w-max" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Update</PrimaryButton>
            </form>


        </div>
    </DashboardLayout>
</template>

<style scoped>

</style>
