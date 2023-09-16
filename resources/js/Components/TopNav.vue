<script setup>
import {Link, usePage} from "@inertiajs/vue3";

const page = usePage();

defineProps({
    tabs: Array
});
const checkPermission = (featurePermissions) => {
  return page.props.auth.permissions.some( ai => featurePermissions.includes(ai));
}
</script>
<template>
  <div style="">
    <ul class="flex sm:flex-row flex-col gap-x-24 items-center px-4 shadow-xs mb-10">
      <li v-for="tab in tabs">
        <Link :href="tab.route" v-if="checkPermission(tab.permissionKey)">
          <button
              class="flex gap-x-2 items-center py-5 px-6 text-gray-400 hover:text-primary relative group" :class="{'text-primary font-semibold' : $page.url.includes(tab.route)}">
            <Component :is="tab.icon" class="w-5 h-5 fill-current" />
            <span class="text-sm"> {{ tab.name }} </span>
            <span class="absolute w-full h-0.5 left-3 bg-primary rounded bottom-0 scale-x-0 group-hover:scale-x-100 transition-transform ease-in-out"
                  :class="{'bg-primary' : $page.url.includes(tab.route)}"/>
          </button>
        </Link>
      </li>
    </ul>
  </div>
</template>

<style scoped>

</style>
