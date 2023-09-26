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
              'create-shipment': this.permissions['create-shipment'],
              'read-shipment': this.permissions['read-shipment'],
              'edit-shipment': this.permissions['edit-shipment'],
              'delete-shipment': this.permissions['delete-shipment'],
              'create-provider': this.permissions['create-provider'],
              'read-provider': this.permissions['read-provider'],
              'edit-provider': this.permissions['edit-provider'],
              'delete-provider': this.permissions['delete-provider'],
              'create-role': this.permissions['create-role'],
              'read-role': this.permissions['read-role'],
              'edit-role': this.permissions['edit-role'],
              'delete-role': this.permissions['delete-role'],
              'create-permission': this.permissions['create-permission'],
              'read-permission': this.permissions['read-permission'],
              'edit-permission': this.permissions['edit-permission'],
              'delete-permission': this.permissions['delete-permission'],
              'read-dashboard': this.permissions['read-dashboard'],
              'read-shipment-report': this.permissions['read-shipment-report'],
              'read-tax-report': this.permissions['read-tax-report'],
              'read-payment-report': this.permissions['read-payment-report'],
              'read-user-report': this.permissions['read-user-report'],
              'read-ticket': this.permissions['read-ticket'],
              'create-ticket': this.permissions['create-ticket'],
              'reply-ticket': this.permissions['reply-ticket'],
              'delete-ticket': this.permissions['delete-ticket'],

            }),
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
    }
}
</script>

<template>
    <DashboardLayout page-title="User Permissions">
        <div class="mb-5 flex flex-row justify-end">
            <Link class="btn btn-primary" :href="route('roles.create')"><PrimaryButton class="w-max">Create Role</PrimaryButton></Link>
        </div>
        <div class="card p-5 border bg-white rounded-xl" v-show="checkPermission('read-permission')">
            <h1>Update permissions for <span class="font-bold">{{ role.name }}</span></h1>
            <form @submit.prevent="submit">
                <div class="overflow-x-auto border-x border-t rounded-xl mt-5">
                    <table class="table-auto w-full ">
                        <thead class="border-b">
                        <tr class="bg-gray-100">
                            <th class="text-left p-4 font-bold">Permission</th>
                            <th class="text-left p-4 font-bold">Create</th>
                            <th class="text-left p-4 font-bold">Read</th>
                            <th class="text-left p-4 font-bold">Update</th>
                            <th class="text-left p-4 font-bold">Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">Roles</h1>
                            </td>
                            <td class="p-4"><Checkbox v-model="form['create-role']" :checked="form['create-role']" /></td>
                            <td class="p-4"><Checkbox v-model="form['read-role']" :checked="form['read-role']" /></td>
                            <td class="p-4"><Checkbox v-model="form['edit-role']" :checked="form['edit-role']" /></td>
                            <td class="p-4"><Checkbox v-model="form['delete-role']" :checked="form['delete-role']" /></td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">Permissions</h1>
                            </td>
                            <td class="p-4"><Checkbox v-model="form['create-permission']" :checked="form['create-permission']" /></td>
                            <td class="p-4"><Checkbox v-model="form['read-permission']" :checked="form['read-permission']" /></td>
                            <td class="p-4"><Checkbox v-model="form['edit-permission']" :checked="form['edit-permission']" /></td>
                            <td class="p-4"><Checkbox v-model="form['delete-permission']" :checked="form['delete-permission']" /></td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">Dashboard</h1>
                            </td>
                            <td class="p-4">N/A</td>
                            <td class="p-4"><Checkbox v-model="form['read-dashboard']" :checked="form['read-dashboard']" /></td>
                            <td class="p-4">N/A</td>
                            <td class="p-4">N/A</td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                              <td class="p-4">
                                  <h1 class="font-normal">Users</h1>
                              </td>
                              <td class="p-4"><Checkbox v-model="form['create-user']" :checked="form['create-user']" /></td>
                              <td class="p-4"><Checkbox v-model="form['read-user']" :checked="form['read-user']" /></td>
                              <td class="p-4"><Checkbox v-model="form['edit-user']" :checked="form['edit-user']" /></td>
                              <td class="p-4"><Checkbox v-model="form['delete-user']" :checked="form['delete-user']" /></td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">Shipment</h1>
                            </td>
                            <td class="p-4"><Checkbox v-model="form['create-shipment']" :checked="form['create-shipment']" /></td>
                            <td class="p-4"><Checkbox v-model="form['read-shipment']" :checked="form['read-shipment']" /></td>
                            <td class="p-4"><Checkbox v-model="form['edit-shipment']" :checked="form['edit-shipment']" /></td>
                            <td class="p-4"><Checkbox v-model="form['delete-shipment']" :checked="form['delete-shipment']" /></td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">API Providers</h1>
                            </td>
                            <td class="p-4"><Checkbox v-model="form['create-provider']" :checked="form['create-provider']" /></td>
                            <td class="p-4"><Checkbox v-model="form['read-provider']" :checked="form['read-provider']" /></td>
                            <td class="p-4"><Checkbox v-model="form['edit-provider']" :checked="form['edit-provider']" /></td>
                            <td class="p-4"><Checkbox v-model="form['delete-provider']" :checked="form['delete-provider']" /></td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">Shipment Report</h1>
                            </td>
                            <td class="p-4">N/A</td>
                            <td class="p-4"><Checkbox v-model="form['read-shipment-report']" :checked="form['read-shipment-report']" /></td>
                            <td class="p-4">N/A</td>
                            <td class="p-4">N/A</td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">User Report</h1>
                            </td>
                            <td class="p-4">N/A</td>
                            <td class="p-4"><Checkbox v-model="form['read-user-report']" :checked="form['read-user-report']" /></td>
                            <td class="p-4">N/A</td>
                            <td class="p-4">N/A</td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">Payment Report</h1>
                            </td>
                            <td class="p-4">N/A</td>
                            <td class="p-4"><Checkbox v-model="form['read-payment-report']" :checked="form['read-payment-report']" /></td>
                            <td class="p-4">N/A</td>
                            <td class="p-4">N/A</td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">Tax Report</h1>
                            </td>
                            <td class="p-4">N/A</td>
                            <td class="p-4"><Checkbox v-model="form['read-tax-report']" :checked="form['read-tax-report']" /></td>
                            <td class="p-4">N/A</td>
                            <td class="p-4">N/A</td>
                          </tr>
                          <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                              <h1 class="font-normal">Tickets</h1>
                            </td>
                            <td class="p-4"><Checkbox v-model="form['create-ticket']" :checked="form['create-ticket']" /></td>
                            <td class="p-4"><Checkbox v-model="form['read-ticket']" :checked="form['read-ticket']" /></td>
                            <td class="p-4"><Checkbox v-model="form['reply-ticket']" :checked="form['reply-ticket']" /> (Reply)</td>
                            <td class="p-4"><Checkbox v-model="form['delete-ticket']" disabled="true" :checked="form['delete-ticket']" class="bg-red-500" /></td>
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
