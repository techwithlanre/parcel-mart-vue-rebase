<script setup>
import {ref} from "vue";
import {DownOutlined} from "@ant-design/icons-vue";
import { UserOutlined, InfoCircleOutlined } from '@ant-design/icons-vue';

const activeKey = ref('1');

</script>

<template>
    <div class="p-4 flex gap-4">
        <div class="flex flex-col">
            <a-dropdown>
                <a class="ant-dropdown-link mb-1.5" @click.prevent>
                    Nigeria
                    <DownOutlined />
                </a>
                <template #overlay>
                    <a-menu>
                        <a-menu-item>
                            <a href="javascript:;">1st menu item</a>
                        </a-menu-item>
                        <a-menu-item>
                            <a href="javascript:;">2nd menu item</a>
                        </a-menu-item>
                        <a-menu-item>
                            <a href="javascript:;">3rd menu item</a>
                        </a-menu-item>
                    </a-menu>
                </template>
            </a-dropdown>
            <a-input placeholder="Enter address" auto-size class="bg-transparent" />
        </div>
        <div class="flex flex-col">
            <a-dropdown>
                <a class="ant-dropdown-link mb-1.5" @click.prevent>
                    United Kingdom
                    <DownOutlined />
                </a>
                <template #overlay>
                    <a-menu>
                        <a-menu-item>
                            <a href="javascript:;">1st menu item</a>
                        </a-menu-item>
                        <a-menu-item>
                            <a href="javascript:;">2nd menu item</a>
                        </a-menu-item>
                        <a-menu-item>
                            <a href="javascript:;">3rd menu item</a>
                        </a-menu-item>
                    </a-menu>
                </template>
            </a-dropdown>
            <a-input placeholder="Enter address" auto-size  />
        </div>
        <div class="flex flex-col mt-7">
            <a-select placeholder="Quantity" auto-size class="" ></a-select>
        </div>
        <div class="flex flex-col mt-7">
            <a-input v-model:value="userName" placeholder="Weight">
                <template #suffix>
                    kg
                </template>
            </a-input>
        </div>
    </div>
</template>

<style scoped>

</style>
