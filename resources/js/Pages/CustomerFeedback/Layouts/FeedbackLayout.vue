<script setup>


const tabs = [
  {name: 'Origin form', index: 1, content: "", key: 'origin', disabled: false},
  {name: 'Destination Address', index: 2, content: "", key: 'destination', disabled: true},
  {name: 'Package Information', index: 3, content: "", key: 'package-information', disabled: true}
];
</script>

<template>
  <div class="sm:w-3/5 mx-auto">
    <div class="flex flex-col min-h-screeqn gap-x-10">
   
      <div class="shadow-md flex flex-1 border rounded-xl">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>