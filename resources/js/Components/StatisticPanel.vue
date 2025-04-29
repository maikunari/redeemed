<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
        <div class="grid grid-cols-2 gap-6 text-center">
            <div>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.file_count }}</p>
                <p class="text-sm text-gray-500">Files</p>
            </div>
            <div>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.download_count }}</p>
                <p class="text-sm text-gray-500">Downloads</p>
            </div>
            <div>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.codes_total }}</p>
                <p class="text-sm text-gray-500">Codes Total</p>
            </div>
            <div>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.codes_redeemed }}</p>
                <p class="text-sm text-gray-500">Codes Redeemed</p>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    stats: {
        type: Object,
        default: () => ({ file_count: 0, download_count: 0, codes_total: 0, codes_redeemed: 0 })
    }
});
</script> 