<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SiteSettings from '@/Components/SiteSettings.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    settings: {
        type: Object,
        default: () => ({
            site_name: '',
            logo_url: null
        })
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <SiteSettings :settings="settings" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
