<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const version = computed(() => usePage().props.version);
</script>

<template>
    <div class="min-h-screen relative">
        <slot />
        <div class="absolute bottom-4 right-4 text-sm text-gray-400">
            v{{ version }}
        </div>
    </div>
</template>
