<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    components: {PrimaryButton, AuthenticatedLayout, Link, Head},
    props: {
        roles: Array
    }
}
</script>

<template>
    <Head title="Roles Management" />
    <AuthenticatedLayout page-title="Roles Management">
        <div class="mb-5 flex flex-row justify-end">
            <Link class="btn btn-primary" :href="route('roles.create')">
                <PrimaryButton class="w-max">Create Role</PrimaryButton>
            </Link>
        </div>
        <div class="card p-5 border bg-white">
            <h1>Roles List</h1>
            <div class="overflow-x-auto border-x border-t rounded-xl mt-5">
                <table class="table-auto w-full ">
                    <thead class="border-b">
                    <tr class="bg-gray-100">
                        <th class="text-left p-4 font-medium">Role</th>
                        <th class="text-left p-4 font-medium">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="border-b hover:bg-gray-50" v-for="item in roles">
                        <td class="p-4">
                            <h1 class="text-md font-bold">{{ item.name }}</h1>
                        </td>
                        <td class="p-4">
                            <div v-if="item.name !== 'admin'">
                                <Link class="text-primary hover:text-primary-dark" :href="route('roles.edit', item.id)">Edit</Link> |
                                <Link class="text-primary hover:text-primary-dark" :href="route('roles.show', item.id)">Permissions</Link>
                            </div>
                            <div v-else class="text-gray-400">
                                Edit | Permissions
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
