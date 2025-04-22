<template>
    <footer class="absolute bottom-0 w-full py-4 px-8">
        <div class="flex flex-col items-end text-gray-400">
            <div class="text-3xl font-black tracking-tight font-['Roboto'] italic drop-shadow-sm">redeem</div>
            <a href="https://github.com/maikunari/redeemed" target="_blank" rel="noopener noreferrer" class="text-sm hover:text-gray-600 transition-colors">v{{ version }}</a>
        </div>
    </footer>
</template>

<script setup>
defineProps({
    version: {
        type: String,
        required: true
    }
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@1,900&display=swap');
</style> 