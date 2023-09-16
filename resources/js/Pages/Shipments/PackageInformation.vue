<script setup>

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import ShipmentLayout from "@/Pages/Shipments/Layouts/ShipmentLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm, Link} from "@inertiajs/vue3";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {onMounted} from "vue";
import InputError from "@/Components/InputError.vue";
import {toast} from "vue3-toastify";


const props = defineProps({
  categories: Array,
  shipment_id: Number,
  item: Object,
});

const form = useForm({
  shipment_id: 0,
  category: '',
  value: '',
  description: '',
  quantity: '',
  weight: '',
  height: '',
  length: '',
  width: ''
});




const submit = () => {
  form.post(route('shipment.package-information.store', props.shipment_id), {
    onError: () => toast.error(form.errors.message)
  })
}

const setExistingValues = () => {
  form.shipment_id = props.shipment_id;
  form.category = props.item.item_category_id ?? '';
  form.value = props.item.value ?? '';
  form.description = props.item.description ?? '';
  form.quantity = props.item.quantity ?? '';
  form.weight = props.item.weight ?? '';
  form.height = props.item.height ?? '';
  form.length = props.item.length ?? '';
  form.width = props.item.width ?? '';
}

onMounted(() => {
  setExistingValues();
})

</script>

<template>
  <DashboardLayout page-title="Package Information">
    <ShipmentLayout>
      <div class="w-full">
        <div class="p-5">
          <h3>Package Information</h3>
        </div>
        <hr>
        <form action="" @submit.prevent="submit">
          <div class="flex flex-col gap-y-4 p-5">
            <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
              <div class="w-full">
                <InputLabel value="Item category" class="font-normal"/>
                <SelectInput  v-model="form.category" :options="categories"
                             place-holder="" :class="{'border-orange-300': form.errors.category}" class="mt-2 w-full"/>
                <InputError class="mt-2" :message="form.errors.category" />
              </div>
              <div class="w-full">
                <InputLabel value="Item value (NGN)" class="font-normal"/>
                <TextInput  type="number" v-model="form.value"  min="1"
                           placeholder="" :class="{'border-orange-300': form.errors.value}" class="mt-2"/>
                <InputError class="mt-2" :message="form.errors.value" />
              </div>
            </div>
            <div>
              <InputLabel value="Item Description" class="font-normal"/>
              <TextAreaInput v-model="form.description" rows="7" :class="{'border-orange-300': form.errors.description}" class="mt-2"
                 placeholder="A piece of text that clearly describes the item being packaged for shipping, it should leave no room for guesses to that Parcels Mart can know how best to handle it."/>
              <InputError class="mt-2" :message="form.errors.description" />
            </div>
            <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
              <div class="w-full">
                <InputLabel value="Quantity" class="font-normal"/>
                <TextInput  type="number" v-model="form.quantity" min="1" placeholder=""
                            :class="{'border-orange-300': form.errors.quantity}" class="mt-2"/>
                <InputError class="mt-2" :message="form.errors.quantity" />
              </div>
              <div class="w-full">
                <InputLabel value="Weight (KG)" class="font-normal"/>
                <TextInput  type="number" v-model="form.weight" min="1" placeholder=""
                            :class="{'border-orange-300': form.errors.weight}" class="mt-2"/>
                <InputError class="mt-2" :message="form.errors.weight" />
              </div>
            </div>
            <div class="flex lg:flex-row flex-col w-full gap-x-10 gap-y-4">
              <div class="w-full">
                <InputLabel value="Length (CM)" class="font-normal"/>
                <TextInput  type="number" v-model="form.length" min="1" placeholder=""
                            :class="{'border-orange-300': form.errors.length}" class="mt-2"/>
                <InputError class="mt-2" :message="form.errors.length" />
              </div>
              <div class="w-full">
                <InputLabel value="Width (CM)" class="font-normal"/>
                <TextInput  type="number" v-model="form.width" min="1" placeholder=""
                            :class="{'border-orange-300': form.errors.width}" class="mt-2"/>
                <InputError class="mt-2" :message="form.errors.width" />
              </div>
              <div class="w-full">
                <InputLabel value="Height (CM)" class="font-normal"/>
                <TextInput  type="number" v-model="form.height" min="1" placeholder=""
                            :class="{'border-orange-300': form.errors.height}" class="mt-2"/>
                <InputError class="mt-2" :message="form.errors.height" />
              </div>
            </div>
            <div class="flex lg:flex-row flex-col items-center w-full gap-x-10 gap-y-4 mt-10">
              <Link :href="route('shipment.destination', shipment_id)" class="w-full">
                <SecondaryButton class="w-full">
                  Back
                </SecondaryButton>
              </Link>
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Next</PrimaryButton>
            </div>
          </div>
        </form>
      </div>
    </ShipmentLayout>
  </DashboardLayout>
</template>

<style scoped>

</style>