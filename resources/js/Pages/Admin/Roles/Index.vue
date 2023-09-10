<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link, usePage} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

export default {
    components: {DashboardLayout, PrimaryButton, AuthenticatedLayout, Link, Head},
    props: {
        roles: Array
    },
  data() {
    return {
      page: usePage(),
      userPermissions: []
    }
  },
  methods: {
    submit: function () {
      this.form.post(route('roles.update-permissions', this.role.id))
    },

    checkPermission: function (permission) {
      return this.userPermissions.includes(permission);
    }
  },

  mounted() {
    this.userPermissions = this.page.props.auth.permissions;
    console.log(this.userPermissions)
  }
}
</script>

<template>
    <Head title="Roles Management" />
    <DashboardLayout page-title="Roles Management">
        <div class="mb-5 flex flex-row justify-end">
            <Link v-show="checkPermission('create-role')" class="" :href="route('roles.create')">
                <PrimaryButton class="w-max">Create Role</PrimaryButton>
            </Link>
        </div>
        <div class="card p-5 border bg-white rounded-xl" v-show="checkPermission('read-role')">
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
                            <div>
                                <Link v-show="checkPermission('edit-role')" class="text-primary hover:text-primary-dark" :href="route('roles.edit', item.id)">Edit</Link> |
                                <Link v-show="checkPermission('edit-permission')" class="text-primary hover:text-primary-dark" :href="route('roles.show', item.id)">Permissions</Link>
                            </div>
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
