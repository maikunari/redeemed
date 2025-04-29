<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-indigo-50 rounded-lg p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 4.5h15m-15 4.5h15" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-indigo-600">{{ displayed.file_count }}</p>
                <p class="text-sm text-gray-600 mt-1">Files</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v6m0-6a4 4 0 100-8 4 4 0 000 8zm-6 4v2a2 2 0 002 2h8a2 2 0 002-2v-2" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-blue-600">{{ displayed.download_count }}</p>
                <p class="text-sm text-gray-600 mt-1">Downloads</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-green-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 9.75L7.5 18.75L3 14.25" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-green-600">{{ displayed.codes_total }}</p>
                <p class="text-sm text-gray-600 mt-1">Codes Total</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-yellow-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-yellow-600">{{ displayed.codes_redeemed }}</p>
                <p class="text-sm text-gray-600 mt-1">Codes Redeemed</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ file_count: 0, download_count: 0, codes_total: 0, codes_redeemed: 0 })
    }
});

const displayed = ref({ ...props.stats }); // will update after animation

function animateValue(key, target) {
    const duration = 800; // ms
    const start = 0;
    const startTime = performance.now();

    function step(now) {
        const progress = Math.min((now - startTime) / duration, 1);
        displayed.value[key] = Math.floor(progress * target);
        if (progress < 1) {
            requestAnimationFrame(step);
        }
    }
    requestAnimationFrame(step);
}

onMounted(() => {
    Object.entries(props.stats).forEach(([key, val]) => {
        animateValue(key, val);
    });
});
</script> 