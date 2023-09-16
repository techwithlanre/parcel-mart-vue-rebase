<script setup>

</script>
<template>
    <div class="p-3 bg-[#FCF0D4] flex justify-between items-center" v-show="$page.props.auth.user.account_mode === 'test'">
        <span class="text-text-color text-sm">You are currently on “Test Mode”. To get access to “Live Mode”, you need to complete your onboarding process</span>
        <span class="text-text-color text-sm">
            <div class="border border-primary p-2 rounded-lg text-primary font-semibold">
                Go To Onboarding
            </div>
        </span>
    </div>
</template>
