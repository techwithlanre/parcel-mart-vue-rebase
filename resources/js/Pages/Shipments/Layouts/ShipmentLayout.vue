<script setup>


const tabs = [
  {name: 'Origin Address', index: 1, content: "", key: 'origin', disabled: false},
  {name: 'Destination Address', index: 2, content: "", key: 'destination', disabled: true},
  {name: 'Package Information', index: 3, content: "", key: 'package-information', disabled: true}
];
</script>

<template>
  <div class="sm:w-3/5 mx-auto">
    <div class="flex flex-col min-h-screen gap-x-10">
      <div class="mb-10 rounded-md">
        <ul class="flex mb-0 list-none flex-wrap gap-y-3 flex-row rounded-md">
          <li v-for="tab in tabs" class="-mb-px mr-5 last:mr-0 flex-auto text-center rounded-lg shadow cursor-pointer border"
            :class="{'bg-primary text-white' : $page.url.includes(tab.key)}">
            <button :disabled="true" class="text-xs w-full font-bold uppercase px-5 py-3 rounded-lg block leading-normal hover:text-gray-900 no-underline duration-500">
              {{ tab.index }}. {{ tab.name }}
            </button>
          </li>
        </ul>
      </div>
      <div class="shadow flex flex-1 border rounded-xl">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>