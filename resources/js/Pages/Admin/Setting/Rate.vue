<script setup>
import {Link, useForm, usePage} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import {toast} from "vue3-toastify";

import {DocumentIcon, WalletIcon} from "@heroicons/vue/20/solid/index.js";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const tabs = [
    { name: "Description", icon: WalletIcon, route: "description" },
    { name: "Measurement", icon: DocumentIcon, route: "measurement" }
];

const page = usePage();
const userPermissions = ref([]);

const count = ref(5);
const visible = ref(false);
const confirmLoading = ref(false);

const showModal = () => {
    visible.value = true;
};

const handleOk = () => {
    confirmLoading.value = true;
    setTimeout(() => {
        submit();
        visible.value = false;
        confirmLoading.value = false;

    }, 2000);
};

const form = useForm({
    description: ''
});

defineProps({
    providers: Array
});

const submit = () => {
    form.post(route('shipping.setting.description.post'), {
        onFinish: () => toast.success('Description Added'),
    })
}

onMounted(() => {
  userPermissions.value = page.props.auth.permissions;
})

const checkPermission = (permission) => {
  return userPermissions.value.includes(permission);
}

</script>

<template>
    <DashboardLayout page-title="API Providers">
        <div class="shadow rounded-xl">
          <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-gray-200">
            <div>
              <h2 class="text-xl font-semibold text-gray-800">
                API Providers
              </h2>
              <p class="text-sm text-gray-600">
                List of API Providers and their profit margin
              </p>
            </div>

          </div>
            <div class="overflow-x-auto shadow-background" v-show="checkPermission('read-provider')">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="rounded-t-3xl">
                      <tr class="bg-gray-100">
                          <td class="font-bold p-4">Name</td>
                          <td class="font-bold p-4">Status</td>
                          <td class="font-bold p-4">Profit Margin</td>
                          <td class="font-bold p-4">Action</td>
                      </tr>
                    </thead>
                    <tbody>
                    <tr v-if="providers.length > 0" v-for="item in providers" class="hover:bg-gray-100 border-b transition-colors group px-5">
                        <td class="p-4">{{ item.name }}</td>
                        <td class="p-4">{{ item.status }}</td>
                        <td class="p-4">{{ item.profit_margin }}%</td>
                        <td class="p-4"><Link v-show="checkPermission('edit-provider')" :href="route('setting.rate.edit', item.id)" class="text-primary">Edit</Link></td>
                    </tr>
                    <tr v-else class="hover:bg-gray-100 transition-colors group border-b px-5">
                        <td class="text-sm p-4 text-center text-gray-400" colspan="5">No data in table</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </DashboardLayout>

</template>

<style scoped>

</style>
