<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";

defineProps({
    users: Array
})

</script>

<template>
    <AuthenticatedLayout page-title="Users">
        <div class="card p-5 border bg-white">
            <h1>Users List</h1>

            <div class="overflow-x-auto border-x border-t rounded-xl">
                <table class="table-auto w-full ">
                    <thead class="border-b">
                    <tr class="bg-gray-100">
                        <th class="text-left p-4 font-medium">Name</th>
                        <th class="text-left p-4 font-medium">Email</th>
                        <th class="text-left p-4 font-medium">Phone</th>
                        <th class="text-left p-4 font-medium">Wallet</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="border-b hover:bg-gray-50" v-for="item in users.data">
                        <td class="p-4">
                            <h1 class="text-md font-bold">{{ item.first_name }} {{ item.last_name }}</h1>
                        </td>
                        <td class="p-4">{{ item.email }}</td>
                        <td class="p-4">{{ item.phone }}</td>
                        <td class="p-4">{{ item.wallet.balance }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
      <Pagination :links="users.links"/>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
