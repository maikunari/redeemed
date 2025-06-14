<template>
    <footer :class="[
        'py-4 px-8',
        isAuthenticated ? 'mt-auto' : 'absolute bottom-0 w-full'
    ]">
        <div class="flex flex-col items-end text-gray-400">
            <div class="text-base font-black tracking-tight font-['Roboto'] italic drop-shadow-sm opacity-60">redeem</div>
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
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@1,900&display=swap');
</style> 