<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 4.5h15m-15 4.5h15" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-indigo-600">{{ displayed.file_count }}</p>
                <p class="text-sm text-gray-600 mt-1">Files</p>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 15.75v4.5A1.5 1.5 0 004.5 21h15a1.5 1.5 0 001.5-1.5v-4.5M7.5 9.75L12 14.25m0 0l4.5-4.5M12 14.25V3" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-blue-600">{{ displayed.download_count }}</p>
                <p class="text-sm text-gray-600 mt-1">Downloads</p>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-green-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3a4.5 4.5 0 11-3.76 7h-.74L4.5 17.75V21h3.25L14 14.5v-.74A4.5 4.5 0 0115.75 3z" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-green-600">{{ displayed.codes_total }}</p>
                <p class="text-sm text-gray-600 mt-1">Codes Total</p>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-yellow-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l1.5 1.5 3-3m6 .75a9 9 0 11-18 0 9 9 0 0118 0z" />
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