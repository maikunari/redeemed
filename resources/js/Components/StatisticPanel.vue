<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-purple-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-purple-600">{{ displayed.file_count }}</p>
                <p class="text-sm text-gray-600 mt-1">Files</p>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-purple-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 15.75v4.5A1.5 1.5 0 004.5 21h15a1.5 1.5 0 001.5-1.5v-4.5M7.5 9.75L12 14.25m0 0l4.5-4.5M12 14.25V3" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-purple-600">{{ displayed.download_count }}</p>
                <p class="text-sm text-gray-600 mt-1">Downloads</p>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-purple-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a7.464 7.464 0 01-1.15 3.993m1.989 3.559A11.209 11.209 0 008.25 10.5a3.75 3.75 0 117.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 01-3.6 9.75m6.633-4.596a18.666 18.666 0 01-2.485 5.33" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5v2.25m0-2.25a2.25 2.25 0 110-4.5 2.25 2.25 0 010 4.5z" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-purple-600">{{ displayed.codes_total }}</p>
                <p class="text-sm text-gray-600 mt-1">Codes Total</p>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-inner p-4 flex flex-col items-center text-center">
                <svg class="h-6 w-6 text-purple-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l1.5 1.5 3-3m6 .75a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-2xl font-semibold text-gray-900 inline-block pb-1 border-b-2 border-purple-600">{{ displayed.codes_redeemed }}</p>
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