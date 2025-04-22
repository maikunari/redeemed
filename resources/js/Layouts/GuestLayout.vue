<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const version = computed(() => usePage().props.version);
</script>

<template>
    <div class="min-h-screen relative">
        <slot />
        <div class="absolute bottom-4 right-4 text-gray-400 flex flex-col items-end">
            <div class="text-3xl font-black tracking-tight font-['Roboto'] italic drop-shadow-sm">redeem</div>
            <div class="text-sm">v{{ version }}</div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@1,900&display=swap');
</style>
