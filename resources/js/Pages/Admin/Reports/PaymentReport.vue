<script setup>

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import ReportsLayout from "@/Layouts/ReportsLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
</script>

<template>
  <DashboardLayout page-title="Payments Report">
    <ReportsLayout>
      <div class="">
        <div class="grid sm:grid-cols-4 grid-cols-1 gap-x-10">
          <div>
            <InputLabel value="Date From" />
            <TextInput model-value="" type="date" class="mt-2" />
          </div>
          <div>
            <InputLabel value="Date To" />
            <TextInput model-value="" type="date" class="mt-2" />
          </div>
        </div>
      </div>
    </ReportsLayout>
  </DashboardLayout>
</template>

<style scoped>

</style>