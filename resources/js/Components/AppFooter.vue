<template>
    <footer :class="[
        'py-4 px-8',
        isAuthenticated ? 'mt-auto' : 'absolute bottom-0 w-full'
    ]">
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
    },
    isAuthenticated: {
        type: Boolean,
        default: false
    }
});
</script>

<style>
</style> 