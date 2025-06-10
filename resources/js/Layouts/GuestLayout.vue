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
        <AppFooter :version="version" :isAuthenticated="false" />
    </div>
</template>

<style>
</style>
