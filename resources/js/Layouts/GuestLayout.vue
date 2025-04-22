<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import AppFooter from '@/Components/AppFooter.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const version = computed(() => usePage().props.version);
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#000000]">
        <slot />
        <AppFooter :version="version" />
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@1,900&display=swap');
</style>
