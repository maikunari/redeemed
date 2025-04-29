<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SiteSettings from '@/Components/SiteSettings.vue';
import { Head } from '@inertiajs/vue3';
import StatisticPanel from '@/Components/StatisticPanel.vue';

defineProps({
    settings: {
        type: Object,
        default: () => ({
            site_name: '',
            logo_url: null
        })
    },
    stats: {
        type: Object,
        default: () => ({})
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="lg:w-1/2 w-full">
                        <SiteSettings :settings="settings" />
                    </div>
                    <div class="lg:w-1/2 w-full">
                        <StatisticPanel :stats="stats" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
