<template>
    <footer class="mt-auto py-4 text-right text-sm text-gray-500 px-8">
        <span>redeem v{{ version }}</span>
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